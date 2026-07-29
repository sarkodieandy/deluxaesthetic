<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentProfileComplete
{
    /** @var list<string> */
    protected array $except = [
        'student.profile.edit',
        'student.profile.update',
        'student.activate.show',
        'student.activate.store',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->route()?->getName(), $this->except, true)) {
            return $next($request);
        }

        $profile = $request->user()?->studentProfile;

        if ($profile && ! $profile->profile_completed_at) {
            return redirect()
                ->route('student.profile.edit')
                ->with('status', 'Please complete your student profile to continue.');
        }

        return $next($request);
    }
}
