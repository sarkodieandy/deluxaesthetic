<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('student.profile.edit', [
            'user' => $request->user(),
            'profile' => $request->user()->studentProfile,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->studentProfile;
        abort_unless($profile, 403);

        $data = $request->validate([
            'phone' => ['nullable', 'string', 'max:50'],
            'address_line_1' => ['nullable', 'string', 'max:190'],
            'city' => ['nullable', 'string', 'max:120'],
            'emergency_contact_name' => ['nullable', 'string', 'max:190'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
        ]);

        $user->update(['phone' => $data['phone'] ?? $user->phone]);
        $profile->update([
            ...$data,
            'profile_completed_at' => now(),
        ]);

        return redirect()->route('student.dashboard')->with('status', 'Profile updated.');
    }
}
