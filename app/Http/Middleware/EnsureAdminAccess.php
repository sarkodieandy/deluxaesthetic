<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(Response::HTTP_FORBIDDEN);
        }

        if ($user->hasAnyRole(['Client', 'Student'])) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $staffRoles = config('admin.roles', []);

        if ($user->hasAnyRole($staffRoles) || $user->can('dashboard.view')) {
            return $next($request);
        }

        abort(Response::HTTP_FORBIDDEN);
    }
}
