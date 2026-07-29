<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PractitionerProfile;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function index(): View
    {
        $ceo = PractitionerProfile::query()
            ->with('user')
            ->where('is_ceo', true)
            ->where('is_active', true)
            ->first();

        return view('web.about.index', [
            'ceo' => $ceo,
            'portraits' => [
                'a' => $ceo?->photoUrl() ?? asset(config('clinic.ceo.portrait_a')),
                'b' => asset(config('clinic.ceo.portrait_b')),
            ],
            'clinic' => config('clinic'),
        ]);
    }
}
