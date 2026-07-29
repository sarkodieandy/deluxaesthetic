<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        return view('web.gallery.index', [
            'galleryItems' => GalleryItem::query()
                ->where('is_active', true)
                ->where('type', 'gallery')
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->get(),
            'beforeAfterItems' => GalleryItem::query()
                ->where('is_active', true)
                ->where('type', 'before_after')
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->get(),
        ]);
    }
}
