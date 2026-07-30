<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Models\PractitionerProfile;
use App\Models\Product;
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
            ->where('is_featured', true)
            ->latest()
            ->take(3)
            ->get();

        $practitioners = PractitionerProfile::query()
            ->with('user')
            ->where('is_active', true)
            ->orderByDesc('is_ceo')
            ->take(3)
            ->get();

        $serviceIndex = [
            [
                'title' => __('web.home.service_facial'),
                'copy' => __('web.home.service_facial_copy'),
                'href' => route('web.treatments.index', ['category' => 'facial-treatments']),
            ],
            [
                'title' => __('web.home.service_skin'),
                'copy' => __('web.home.service_skin_copy'),
                'href' => route('web.treatments.index'),
            ],
            [
                'title' => __('web.home.service_body'),
                'copy' => __('web.home.service_body_copy'),
                'href' => route('web.treatments.index', ['category' => 'body-treatments']),
            ],
            [
                'title' => __('web.home.service_injectables'),
                'copy' => __('web.home.service_injectables_copy'),
                'href' => route('web.booking.create'),
            ],
            [
                'title' => __('web.home.service_wellness'),
                'copy' => __('web.home.service_wellness_copy'),
                'href' => route('web.treatments.index'),
            ],
            [
                'title' => __('web.home.service_training'),
                'copy' => __('web.home.service_training_copy'),
                'href' => route('web.academy.index'),
            ],
        ];

        $featuredProducts = Product::query()
            ->with(['category', 'images'])
            ->where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->take(2)
            ->get();

        $featuredBeforeAfter = GalleryItem::query()
            ->where('is_active', true)
            ->where('type', 'before_after')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->first();

        $featuredGalleryImage = GalleryItem::query()
            ->where('is_active', true)
            ->where('type', 'gallery')
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

        return view('web.home.index', [
            'ceo' => $ceo,
            'featuredTreatments' => $featuredTreatments,
            'practitioners' => $practitioners,
            'serviceIndex' => $serviceIndex,
            'featuredProducts' => $featuredProducts,
            'featuredBeforeAfter' => $featuredBeforeAfter,
            'featuredGalleryImage' => $featuredGalleryImage,
            'ourWorkGallery' => $ourWorkGallery,
            'heroSlides' => [
                [
                    'src' => 'assets/web/images/hero/spa-treatment-room.webp',
                    'alt' => 'Calm spa treatment room at '.config('clinic.name'),
                    'label' => 'Spa & clinic',
                ],
                [
                    'src' => 'assets/web/images/hero/hero-botox.webp',
                    'alt' => 'Professional Botox and injectable aesthetic treatment',
                    'label' => 'Botox',
                ],
                [
                    'src' => 'assets/web/images/hero/hero-nail-tech.webp',
                    'alt' => 'Professional nail technician manicure and gel finishing',
                    'label' => 'Nail tech',
                ],
                [
                    'src' => 'assets/web/images/hero/hero-facial-tech.webp',
                    'alt' => 'Advanced facial and skin treatment technology',
                    'label' => 'Skin tech',
                ],
                [
                    'src' => 'assets/web/images/hero/hero-spa-massage.webp',
                    'alt' => 'Restorative spa and body massage therapy',
                    'label' => 'Massage',
                ],
                [
                    'src' => 'assets/web/images/hero/hero-beauty-academy.webp',
                    'alt' => 'Professional aesthetic training at '.__('web.pages.academy_title'),
                    'label' => 'Academy',
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
