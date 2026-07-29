<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PractitionerProfile;
use Illuminate\View\View;

class PractitionerController extends Controller
{
    public function index(): View
    {
        $practitioners = PractitionerProfile::query()
            ->with('user')
            ->where('is_active', true)
            ->orderByDesc('is_ceo')
            ->orderBy('sort_order')
            ->get();

        return view('web.practitioners.index', [
            'practitioners' => $practitioners,
        ]);
    }
}
