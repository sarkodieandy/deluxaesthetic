<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientProfile;
use App\Models\LoyaltyTransaction;
use App\Models\Referral;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReferralController extends Controller
{
    public function index(): View
    {
        return view('admin.referrals.index', [
            'referrals' => Referral::with('referrer.user')->latest()->paginate(20),
            'clients' => ClientProfile::with('user:id,name,email')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Referral::create($this->validated($request) + ['created_by' => $request->user()->id]);

        return back()->with('status', 'referral-created');
    }

    public function update(Request $request, Referral $referral): RedirectResponse
    {
        $data = $this->validated($request);
        DB::transaction(function () use ($referral, $data, $request) {
            $wasConverted = $referral->status === 'converted';
            $isConverted = $data['status'] === 'converted';
            $referral->update([...$data, 'converted_at' => $isConverted ? ($referral->converted_at ?? now()) : null]);
            if (! $wasConverted && $isConverted && $data['reward_points'] > 0) {
                $referral->referrer()->increment('loyalty_points', $data['reward_points']);
                LoyaltyTransaction::create([
                    'client_profile_id' => $referral->referrer_client_profile_id,
                    'points' => $data['reward_points'],
                    'type' => 'referral',
                    'description' => 'Referral reward for '.$referral->referred_name,
                    'created_by' => $request->user()->id,
                ]);
            }
        });

        return back()->with('status', 'referral-updated');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'referrer_client_profile_id' => ['required', 'exists:client_profiles,id'],
            'referred_name' => ['required', 'string', 'max:120'],
            'referred_email' => ['nullable', 'email', 'max:255'],
            'referred_phone' => ['nullable', 'string', 'max:40'],
            'status' => ['required', Rule::in(['pending', 'contacted', 'converted', 'declined'])],
            'reward_points' => ['required', 'integer', 'min:0', 'max:100000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
