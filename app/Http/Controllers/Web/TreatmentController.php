<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Treatment;
use App\Models\TreatmentCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TreatmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Treatment::query()
            ->with(['category', 'practitioners.user'])
            ->where('is_active', true);

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
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            default => $query->orderByDesc('is_featured')->orderBy('name'),
        };

        return view('web.treatments.index', [
            'treatments' => $query->paginate(9)->withQueryString(),
            'categories' => TreatmentCategory::query()->where('is_active', true)->orderBy('sort_order')->get(),
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
            ->with(['category', 'practitioners.user'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $related = Treatment::query()
            ->where('is_active', true)
            ->where('treatment_category_id', $treatment->treatment_category_id)
            ->where('id', '!=', $treatment->id)
            ->take(3)
            ->get();

        return view('web.treatments.show', [
            'treatment' => $treatment,
            'related' => $related,
        ]);
    }
}
