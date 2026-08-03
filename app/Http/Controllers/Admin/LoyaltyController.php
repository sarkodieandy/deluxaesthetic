<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientProfile;
use App\Models\LoyaltyTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LoyaltyController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $clients = ClientProfile::with('user:id,name,email,phone')
            ->when($search, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")))
            ->orderByDesc('loyalty_points')->paginate(20)->withQueryString();

        return view('admin.loyalty.index', [
            'clients' => $clients,
            'transactions' => LoyaltyTransaction::with('client.user')->latest()->limit(12)->get(),
            'totalPoints' => ClientProfile::sum('loyalty_points'),
            'search' => $search,
        ]);
    }

    public function adjust(Request $request, ClientProfile $client): RedirectResponse
    {
        $data = $request->validate([
            'points' => ['required', 'integer', 'not_in:0', 'between:-100000,100000'],
            'description' => ['required', 'string', 'max:255'],
        ]);
        if ($client->loyalty_points + $data['points'] < 0) {
            return back()->withErrors(['points' => 'This adjustment would make the balance negative.']);
        }

        DB::transaction(function () use ($client, $data, $request) {
            $client->increment('loyalty_points', $data['points']);
            LoyaltyTransaction::create([
                'client_profile_id' => $client->id,
                'points' => $data['points'],
                'type' => 'manual',
                'description' => $data['description'],
                'created_by' => $request->user()->id,
            ]);
        });

        return back()->with('status', 'loyalty-adjusted');
    }
}
