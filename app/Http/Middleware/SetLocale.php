<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supported = config('clinic.supported_locales', ['en', 'fr']);
        $locale = session('locale', config('clinic.default_locale', 'en'));

        if ($request->user()?->locale && in_array($request->user()->locale, $supported, true)) {
            $locale = $request->user()->locale;
        }

        if (! in_array($locale, $supported, true)) {
            $locale = config('clinic.default_locale', 'en');
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
