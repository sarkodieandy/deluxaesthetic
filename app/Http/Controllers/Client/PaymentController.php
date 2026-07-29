<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\Client\ClientPortalService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private readonly ClientPortalService $portal,
    ) {}

    public function index(Request $request): View
    {
        return view('client.payments.index', [
            'payments' => $this->portal->payments($request->user()),
        ]);
    }
}
