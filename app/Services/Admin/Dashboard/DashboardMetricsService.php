<?php

namespace App\Services\Admin\Dashboard;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\ClientProfile;
use App\Models\Order;
use App\Models\StudentProfile;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardMetricsService
{
    public function metrics(): array
    {
        return Cache::remember('admin.dashboard.metrics', now()->addMinutes(2), function () {
            $today = CarbonImmutable::today(config('clinic.timezone', 'Africa/Accra'));
            $monthStart = $today->startOfMonth();

            return [
                'appointments_today' => $this->appointmentsToday($today),
                'appointments_pending' => $this->appointmentsPending(),
                'appointments_confirmed_today' => $this->appointmentsConfirmedToday($today),
                'appointments_completed_today' => $this->appointmentsCompletedToday($today),
                'monthly_clinic_revenue' => $this->monthlyClinicRevenue($monthStart),
                'active_clients' => ClientProfile::query()->count(),
                'active_students' => StudentProfile::query()->count(),
                'new_orders' => $this->newOrdersCount(),
                'low_stock_products' => $this->lowStockCount(),
                'failed_payments' => $this->failedPaymentsCount(),
                'staff_users' => User::query()->role(config('admin.roles', []))->where('is_active', true)->count(),
            ];
        });
    }

    /**
     * @return list<array{description: string, created_at: string, user: ?string}>
     */
    public function recentActivity(int $limit = 8): array
    {
        return AuditLog::query()
            ->with('user:id,name')
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(fn (AuditLog $log) => [
                'description' => $log->description ?? data_get($log->new_values, 'message') ?? $log->action,
                'created_at' => $log->created_at?->timezone(config('clinic.timezone', 'Africa/Accra'))->format('d M Y H:i') ?? '',
                'user' => $log->user?->name,
            ])
            ->all();
    }

    public function orderTrend(int $days = 30): array
    {
        $days = in_array($days, [7, 30, 90], true) ? $days : 30;

        return Cache::remember("admin.dashboard.order-trend.{$days}", now()->addMinutes(2), function () use ($days) {
            $timezone = config('clinic.timezone', 'Africa/Accra');
            $start = CarbonImmutable::today($timezone)->subDays($days - 1);
            $orders = Order::query()
                ->where('created_at', '>=', $start->startOfDay()->utc())
                ->get(['created_at', 'grand_total', 'payment_status']);

            $series = collect(range(0, $days - 1))->map(function (int $offset) use ($start, $orders, $timezone, $days) {
                $date = $start->addDays($offset);
                $daily = $orders->filter(fn (Order $order) => $order->created_at->timezone($timezone)->isSameDay($date));

                return [
                    'date' => $date->toDateString(),
                    'label' => $days <= 7 ? $date->format('D') : $date->format('d M'),
                    'orders' => $daily->count(),
                    'revenue' => round((float) $daily->where('payment_status', 'paid')->sum('grand_total'), 2),
                ];
            })->values();

            return [
                'days' => $days,
                'series' => $series->all(),
                'total_orders' => $series->sum('orders'),
                'total_revenue' => $series->sum('revenue'),
                'average_order_value' => $series->sum('orders') > 0
                    ? round($series->sum('revenue') / $series->sum('orders'), 2)
                    : 0,
            ];
        });
    }

    private function appointmentsToday(CarbonImmutable $today): int
    {
        return Appointment::query()
            ->whereDate('starts_at', $today)
            ->whereNotIn('status', [AppointmentStatus::Cancelled, AppointmentStatus::Draft])
            ->count();
    }

    private function appointmentsPending(): int
    {
        return Appointment::query()
            ->whereIn('status', [AppointmentStatus::Pending, AppointmentStatus::AwaitingPayment])
            ->count();
    }

    private function appointmentsConfirmedToday(CarbonImmutable $today): int
    {
        return Appointment::query()
            ->whereDate('starts_at', $today)
            ->where('status', AppointmentStatus::Confirmed)
            ->count();
    }

    private function appointmentsCompletedToday(CarbonImmutable $today): int
    {
        return Appointment::query()
            ->whereDate('starts_at', $today)
            ->where('status', AppointmentStatus::Completed)
            ->count();
    }

    private function monthlyClinicRevenue(CarbonImmutable $monthStart): float
    {
        $fromAppointments = (float) Appointment::query()
            ->where('starts_at', '>=', $monthStart)
            ->whereIn('status', [
                AppointmentStatus::Completed,
                AppointmentStatus::Confirmed,
                AppointmentStatus::InProgress,
                AppointmentStatus::CheckedIn,
            ])
            ->sum('amount_paid');

        if (Schema::hasTable('payments')) {
            $fromPayments = (float) DB::table('payments')
                ->where('created_at', '>=', $monthStart)
                ->where('status', 'completed')
                ->sum('amount');

            return max($fromAppointments, $fromPayments);
        }

        return $fromAppointments;
    }

    private function newOrdersCount(): int
    {
        if (! Schema::hasTable('orders')) {
            return 0;
        }

        return (int) DB::table('orders')
            ->whereIn('status', ['awaiting_payment', 'paid', 'processing'])
            ->count();
    }

    private function lowStockCount(): int
    {
        if (! Schema::hasTable('products')) {
            return 0;
        }

        return (int) DB::table('products')
            ->where('is_active', true)
            ->whereRaw('stock_quantity <= low_stock_threshold')
            ->count();
    }

    private function failedPaymentsCount(): int
    {
        if (! Schema::hasTable('payments')) {
            return 0;
        }

        return (int) DB::table('payments')
            ->where('status', 'failed')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
    }
}
