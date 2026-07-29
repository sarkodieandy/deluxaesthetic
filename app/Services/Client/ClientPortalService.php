<?php

namespace App\Services\Client;

use App\Models\Appointment;
use App\Models\ConsultationRequest;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClientPortalService
{
    public function appointments(User $user): Collection
    {
        $profileId = $user->clientProfile?->id;
        if (! $profileId) {
            return collect();
        }

        return Appointment::query()
            ->with(['treatment', 'practitioner.user', 'branch'])
            ->where('client_profile_id', $profileId)
            ->orderByDesc('starts_at')
            ->limit(50)
            ->get();
    }

    public function upcomingAppointments(User $user): Collection
    {
        return $this->appointments($user)
            ->filter(fn (Appointment $appointment) => $appointment->starts_at && $appointment->starts_at->isFuture())
            ->values();
    }

    public function consultations(User $user): Collection
    {
        return ConsultationRequest::query()
            ->where('user_id', $user->id)
            ->latest()
            ->limit(50)
            ->get();
    }

    public function payments(User $user): Collection
    {
        if (! Schema::hasTable('payments')) {
            return collect();
        }

        return DB::table('payments')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();
    }

    public function orders(User $user): Collection
    {
        if (! Schema::hasTable('orders')) {
            return collect();
        }

        return DB::table('orders')
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();
    }

    public function loyaltyPoints(User $user): int
    {
        $profile = $user->clientProfile;
        if (! $profile || ! Schema::hasColumn('client_profiles', 'loyalty_points')) {
            return 0;
        }

        return (int) ($profile->loyalty_points ?? 0);
    }

    /**
     * @return array<string, mixed>
     */
    public function dashboardMetrics(User $user): array
    {
        $appointments = $this->appointments($user);
        $upcoming = $appointments->filter(fn (Appointment $a) => $a->starts_at?->isFuture())->values();
        $orders = $this->orders($user);

        return [
            'appointments' => $appointments,
            'upcoming' => $upcoming,
            'next_appointment' => $upcoming->first(),
            'appointment_count' => $appointments->count(),
            'upcoming_count' => $upcoming->count(),
            'consultation_count' => $this->consultations($user)->count(),
            'order_count' => $orders->count(),
            'open_orders' => $orders->whereIn('status', ['pending', 'processing', 'paid', 'confirmed'])->count(),
            'loyalty_points' => $this->loyaltyPoints($user),
            'payment_count' => $this->payments($user)->count(),
            'unread_notifications' => Schema::hasTable('notifications')
                ? $user->unreadNotifications()->count()
                : 0,
        ];
    }
}
