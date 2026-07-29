<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Products\StoreTreatmentRequest;
use App\Http\Requests\Admin\Products\UpdateTreatmentRequest;
use App\Models\Treatment;
use App\Models\TreatmentCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TreatmentController extends Controller
{
    public function index(): View
    {
        $treatments = Treatment::query()->with('category')->orderByDesc('is_featured')->orderBy('name')->paginate(15);

        return view('admin.treatments.index', compact('treatments'));
    }

    public function create(): View
    {
        return view('admin.treatments.create');
    }

    public function store(StoreTreatmentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $category = $this->resolveCategory($data['category_name']);

        Treatment::create([
            'treatment_category_id' => $category->id,
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name']),
            'short_description' => $data['short_description'],
            'description' => $data['description'] ?? null,
            'duration_minutes' => (int) $data['duration_minutes'],
            'price' => $data['price'],
            'deposit_amount' => $data['deposit_amount'] ?? null,
            'recommended_sessions' => (int) ($data['recommended_sessions'] ?? 1),
            'image_path' => $request->file('image')?->store('treatments', 'public'),
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active', true),
            'benefits' => $data['benefits'] ? preg_split('/\r\n|\r|\n/', trim($data['benefits'])) : [],
            'preparation_instructions' => $data['preparation_instructions'] ?? null,
            'aftercare_instructions' => $data['aftercare_instructions'] ?? null,
        ]);

        return redirect()->route('admin.treatments.index')->with('status', 'Treatment added successfully.');
    }

    public function edit(Treatment $treatment): View
    {
        $treatment->load('category');

        return view('admin.treatments.edit', compact('treatment'));
    }

    public function update(UpdateTreatmentRequest $request, Treatment $treatment): RedirectResponse
    {
        $data = $request->validated();
        $category = $this->resolveCategory($data['category_name']);

        $payload = [
            'treatment_category_id' => $category->id,
            'name' => $data['name'],
            'short_description' => $data['short_description'],
            'description' => $data['description'] ?? null,
            'duration_minutes' => (int) $data['duration_minutes'],
            'price' => $data['price'],
            'deposit_amount' => $data['deposit_amount'] ?? null,
            'recommended_sessions' => (int) ($data['recommended_sessions'] ?? 1),
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active', true),
            'benefits' => $data['benefits'] ? preg_split('/\r\n|\r|\n/', trim($data['benefits'])) : [],
            'preparation_instructions' => $data['preparation_instructions'] ?? null,
            'aftercare_instructions' => $data['aftercare_instructions'] ?? null,
        ];

        if ($request->hasFile('image')) {
            if ($treatment->image_path && ! str_starts_with($treatment->image_path, 'assets/') && Storage::disk('public')->exists($treatment->image_path)) {
                Storage::disk('public')->delete($treatment->image_path);
            }
            $payload['image_path'] = $request->file('image')->store('treatments', 'public');
        }

        $treatment->update($payload);

        return redirect()->route('admin.treatments.index')->with('status', 'Treatment updated successfully.');
    }

    public function destroy(Treatment $treatment): RedirectResponse
    {
        if ($treatment->image_path && ! str_starts_with($treatment->image_path, 'assets/') && Storage::disk('public')->exists($treatment->image_path)) {
            Storage::disk('public')->delete($treatment->image_path);
        }

        $treatment->delete();

        return redirect()->route('admin.treatments.index')->with('status', 'Treatment removed.');
    }

    private function resolveCategory(string $name): TreatmentCategory
    {
        $slug = Str::slug($name) ?: 'treatments';

        return TreatmentCategory::query()->firstOrCreate(
            ['slug' => $slug],
            ['name' => $name, 'is_active' => true]
        );
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
