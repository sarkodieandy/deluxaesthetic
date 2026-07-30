<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function profile(Request $request): View
    {
        return view('admin.account.profile', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'locale' => ['required', Rule::in(config('clinic.supported_locales', ['en', 'fr']))],
        ]);

        $user->fill($data);
        if ($user->isDirty('email')) {
            $user->email_verified_at = now();
        }
        $user->profile_completed_at ??= now();
        $user->save();

        return back()->with('status', 'profile-updated');
    }

    public function security(Request $request): View
    {
        $user = $request->user();
        $sessions = collect();

        if (config('session.driver') === 'database' && Schema::hasTable(config('session.table', 'sessions'))) {
            $sessions = DB::table(config('session.table', 'sessions'))
                ->where('user_id', $user->id)
                ->orderByDesc('last_activity')
                ->get()
                ->map(fn ($session) => [
                    'id' => $session->id,
                    'current' => hash_equals((string) $session->id, (string) $request->session()->getId()),
                    'ip_address' => $session->ip_address ?: 'Unknown',
                    'user_agent' => $this->deviceLabel((string) $session->user_agent),
                    'last_active_at' => now()->setTimestamp((int) $session->last_activity),
                ]);
        }

        return view('admin.account.security', [
            'user' => $user,
            'googleLinked' => $user->socialAccounts()->where('provider', 'google')->exists(),
            'sessions' => $sessions,
        ]);
    }

    public function destroyOtherSessions(Request $request): RedirectResponse
    {
        $request->validateWithBag('sessions', [
            'password' => ['required', 'current_password'],
        ]);

        if (config('session.driver') === 'database' && Schema::hasTable(config('session.table', 'sessions'))) {
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', $request->user()->id)
                ->where('id', '!=', $request->session()->getId())
                ->delete();
        }

        return back()->with('status', 'other-sessions-ended');
    }

    private function deviceLabel(string $userAgent): string
    {
        $browser = match (true) {
            str_contains($userAgent, 'Edg/') => 'Microsoft Edge',
            str_contains($userAgent, 'Chrome/') => 'Chrome',
            str_contains($userAgent, 'Firefox/') => 'Firefox',
            str_contains($userAgent, 'Safari/') => 'Safari',
            default => 'Unknown browser',
        };
        $device = match (true) {
            str_contains($userAgent, 'iPhone') => 'iPhone',
            str_contains($userAgent, 'iPad') => 'iPad',
            str_contains($userAgent, 'Android') => 'Android',
            str_contains($userAgent, 'Macintosh') => 'Mac',
            str_contains($userAgent, 'Windows') => 'Windows',
            str_contains($userAgent, 'Linux') => 'Linux',
            default => 'Unknown device',
        };

        return $browser.' on '.$device;
    }
}
