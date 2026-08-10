<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AcademyShowcaseItem;
use App\Models\Course;
use App\Models\GalleryItem;
use App\Models\PractitionerProfile;
use App\Models\Setting;
use App\Models\Treatment;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $ceo = PractitionerProfile::query()
            ->with('user')
            ->where('is_ceo', true)
            ->where('is_active', true)
            ->first();

        $featuredTreatments = Treatment::query()
            ->with('category')
            ->where('is_active', true)
            ->whereHas('category', fn ($category) => $category->where('is_active', true))
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->take(3)
            ->get();

        $featuredBeforeAfter = GalleryItem::query()
            ->where('is_active', true)
            ->where('type', 'before_after')
            ->where(function ($gallery) {
                $gallery->whereNull('treatment_id')
                    ->orWhereHas('treatment', fn ($treatment) => $treatment
                        ->where('is_active', true)
                        ->whereHas('category', fn ($category) => $category->where('is_active', true)));
            })
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->first();

        $ourWorkGallery = GalleryItem::query()
            ->where('is_active', true)
            ->where('type', 'gallery')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        $trainingCountries = AcademyShowcaseItem::query()
            ->where('type', 'training_country')
            ->where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->get();

        return view('web.home.index', [
            'ceo' => $ceo,
            'featuredTreatments' => $featuredTreatments,
            'featuredBeforeAfter' => $featuredBeforeAfter,
            'ourWorkGallery' => $ourWorkGallery,
            'trainingCountries' => $trainingCountries,
            'academyPathwayCount' => Course::query()
                ->where('is_active', true)
                ->where('is_featured', true)
                ->whereHas('category', fn ($category) => $category->where('is_active', true))
                ->count(),
            'heroSlides' => [
                [
                    'src' => 'assets/web/images/hero/hero-botox.webp',
                    'alt' => 'Expert injectable treatment — Botox and dermal fillers at '.config('clinic.name'),
                    'label' => 'Injectables',
                ],
                [
                    'src' => 'assets/web/images/hero/hero-beauty-academy.webp',
                    'alt' => 'Professional aesthetics academy training at '.config('clinic.name'),
                    'label' => 'Academy',
                ],
                [
                    'src' => 'assets/web/images/treatments/skincare-ritual.webp',
                    'alt' => 'Curated premium skincare and professional beauty products at '.config('clinic.name'),
                    'label' => 'Products',
                ],
            ],
            'announcement' => Setting::getValue(
                app()->getLocale() === 'fr' ? 'bar_text_fr' : 'bar_text_en',
                __('web.announcement')
            ),
            'clinic' => config('clinic'),
        ]);
    }
}
