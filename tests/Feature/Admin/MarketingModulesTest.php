<?php

namespace Tests\Feature\Admin;

use App\Models\ClientProfile;
use App\Models\Promotion;
use App\Models\Referral;
use App\Models\Testimonial;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MarketingModulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_content_manager_can_publish_a_promotion_banner(): void
    {
        Storage::fake('public');
        $admin = $this->staff('Content Manager');

        $this->actingAs($admin)->post(route('admin.promotions.store'), [
            'title' => 'Skin confidence week',
            'subtitle' => 'Book a consultation and discover your personalised plan.',
            'placement' => 'sitewide',
            'cta_label' => 'Book now',
            'cta_url' => '/book',
            'coupon_code' => 'GLOW20',
            'background_color' => '#171613',
            'text_color' => '#ffffff',
            'priority' => 20,
            'is_active' => '1',
            'image' => UploadedFile::fake()->image('campaign.webp', 1600, 500),
        ])->assertRedirect();

        $promotion = Promotion::firstOrFail();
        Storage::disk('public')->assertExists($promotion->image_path);

        $this->get(route('web.home'))
            ->assertOk()
            ->assertSee('Skin confidence week')
            ->assertSee('GLOW20');
    }

    public function test_managed_testimonial_appears_on_homepage(): void
    {
        $admin = $this->staff('Content Manager');

        $this->actingAs($admin)->post(route('admin.testimonials.store'), [
            'name' => 'Ama K.',
            'context' => 'Skin client',
            'quote' => 'The consultation was thoughtful and the result felt completely natural.',
            'rating' => 5,
            'sort_order' => 1,
            'is_featured' => '1',
            'is_active' => '1',
        ])->assertRedirect();

        $this->assertTrue(Testimonial::firstOrFail()->is_active);
        $this->get(route('web.home'))->assertSee('The consultation was thoughtful');
    }

    public function test_admin_can_adjust_loyalty_points_with_an_audit_transaction(): void
    {
        $admin = $this->staff('Super Administrator');
        $client = $this->client();

        $this->actingAs($admin)->post(route('admin.loyalty.adjust', $client), [
            'points' => 250,
            'description' => 'Completed treatment reward',
        ])->assertSessionHas('status', 'loyalty-adjusted');

        $this->assertSame(250, $client->fresh()->loyalty_points);
        $this->assertDatabaseHas('loyalty_transactions', ['client_profile_id' => $client->id, 'points' => 250]);
    }

    public function test_converting_referral_awards_points_only_once(): void
    {
        $admin = $this->staff('Super Administrator');
        $client = $this->client();
        $referral = Referral::create([
            'referrer_client_profile_id' => $client->id,
            'referred_name' => 'Esi A.',
            'status' => 'pending',
            'reward_points' => 0,
        ]);
        $payload = [
            'referrer_client_profile_id' => $client->id,
            'referred_name' => 'Esi A.',
            'referred_email' => 'esi@example.com',
            'referred_phone' => '',
            'status' => 'converted',
            'reward_points' => 100,
            'notes' => '',
        ];

        $this->actingAs($admin)->put(route('admin.referrals.update', $referral), $payload)->assertSessionHas('status', 'referral-updated');
        $this->actingAs($admin)->put(route('admin.referrals.update', $referral), $payload)->assertSessionHas('status', 'referral-updated');

        $this->assertSame(100, $client->fresh()->loyalty_points);
    }

    private function staff(string $role): User
    {
        $user = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $user->assignRole($role);

        return $user;
    }

    private function client(): ClientProfile
    {
        $user = User::factory()->create(['is_active' => true]);

        return ClientProfile::create([
            'user_id' => $user->id,
            'loyalty_points' => 0,
            'referral_code' => 'DLX'.str_pad((string) $user->id, 5, '0', STR_PAD_LEFT),
        ]);
    }
}
