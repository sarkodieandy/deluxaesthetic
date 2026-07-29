<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        $supported = config('clinic.supported_locales', ['en', 'fr']);

        if (! in_array($locale, $supported, true)) {
            abort(404);
        }

        session(['locale' => $locale]);
        app()->setLocale($locale);

        if ($request->user()) {
            $request->user()->forceFill(['locale' => $locale])->save();
        }

        return redirect()->back();
    }
}
