<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\Client\ClientPortalService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoyaltyController extends Controller
{
    public function __construct(
        private readonly ClientPortalService $portal,
    ) {}

    public function index(Request $request): View
    {
        return view('client.loyalty.index', [
            'points' => $this->portal->loyaltyPoints($request->user()),
            'profile' => $request->user()->clientProfile,
        ]);
    }
}
