<?php

namespace Tests\Feature\Auth;

use App\Models\SocialAccount;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class GoogleAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        config([
            'services.google.client_id' => 'test-client',
            'services.google.client_secret' => 'test-secret',
            'authentication.google.enabled' => true,
        ]);
    }

    private function fakeGoogleUser(array $overrides = []): \Laravel\Socialite\Two\User
    {
        $data = array_merge([
            'id' => 'google-id',
            'nickname' => null,
            'name' => 'Google User',
            'email' => 'google-user@example.com',
            'avatar' => null,
            'email_verified' => true,
        ], $overrides);

        $user = new \Laravel\Socialite\Two\User;
        $user->map($data);

        return $user;
    }

    private function mockGoogleProvider(): \Mockery\MockInterface
    {
        $provider = Mockery::mock(\Laravel\Socialite\Contracts\Provider::class);
        $provider->shouldReceive('redirectUrl')->andReturnSelf();
        $provider->shouldReceive('scopes')->andReturnSelf();

        return $provider;
    }

    public function test_google_redirect_route_is_available(): void
    {
        $provider = $this->mockGoogleProvider();
        $provider->shouldReceive('redirect')->once()->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $this->get(route('auth.google.redirect'))->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    public function test_existing_social_account_logs_in_and_redirects_to_portal(): void
    {
        $user = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $user->assignRole('Client');
        \App\Models\ClientProfile::create(['user_id' => $user->id, 'referral_code' => 'TEST1234']);

        SocialAccount::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_user_id' => 'google-123',
            'provider_email' => $user->email,
            'linked_at' => now(),
        ]);

        $abstractUser = $this->fakeGoogleUser([
            'id' => 'google-123',
            'email' => $user->email,
            'name' => $user->name,
        ]);

        $provider = $this->mockGoogleProvider();
        $provider->shouldReceive('user')->once()->andReturn($abstractUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('client.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_existing_user_with_same_email_logs_in_without_password_link(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $user->assignRole('Student');
        \App\Models\StudentProfile::query()->create([
            'user_id' => $user->id,
            'student_number' => 'STU-2026-0001',
        ]);

        $abstractUser = $this->fakeGoogleUser([
            'id' => 'google-existing',
            'email' => $user->email,
            'name' => $user->name,
        ]);

        $provider = $this->mockGoogleProvider();
        $provider->shouldReceive('user')->once()->andReturn($abstractUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('student.dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('social_accounts', [
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_user_id' => 'google-existing',
        ]);
    }

    public function test_new_google_user_is_sent_to_account_type_selection(): void
    {
        $abstractUser = $this->fakeGoogleUser([
            'id' => 'google-new',
            'email' => 'new-google@example.com',
            'name' => 'New Google User',
        ]);

        $provider = $this->mockGoogleProvider();
        $provider->shouldReceive('user')->once()->andReturn($abstractUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('auth.google.select-account-type'));
    }
}
