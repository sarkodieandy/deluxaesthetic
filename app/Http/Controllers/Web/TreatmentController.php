<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Treatment;
use App\Models\TreatmentCategory;
use App\Models\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TreatmentController extends Controller
{
    public function index(Request $request): View
    {
        $categories = TreatmentCategory::query()
            ->where('is_active', true)
            ->withCount(['treatments as published_treatments_count' => fn ($query) => $query->where('is_active', true)])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $query = Treatment::query()
            ->with('category')
            ->where('is_active', true)
            ->whereHas('category', fn ($category) => $category->where('is_active', true));

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->string('category')));
        }

        if ($request->filled('q')) {
            $term = '%'.$request->string('q').'%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('short_description', 'like', $term);
            });
        }

        $sort = $request->string('sort')->toString();
        match ($sort) {
            'price_asc' => $query->orderByRaw('COALESCE(promotional_price, price) asc'),
            'price_desc' => $query->orderByRaw('COALESCE(promotional_price, price) desc'),
            'name' => $query->orderBy('name'),
            default => $query
                ->orderBy(TreatmentCategory::query()
                    ->select('sort_order')
                    ->whereColumn('treatment_categories.id', 'treatments.treatment_category_id'))
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->orderBy('name'),
        };

        return view('web.treatments.index', [
            'treatments' => $query->paginate(9)->withQueryString(),
            'categories' => $categories,
            'selectedCategory' => $categories->firstWhere('slug', $request->string('category')->toString()),
            'beforeAfter' => GalleryItem::query()
                ->with('treatment:id,name,slug,treatment_category_id')
                ->where('type', 'before_after')
                ->where('is_active', true)
                ->where(function ($gallery) {
                    $gallery->whereNull('treatment_id')
                        ->orWhereHas('treatment', fn ($treatment) => $treatment
                            ->where('is_active', true)
                            ->whereHas('category', fn ($category) => $category->where('is_active', true)));
                })
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->get()
                ->filter->hasBeforeAfterPair()
                ->take(6),
            'filters' => [
                'q' => $request->string('q')->toString(),
                'category' => $request->string('category')->toString(),
                'sort' => $sort,
            ],
        ]);
    }

    public function show(string $slug): View
    {
        $treatment = Treatment::query()
            ->with([
                'category',
                'practitioners' => fn ($practitioners) => $practitioners
                    ->where('practitioner_profiles.is_active', true)
                    ->whereHas('user', fn ($user) => $user->where('is_active', true)),
                'practitioners.user',
            ])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->whereHas('category', fn ($category) => $category->where('is_active', true))
            ->firstOrFail();

        $related = Treatment::query()
            ->where('is_active', true)
            ->where('treatment_category_id', $treatment->treatment_category_id)
            ->where('id', '!=', $treatment->id)
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        $beforeAfter = GalleryItem::query()
            ->where('treatment_id', $treatment->id)
            ->where('type', 'before_after')
            ->where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->get()
            ->filter->hasBeforeAfterPair();

        return view('web.treatments.show', [
            'treatment' => $treatment,
            'related' => $related,
            'beforeAfter' => $beforeAfter,
        ]);
    }
}
