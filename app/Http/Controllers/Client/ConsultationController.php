<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ConsultationRequest;
use App\Services\Client\ClientPortalService;
use App\Services\Notifications\InAppNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsultationController extends Controller
{
    public function __construct(
        private readonly ClientPortalService $portal,
        private readonly InAppNotificationService $notifications,
    ) {}

    public function index(Request $request): View
    {
        return view('client.consultations.index', [
            'consultations' => $this->portal->consultations($request->user()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'description' => ['required', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:50'],
            'preferred_date' => ['nullable', 'date', 'after_or_equal:today'],
            'preferred_channel' => ['nullable', 'string', 'max:40'],
        ]);

        $user = $request->user();

        ConsultationRequest::query()->create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $data['phone'] ?? $user->phone,
            'preferred_date' => $data['preferred_date'] ?? null,
            'preferred_channel' => $data['preferred_channel'] ?? 'phone',
            'description' => $data['description'],
            'consent_accepted' => true,
            'status' => 'new',
        ]);

        $this->notifications->notifyAdmins([
            'title' => 'New consultation request',
            'message' => $user->name.' requested a consultation.',
            'action_url' => route('admin.consultations.index', absolute: false),
            'category' => 'consultation',
        ]);

        $this->notifications->notifyUser($user, [
            'title' => 'Consultation request received',
            'message' => 'Thanks — our team will contact you about your consultation request.',
            'action_url' => route('client.consultations.index', absolute: false),
            'category' => 'consultation',
        ]);

        return back()->with('status', 'Consultation request submitted. Our team will contact you soon.');
    }
}
