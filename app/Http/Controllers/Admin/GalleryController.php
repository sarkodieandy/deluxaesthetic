<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\StoreGalleryItemRequest;
use App\Http\Requests\Admin\Content\UpdateGalleryItemRequest;
use App\Models\GalleryItem;
use App\Support\GalleryMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        $items = GalleryItem::query()->orderBy('type')->orderByDesc('is_featured')->orderBy('sort_order')->paginate(16);

        return view('admin.gallery.index', compact('items'));
    }

    public function create(Request $request): View
    {
        $type = $request->query('type');
        if (! in_array($type, ['gallery', 'before_after'], true)) {
            $type = null;
        }

        return view('admin.gallery.create', ['defaultType' => $type]);
    }

    public function store(StoreGalleryItemRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $type = $data['type'];

        GalleryItem::create([
            'title' => $data['title'],
            'slug' => $this->uniqueSlug($data['title']),
            'type' => $type,
            'description' => $data['description'] ?? null,
            'image_path' => $type === 'gallery'
                ? $this->resolveNewPath($request->file('image'), $request->input('image_url'), 'gallery')
                : null,
            'before_image_path' => $type === 'before_after'
                ? $this->resolveNewPath($request->file('before_image'), $request->input('before_image_url'), 'before-after')
                : null,
            'after_image_path' => $type === 'before_after'
                ? $this->resolveNewPath($request->file('after_image'), $request->input('after_image_url'), 'before-after')
                : null,
            'alt_text' => $data['alt_text'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => (int) ($data['sort_order'] ?? 10),
        ]);

        return redirect()->route('admin.gallery.index')->with('status', 'Gallery item added successfully.');
    }

    public function edit(GalleryItem $gallery): View
    {
        return view('admin.gallery.edit', ['item' => $gallery]);
    }

    public function update(UpdateGalleryItemRequest $request, GalleryItem $gallery): RedirectResponse
    {
        $data = $request->validated();
        $type = $data['type'];

        $payload = [
            'title' => $data['title'],
            'type' => $type,
            'description' => $data['description'] ?? null,
            'alt_text' => $data['alt_text'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => (int) ($data['sort_order'] ?? 10),
        ];

        if ($type === 'gallery') {
            $this->applyMediaUpdate(
                $gallery,
                'image_path',
                $request->file('image'),
                $request->input('image_url'),
                'gallery',
                $payload
            );
            if ($gallery->before_image_path || $gallery->after_image_path) {
                $this->deletePath($gallery->before_image_path);
                $this->deletePath($gallery->after_image_path);
                $payload['before_image_path'] = null;
                $payload['after_image_path'] = null;
            }
        }

        if ($type === 'before_after') {
            $this->applyMediaUpdate(
                $gallery,
                'before_image_path',
                $request->file('before_image'),
                $request->input('before_image_url'),
                'before-after',
                $payload
            );
            $this->applyMediaUpdate(
                $gallery,
                'after_image_path',
                $request->file('after_image'),
                $request->input('after_image_url'),
                'before-after',
                $payload
            );
            if ($gallery->image_path) {
                $this->deletePath($gallery->image_path);
                $payload['image_path'] = null;
            }
        }

        $gallery->update($payload);

        return redirect()->route('admin.gallery.index')->with('status', 'Gallery item updated successfully.');
    }

    public function destroy(GalleryItem $gallery): RedirectResponse
    {
        $this->deletePath($gallery->image_path);
        $this->deletePath($gallery->before_image_path);
        $this->deletePath($gallery->after_image_path);
        $gallery->delete();

        return redirect()->route('admin.gallery.index')->with('status', 'Gallery item removed.');
    }

    private function resolveNewPath(?UploadedFile $file, ?string $urlInput, string $dir): ?string
    {
        return GalleryMedia::resolvePath(
            $file,
            $urlInput,
            null,
            fn (UploadedFile $upload) => $this->storeImage($upload, $dir)
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function applyMediaUpdate(
        GalleryItem $item,
        string $column,
        ?UploadedFile $file,
        ?string $urlInput,
        string $dir,
        array &$payload
    ): void {
        $existing = $item->{$column};
        $next = GalleryMedia::resolvePath(
            $file,
            $urlInput,
            $existing,
            fn (UploadedFile $upload) => $this->storeImage($upload, $dir)
        );

        if ($next !== $existing) {
            $this->deletePath($existing);
            $payload[$column] = $next;
        }
    }

    private function storeImage(UploadedFile $file, string $dir): string
    {
        return $file->store($dir, 'public');
    }

    private function deletePath(?string $path): void
    {
        if (! GalleryMedia::isLocalStoredPath($path) || str_starts_with($path, 'assets/')) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'gallery-item';
        $slug = $base;
        $i = 2;

        while (GalleryItem::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
