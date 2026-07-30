<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\WebPage;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebPageCmsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_content_admin_can_edit_a_public_page_and_its_seo(): void
    {
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Content Manager');
        $page = WebPage::where('route_name', 'web.contact')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'name' => 'Contact',
            'seo_title' => 'Contact Our Accra Aesthetic Clinic',
            'meta_description' => 'Speak with the De Luxe clinic, academy and store team in East Legon.',
            'hero_eyebrow' => 'Speak with our team',
            'hero_title' => 'Your next step starts here.',
            'hero_body' => 'Our team will guide you toward the right service.',
            'hero_image_url' => '',
            'is_published' => '1',
            'sections' => [[
                'eyebrow' => 'Private guidance',
                'heading' => 'Not sure where to begin?',
                'body' => 'Tell us what you need and our reception team will direct your enquiry.',
                'cta_label' => 'Book now',
                'cta_url' => '/book',
                'is_enabled' => '1',
            ]],
        ])->assertSessionHas('status', 'page-updated');

        $this->get(route('web.contact'))
            ->assertOk()
            ->assertSee('Your next step starts here.')
            ->assertSee('Not sure where to begin?')
            ->assertSee('<title>Contact Our Accra Aesthetic Clinic</title>', false);
    }

    public function test_unpublished_page_is_hidden_but_admin_can_preview_it(): void
    {
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Content Manager');
        $page = WebPage::where('route_name', 'web.contact')->firstOrFail();
        $page->update(['is_published' => false, 'hero_title' => 'Draft contact page']);

        $this->get(route('web.contact'))->assertNotFound();

        $this->actingAs($admin)
            ->get(route('web.contact', ['cms_preview' => $page->id]))
            ->assertOk()
            ->assertSee('Draft preview')
            ->assertSee('Draft contact page');
    }

    public function test_staff_without_content_permission_cannot_manage_pages(): void
    {
        $staff = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $staff->assignRole('Receptionist');

        $this->actingAs($staff)->get(route('admin.pages.index'))->assertForbidden();
    }
}
