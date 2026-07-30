<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_exposes_search_and_social_metadata(): void
    {
        $response = $this->get('/');
        $baseUrl = url('/');

        $response
            ->assertOk()
            ->assertSee('<meta name="description"', false)
            ->assertSee('<meta name="robots" content="index, follow, max-image-preview:large', false)
            ->assertSee('<link rel="canonical" href="'.$baseUrl.'">', false)
            ->assertSee('<meta property="og:title"', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
            ->assertSee('Aesthetics, Academy &amp; Beauty', false)
            ->assertDontSee('Academy &amp;amp; Beauty', false)
            ->assertSee('"@type":["MedicalBusiness","DaySpa"]', false);
    }

    public function test_filtered_store_pages_are_not_indexed_and_use_the_clean_canonical(): void
    {
        $response = $this->get('/store?q=cream&sort=price_asc');

        $response
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, follow">', false)
            ->assertSee('<link rel="canonical" href="'.url('/store').'">', false);
    }

    public function test_authentication_pages_are_not_indexed(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    public function test_sitemap_is_public_xml_and_contains_core_pages(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', false)
            ->assertSee('<loc>'.url('/').'</loc>', false)
            ->assertSee('<loc>'.url('/academy').'</loc>', false)
            ->assertDontSee('/admin', false)
            ->assertDontSee('/student', false);
    }
}
