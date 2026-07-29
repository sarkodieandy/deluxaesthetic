<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use App\Support\PortalRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ActivationController extends Controller
{
    public function show(string $token): View|RedirectResponse
    {
        if (! $this->findProfile($token)) {
            abort(404);
        }

        return view('student.security.activate', compact('token'));
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $profile = $this->findProfile($token);
        if (! $profile) {
            abort(404);
        }

        $data = $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = $profile->user;
        $user->update([
            'password' => Hash::make($data['password']),
            'email_verified_at' => $user->email_verified_at ?? now(),
            'is_active' => true,
        ]);

        $profile->update([
            'portal_activated_at' => now(),
            'invitation_token' => null,
            'invitation_expires_at' => null,
        ]);

        auth()->login($user);

        return PortalRedirect::afterRegistration($user)
            ->with('status', 'Portal access activated. Complete your profile to continue.');
    }

    private function findProfile(string $token): ?StudentProfile
    {
        return StudentProfile::query()
            ->where('invitation_token', hash('sha256', $token))
            ->where('invitation_expires_at', '>', now())
            ->first();
    }
}
