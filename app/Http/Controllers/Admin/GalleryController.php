<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\StoreGalleryItemRequest;
use App\Http\Requests\Admin\Content\UpdateGalleryItemRequest;
use App\Models\GalleryItem;
use App\Models\Treatment;
use App\Support\GalleryMedia;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(Request $request): View
    {
        $collectionFilter = $request->string('collection')->toString();
        if (! array_key_exists($collectionFilter, GalleryItem::LOCATION_GROUPS)) {
            $collectionFilter = '';
        }

        $typeFilter = $request->string('type')->toString();
        if (! in_array($typeFilter, ['gallery', 'before_after'], true)) {
            $typeFilter = '';
        }

        $items = GalleryItem::query()
            ->with('treatment:id,name')
            ->when($collectionFilter, fn ($query) => $query->where('location_group', $collectionFilter))
            ->when($typeFilter, fn ($query) => $query->where('type', $typeFilter))
            ->orderBy('location_group')
            ->orderBy('type')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->paginate(16)
            ->withQueryString();

        $rawCollectionStats = GalleryItem::query()
            ->selectRaw("location_group, COUNT(*) as total, SUM(CASE WHEN type = 'gallery' THEN 1 ELSE 0 END) as photos, SUM(CASE WHEN type = 'before_after' THEN 1 ELSE 0 END) as results, SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active")
            ->groupBy('location_group')
            ->get()
            ->keyBy(fn (GalleryItem $item) => $item->location_group ?: GalleryItem::DEFAULT_LOCATION_GROUP);

        $collectionStats = collect(GalleryItem::LOCATION_GROUPS)
            ->map(function (string $label, string $key) use ($rawCollectionStats): array {
                $stats = $rawCollectionStats->get($key);

                return [
                    'key' => $key,
                    'label' => $label,
                    'description' => GalleryItem::LOCATION_GROUP_DESCRIPTIONS[$key],
                    'total' => (int) ($stats?->total ?? 0),
                    'photos' => (int) ($stats?->photos ?? 0),
                    'results' => (int) ($stats?->results ?? 0),
                    'active' => (int) ($stats?->active ?? 0),
                ];
            })
            ->values();

        return view('admin.gallery.index', [
            'items' => $items,
            'locationGroups' => GalleryItem::LOCATION_GROUPS,
            'collectionStats' => $collectionStats,
            'collectionFilter' => $collectionFilter,
            'typeFilter' => $typeFilter,
        ]);
    }

    public function create(Request $request): View
    {
        $type = $request->query('type');
        if (! in_array($type, ['gallery', 'before_after'], true)) {
            $type = null;
        }

        $locationGroup = $request->string('collection')->toString();
        if (! array_key_exists($locationGroup, GalleryItem::LOCATION_GROUPS)) {
            $locationGroup = GalleryItem::DEFAULT_LOCATION_GROUP;
        }

        return view('admin.gallery.create', [
            'defaultType' => $type,
            'defaultLocationGroup' => $locationGroup,
            'treatments' => $this->treatments(),
        ]);
    }

    public function store(StoreGalleryItemRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $type = $data['type'];

        GalleryItem::create([
            'treatment_id' => $data['treatment_id'] ?? null,
            'title' => $data['title'],
            'slug' => $this->uniqueSlug($data['title']),
            'type' => $type,
            'location_group' => $data['location_group'] ?? GalleryItem::DEFAULT_LOCATION_GROUP,
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
            'is_active' => $request->boolean('is_active'),
            'sort_order' => (int) ($data['sort_order'] ?? 10),
        ]);

        return redirect()->route('admin.gallery.index')->with('status', 'Gallery item added successfully.');
    }

    public function edit(GalleryItem $gallery): View
    {
        return view('admin.gallery.edit', [
            'item' => $gallery,
            'treatments' => $this->treatments($gallery),
        ]);
    }

    public function update(UpdateGalleryItemRequest $request, GalleryItem $gallery): RedirectResponse
    {
        $data = $request->validated();
        $type = $data['type'];

        $payload = [
            'treatment_id' => $data['treatment_id'] ?? null,
            'title' => $data['title'],
            'type' => $type,
            'location_group' => $data['location_group'] ?? $gallery->location_group ?? GalleryItem::DEFAULT_LOCATION_GROUP,
            'description' => $data['description'] ?? null,
            'alt_text' => $data['alt_text'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
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
                $payload['image_path'] = null;
            }
        }

        $previousPaths = array_filter([
            $gallery->image_path,
            $gallery->before_image_path,
            $gallery->after_image_path,
        ]);
        $nextPaths = array_filter([
            array_key_exists('image_path', $payload) ? $payload['image_path'] : $gallery->image_path,
            array_key_exists('before_image_path', $payload) ? $payload['before_image_path'] : $gallery->before_image_path,
            array_key_exists('after_image_path', $payload) ? $payload['after_image_path'] : $gallery->after_image_path,
        ]);

        DB::transaction(function () use ($gallery, $payload, $previousPaths, $nextPaths): void {
            $gallery->update($payload);

            foreach (array_diff($previousPaths, $nextPaths) as $obsoletePath) {
                DB::afterCommit(fn () => $this->deletePath($obsoletePath));
            }
        });

        return redirect()->route('admin.gallery.index')->with('status', 'Gallery item updated successfully.');
    }

    public function destroy(GalleryItem $gallery): RedirectResponse
    {
        DB::transaction(function () use ($gallery): void {
            $paths = [$gallery->image_path, $gallery->before_image_path, $gallery->after_image_path];
            $gallery->delete();
            DB::afterCommit(function () use ($paths): void {
                foreach ($paths as $path) {
                    $this->deletePath($path);
                }
            });
        });

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
            $payload[$column] = $next;
        }
    }

    private function storeImage(UploadedFile $file, string $dir): string
    {
        $path = $file->store($dir, 'public');

        if (! is_string($path) || $path === '') {
            throw new \RuntimeException('The gallery image could not be stored.');
        }

        return $path;
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

    /** @return Collection<int, Treatment> */
    private function treatments(?GalleryItem $item = null)
    {
        return Treatment::query()
            ->where(function ($query) use ($item) {
                $query->where('is_active', true);
                if ($item?->treatment_id) {
                    $query->orWhereKey($item->treatment_id);
                }
            })
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
