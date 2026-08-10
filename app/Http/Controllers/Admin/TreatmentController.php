<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Products\StoreTreatmentRequest;
use App\Http\Requests\Admin\Products\UpdateTreatmentRequest;
use App\Models\Treatment;
use App\Models\TreatmentCategory;
use App\Models\PractitionerProfile;
use App\Support\GalleryMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TreatmentController extends Controller
{
    public function index(): View
    {
        $treatments = Treatment::query()
            ->with('category')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.treatments.index', [
            'treatments' => $treatments,
            'categoryCount' => TreatmentCategory::query()->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.treatments.create', $this->formOptions());
    }

    public function store(StoreTreatmentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        DB::transaction(function () use ($request, $data) {
            $treatment = Treatment::create($this->payload($request, $data) + [
                'slug' => $this->uniqueSlug($data['name']),
                'image_path' => $this->resolveNewImage($request->file('image'), $data['image_url'] ?? null),
            ]);

            $treatment->practitioners()->sync($data['practitioner_profile_ids'] ?? []);
        });

        return redirect()->route('admin.treatments.index')->with('status', 'Treatment added successfully.');
    }

    public function edit(Treatment $treatment): View
    {
        $treatment->load(['category', 'practitioners']);

        return view('admin.treatments.edit', ['treatment' => $treatment] + $this->formOptions($treatment));
    }

    public function update(UpdateTreatmentRequest $request, Treatment $treatment): RedirectResponse
    {
        $data = $request->validated();
        DB::transaction(function () use ($request, $data, $treatment) {
            $nextImage = $this->resolveUpdatedImage($request, $treatment->image_path);
            $previousImage = $treatment->image_path;

            $treatment->update($this->payload($request, $data) + ['image_path' => $nextImage]);
            $treatment->practitioners()->sync($data['practitioner_profile_ids'] ?? []);

            if ($nextImage !== $previousImage) {
                DB::afterCommit(fn () => $this->deleteImage($previousImage));
            }
        });

        return redirect()->route('admin.treatments.index')->with('status', 'Treatment updated successfully.');
    }

    public function destroy(Treatment $treatment): RedirectResponse
    {
        if ($treatment->appointments()->withTrashed()->exists()) {
            return back()->withErrors([
                'treatment' => 'This procedure has appointment history and cannot be deleted. Unpublish it instead to preserve client records.',
            ]);
        }

        DB::transaction(function () use ($treatment): void {
            $imagePath = $treatment->image_path;
            $treatment->update(['is_active' => false]);
            $treatment->delete();
            DB::afterCommit(fn () => $this->deleteImage($imagePath));
        });

        return redirect()->route('admin.treatments.index')->with('status', 'Treatment removed.');
    }

    /** @return array<string, mixed> */
    private function payload(StoreTreatmentRequest $request, array $data): array
    {
        return [
            'treatment_category_id' => (int) $data['treatment_category_id'],
            'name' => $data['name'],
            'short_description' => $data['short_description'],
            'description' => $data['description'] ?? null,
            'duration_minutes' => (int) $data['duration_minutes'],
            'recovery_days' => (int) ($data['recovery_days'] ?? 0),
            'price' => $data['price'],
            'promotional_price' => $data['promotional_price'] ?? null,
            'deposit_amount' => $data['deposit_amount'] ?? null,
            'recommended_sessions' => (int) ($data['recommended_sessions'] ?? 1),
            'buffer_before_minutes' => (int) ($data['buffer_before_minutes'] ?? 0),
            'buffer_after_minutes' => (int) ($data['buffer_after_minutes'] ?? 15),
            'sort_order' => (int) $data['sort_order'],
            'benefits' => $this->lines($data['benefits'] ?? null),
            'suitable_candidates' => $data['suitable_candidates'] ?? null,
            'contraindications' => $data['contraindications'] ?? null,
            'preparation_instructions' => $data['preparation_instructions'] ?? null,
            'aftercare_instructions' => $data['aftercare_instructions'] ?? null,
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    /** @return array<int, string> */
    private function lines(?string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', trim((string) $value)) ?: [])
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    /** @return array{categories: \Illuminate\Database\Eloquent\Collection, practitioners: \Illuminate\Database\Eloquent\Collection} */
    private function formOptions(?Treatment $treatment = null): array
    {
        $categories = TreatmentCategory::query()
            ->where(function ($query) use ($treatment) {
                $query->where('is_active', true);
                if ($treatment) {
                    $query->orWhereKey($treatment->treatment_category_id);
                }
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return [
            'categories' => $categories,
            'practitioners' => PractitionerProfile::query()
                ->with('user:id,name')
                ->where('is_active', true)
                ->whereHas('user', fn ($user) => $user->where('is_active', true))
                ->orderBy('sort_order')
                ->get(),
        ];
    }

    private function resolveNewImage(?UploadedFile $image, ?string $url): ?string
    {
        return GalleryMedia::resolvePath(
            $image,
            $url,
            null,
            fn (UploadedFile $upload) => $upload->store('treatments', 'public'),
        );
    }

    private function resolveUpdatedImage(StoreTreatmentRequest $request, ?string $existing): ?string
    {
        if ($request->boolean('remove_image') && ! $request->hasFile('image') && ! GalleryMedia::normalizeUrl($request->input('image_url'))) {
            return null;
        }

        return GalleryMedia::resolvePath(
            $request->file('image'),
            $request->input('image_url'),
            $existing,
            fn (UploadedFile $upload) => $upload->store('treatments', 'public'),
        );
    }

    private function deleteImage(?string $path): void
    {
        if (! GalleryMedia::isLocalStoredPath($path) || str_starts_with((string) $path, 'assets/')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'treatment';
        $slug = $base;
        $i = 2;

        while (Treatment::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
