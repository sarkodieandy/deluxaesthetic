<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Course;
use App\Models\Product;
use App\Models\Treatment;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = collect([
            ['loc' => route('web.home'), 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['loc' => route('web.about'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => route('web.treatments.index'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => route('web.practitioners.index'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('web.academy.index'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => route('web.courses.index'), 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => route('web.store.index'), 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => route('web.gallery'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('web.blog.index'), 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => route('web.contact'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('web.booking.create'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => route('web.enrol'), 'changefreq' => 'monthly', 'priority' => '0.8'],
        ]);

        Treatment::query()
            ->where('is_active', true)
            ->select(['slug', 'updated_at'])
            ->each(fn (Treatment $treatment) => $urls->push([
                'loc' => route('web.treatments.show', $treatment->slug),
                'lastmod' => $treatment->updated_at?->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ]));

        Course::query()
            ->where('is_active', true)
            ->select(['slug', 'updated_at'])
            ->each(fn (Course $course) => $urls->push([
                'loc' => route('web.courses.show', $course->slug),
                'lastmod' => $course->updated_at?->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ]));

        Product::query()
            ->where('is_active', true)
            ->select(['slug', 'updated_at'])
            ->each(fn (Product $product) => $urls->push([
                'loc' => route('web.store.show', $product->slug),
                'lastmod' => $product->updated_at?->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ]));

        BlogPost::query()->published()
            ->select(['slug', 'updated_at'])
            ->each(fn (BlogPost $post) => $urls->push([
                'loc' => route('web.blog.show', $post),
                'lastmod' => $post->updated_at?->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ]));

        return response()
            ->view('web.seo.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
