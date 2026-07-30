<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class WebsitePerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_browsing_a_public_page_does_not_create_an_empty_cart(): void
    {
        Cache::flush();

        $this->get('/')
            ->assertOk()
            ->assertSee('spa-treatment-room.webp')
            ->assertSee('rel="preload"', false)
            ->assertDontSee('fonts.googleapis.com');

        $this->assertDatabaseCount('carts', 0);
    }
}
