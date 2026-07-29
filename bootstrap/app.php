<?php

use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\SetLocale;
use App\Support\PortalRedirect;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));

            // Client portal routes are intentionally disabled. Clients will use the public site instead of a separate portal.
            // Route::middleware('web')
            //     ->group(base_path('routes/client.php'));

            Route::middleware('web')
                ->group(base_path('routes/student.php'));

            Route::middleware('web')
                ->group(base_path('routes/practitioner.php'));

            Route::middleware('web')
                ->group(base_path('routes/trainer.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetLocale::class,
        ]);

        // Shared hosting / reverse proxies terminate SSL before PHP.
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'ensure_role' => EnsureUserHasRole::class,
            'active.account' => \App\Http\Middleware\EnsureActiveAccount::class,
            'admin.access' => \App\Http\Middleware\EnsureAdminAccess::class,
            'student.role' => \App\Http\Middleware\EnsureStudentRole::class,
            'student.profile.complete' => \App\Http\Middleware\EnsureStudentProfileComplete::class,
            'student.enrolment.active' => \App\Http\Middleware\EnsureActiveStudentEnrolment::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('login'));

        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();

            return $user ? PortalRedirect::homeUrl($user) : route('web.home');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
