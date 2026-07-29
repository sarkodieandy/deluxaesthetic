<?php

namespace App\Services\Appointments;

use App\Models\ClientProfile;
use App\Models\User;
use Illuminate\Support\Str;

class ResolveBookingClientProfile
{
    /**
     * Resolve a client profile for booking without requiring registration.
     * Logged-in clients reuse their profile; guests use contact details only.
     *
     * @param  array{name:string,email:string,phone:string}  $guest
     */
    public function execute(?User $user, array $guest): ClientProfile
    {
        if ($user?->hasRole('Client')) {
            $profile = $user->clientProfile;

            if ($profile) {
                return $profile;
            }

            return ClientProfile::create([
                'user_id' => $user->id,
                'referral_code' => strtoupper(Str::random(8)),
            ]);
        }

        $email = strtolower(trim($guest['email']));
        $name = trim($guest['name']);
        $phone = trim($guest['phone']);

        $existingUser = User::query()->where('email', $email)->first();

        if ($existingUser?->clientProfile) {
            return $existingUser->clientProfile;
        }

        $guestProfile = ClientProfile::query()
            ->whereNull('user_id')
            ->where('guest_email', $email)
            ->first();

        if ($guestProfile) {
            $guestProfile->update([
                'guest_name' => $name,
                'guest_phone' => $phone,
            ]);

            return $guestProfile->fresh();
        }

        return ClientProfile::create([
            'user_id' => null,
            'guest_name' => $name,
            'guest_email' => $email,
            'guest_phone' => $phone,
            'referral_code' => strtoupper(Str::random(8)),
        ]);
    }
}
