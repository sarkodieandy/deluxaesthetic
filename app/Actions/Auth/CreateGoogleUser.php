<?php

namespace App\Actions\Auth;

use App\DTOs\Auth\GoogleUserData;
use App\Events\Auth\SocialAccountRegistered;
use App\Models\ClientProfile;
use App\Models\SocialAccount;
use App\Models\StudentProfile;
use App\Models\User;
use App\Services\Academy\PhysicalEnrolmentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class CreateGoogleUser
{
    public function __construct(
        private readonly PhysicalEnrolmentService $enrolments,
    ) {}

    /**
     * @param  'Client'|'Student'  $roleName
     * @param  array{phone?: string, marketing_email_opt_in?: bool}  $profile
     */
    public function handle(GoogleUserData $google, string $roleName, array $profile, bool $termsAccepted, bool $privacyAccepted): User
    {
        return DB::transaction(function () use ($google, $roleName, $profile, $termsAccepted, $privacyAccepted) {
            $user = User::create([
                'name' => $google->name,
                'email' => $google->email,
                'phone' => $profile['phone'] ?? null,
                'password' => null,
                'email_verified_at' => now(),
                'locale' => app()->getLocale(),
                'is_active' => true,
                'accepted_terms_at' => $termsAccepted ? now() : null,
                'accepted_privacy_at' => $privacyAccepted ? now() : null,
                'marketing_email_opt_in' => (bool) ($profile['marketing_email_opt_in'] ?? false),
                'marketing_opt_in_at' => ($profile['marketing_email_opt_in'] ?? false) ? now() : null,
            ]);

            $user->assignRole(Role::findOrCreate($roleName));

            if ($roleName === 'Client') {
                ClientProfile::create([
                    'user_id' => $user->id,
                    'referral_code' => strtoupper(Str::random(8)),
                ]);
            } else {
                StudentProfile::create([
                    'user_id' => $user->id,
                    'student_number' => $this->enrolments->allocateStudentNumber(),
                    'phone' => $profile['phone'] ?? null,
                    'profile_completed_at' => now(),
                ]);
            }

            SocialAccount::create([
                'user_id' => $user->id,
                'provider' => 'google',
                'provider_user_id' => $google->providerUserId,
                'provider_email' => $google->email,
                'provider_avatar_url' => $google->avatarUrl,
                'linked_at' => now(),
                'last_used_at' => now(),
            ]);

            event(new SocialAccountRegistered($user, 'google'));

            return $user->fresh(['socialAccounts', 'clientProfile', 'studentProfile']);
        });
    }
}
