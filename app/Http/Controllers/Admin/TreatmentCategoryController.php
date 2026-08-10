<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Clinic\StoreTreatmentCategoryRequest;
use App\Http\Requests\Admin\Clinic\UpdateTreatmentCategoryRequest;
use App\Models\TreatmentCategory;
use App\Support\GalleryMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TreatmentCategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.treatment-categories.index', [
            'categories' => TreatmentCategory::query()
                ->withCount('treatments')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.treatment-categories.create');
    }

    public function store(StoreTreatmentCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        TreatmentCategory::create([
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name']),
            'description' => $data['description'] ?? null,
            'image_path' => $this->resolveNewImage($request->file('image'), $data['image_url'] ?? null),
            'sort_order' => (int) $data['sort_order'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.treatment-categories.index')
            ->with('status', 'Procedure category added successfully.');
    }

    public function edit(TreatmentCategory $treatmentCategory): View
    {
        return view('admin.treatment-categories.edit', ['category' => $treatmentCategory]);
    }

    public function update(
        UpdateTreatmentCategoryRequest $request,
        TreatmentCategory $treatmentCategory,
    ): RedirectResponse {
        $data = $request->validated();
        $nextImage = $this->resolveUpdatedImage($request, $treatmentCategory->image_path);

        $previousImage = $treatmentCategory->image_path;

        $treatmentCategory->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'image_path' => $nextImage,
            'sort_order' => (int) $data['sort_order'],
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($nextImage !== $previousImage) {
            $this->deleteImage($previousImage);
        }

        return redirect()->route('admin.treatment-categories.index')
            ->with('status', 'Procedure category updated successfully.');
    }

    public function destroy(TreatmentCategory $treatmentCategory): RedirectResponse
    {
        if ($treatmentCategory->treatments()->exists()) {
            return back()->withErrors([
                'category' => 'Move or remove the procedures in this category before deleting it.',
            ]);
        }

        $this->deleteImage($treatmentCategory->image_path);
        $treatmentCategory->delete();

        return redirect()->route('admin.treatment-categories.index')
            ->with('status', 'Procedure category removed.');
    }

    private function resolveNewImage(?UploadedFile $image, ?string $url): ?string
    {
        return GalleryMedia::resolvePath(
            $image,
            $url,
            null,
            fn (UploadedFile $upload) => $upload->store('treatment-categories', 'public'),
        );
    }

    private function resolveUpdatedImage(UpdateTreatmentCategoryRequest $request, ?string $existing): ?string
    {
        if ($request->boolean('remove_image') && ! $request->hasFile('image') && ! GalleryMedia::normalizeUrl($request->input('image_url'))) {
            return null;
        }

        return GalleryMedia::resolvePath(
            $request->file('image'),
            $request->input('image_url'),
            $existing,
            fn (UploadedFile $upload) => $upload->store('treatment-categories', 'public'),
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
        $base = Str::slug($name) ?: 'procedure-category';
        $slug = $base;
        $suffix = 2;

        while (TreatmentCategory::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
