<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PortalRedirect
{
    public static function homeUrl(User $user): string
    {
        return route($user->portalHomeRoute(), absolute: false);
    }

    public static function afterLogin(User $user, Request $request): RedirectResponse
    {
        $intended = $request->session()->pull('url.intended');
        $default = self::homeUrl($user);

        if (is_string($intended) && $intended !== '' && self::intendedAllowedFor($user, $intended)) {
            return redirect()->to($intended);
        }

        return redirect()->to($default);
    }

    public static function afterRegistration(User $user): RedirectResponse
    {
        return redirect()->to(self::homeUrl($user));
    }

    /**
     * @param  array<string, mixed>  $query
     */
    public static function afterAuthenticated(User $user, Request $request, array $query = []): RedirectResponse
    {
        $intended = $request->session()->pull('url.intended');
        $default = self::homeUrl($user);

        if ($query !== []) {
            $default .= (str_contains($default, '?') ? '&' : '?').http_build_query($query);
        }

        if (is_string($intended) && $intended !== '' && self::intendedAllowedFor($user, $intended)) {
            $target = $intended;
            if ($query !== []) {
                $target .= (str_contains($target, '?') ? '&' : '?').http_build_query($query);
            }

            return redirect()->to($target);
        }

        return redirect()->to($default);
    }

    public static function intendedAllowedFor(User $user, string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '/';
        $staffRoles = config('admin.roles', []);

        if ($user->hasRole('Student') && ! $user->hasAnyRole($staffRoles)) {
            return str_starts_with($path, '/student')
                || str_starts_with($path, '/profile')
                || $path === '/dashboard'
                || str_starts_with($path, '/academy');
        }

        if ($user->hasRole('Client') && ! $user->hasAnyRole($staffRoles) && ! $user->hasRole('Student')) {
            return str_starts_with($path, '/profile')
                || $path === '/dashboard'
                || str_starts_with($path, '/book');
        }

        if ($user->hasAnyRole(['Practitioner', 'Therapist'])) {
            return str_starts_with($path, '/practitioner')
                || str_starts_with($path, '/profile')
                || $path === '/dashboard';
        }

        if ($user->hasRole('Trainer')) {
            return str_starts_with($path, '/trainer')
                || str_starts_with($path, '/profile')
                || $path === '/dashboard';
        }

        if (str_starts_with($path, '/admin')) {
            return ! $user->hasAnyRole(['Client', 'Student'])
                && ($user->hasAnyRole($staffRoles) || $user->can('dashboard.view'));
        }

        return ! str_starts_with($path, '/admin');
    }
}
