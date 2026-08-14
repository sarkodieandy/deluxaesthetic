<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_privacy_policy_is_complete_and_publicly_accessible(): void
    {
        $this->get(route('web.privacy'))
            ->assertOk()
            ->assertSee('Privacy Policy')
            ->assertSee('Data Protection Act, 2012 (Act 843)')
            ->assertSee('Paystack')
            ->assertSee('Google sign-in')
            ->assertSee('WhatsApp')
            ->assertSee('Your data-protection rights')
            ->assertSee('14 August 2026');
    }

    public function test_terms_cover_clinic_academy_products_payments_and_refunds(): void
    {
        $this->get(route('web.terms'))
            ->assertOk()
            ->assertSee('Terms of Service')
            ->assertSee('Clinical consultations and procedures')
            ->assertSee('Academy applications and physical training')
            ->assertSee('Products, availability and delivery')
            ->assertSee('Prices, payments, deposits and refunds')
            ->assertSee('Paystack')
            ->assertSee('laws of the Republic of Ghana');
    }

    public function test_legal_pages_are_linked_from_footer_consent_and_sitemap(): void
    {
        $this->get(route('web.home'))
            ->assertOk()
            ->assertSee(route('web.privacy'), false)
            ->assertSee(route('web.terms'), false);

        $this->get(route('web.academy.student-portal.create'))
            ->assertOk()
            ->assertSee(route('web.privacy'), false);

        $this->get(route('seo.sitemap'))
            ->assertOk()
            ->assertSee(route('web.privacy'), false)
            ->assertSee(route('web.terms'), false);
    }

    public function test_short_legal_urls_redirect_permanently(): void
    {
        $this->get('/privacy')->assertRedirect('/privacy-policy')->assertStatus(301);
        $this->get('/terms')->assertRedirect('/terms-of-service')->assertStatus(301);
    }
}
