<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(Request $request): View
    {
        $selectedCollection = $request->string('collection')->toString();
        if ($selectedCollection !== 'all' && ! array_key_exists($selectedCollection, GalleryItem::LOCATION_GROUPS)) {
            $selectedCollection = 'all';
        }

        if ($selectedCollection === '') {
            $selectedCollection = 'all';
        }

        $galleryQuery = GalleryItem::query()
            ->where('is_active', true)
            ->where('type', 'gallery');

        $collectionCounts = (clone $galleryQuery)
            ->selectRaw('location_group, COUNT(*) as aggregate')
            ->groupBy('location_group')
            ->pluck('aggregate', 'location_group');

        $galleryShowcaseItems = (clone $galleryQuery)
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->get();

        $galleryItems = $selectedCollection === 'all'
            ? $galleryShowcaseItems
            : $galleryShowcaseItems
                ->where('location_group', $selectedCollection)
                ->values();

        $galleryCollections = collect(GalleryItem::LOCATION_GROUPS)
            ->map(fn (string $label, string $key) => [
                'key' => $key,
                'label' => $label,
                'items' => $galleryItems->filter(
                    fn (GalleryItem $item) => ($item->location_group ?: GalleryItem::DEFAULT_LOCATION_GROUP) === $key
                )->values(),
            ])
            ->filter(fn (array $collection) => $collection['items']->isNotEmpty());

        return view('web.gallery.index', [
            'galleryCollections' => $galleryCollections,
            'galleryShowcaseItems' => $galleryShowcaseItems,
            'locationGroups' => GalleryItem::LOCATION_GROUPS,
            'collectionCounts' => $collectionCounts,
            'selectedCollection' => $selectedCollection,
            'beforeAfterItems' => GalleryItem::query()
                ->where('is_active', true)
                ->where('type', 'before_after')
                ->when($selectedCollection !== 'all', fn ($query) => $query->where('location_group', $selectedCollection))
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->get(),
        ]);
    }
}
