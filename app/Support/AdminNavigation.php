<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Route;

class AdminNavigation
{
    /**
     * @return list<array{key: string, label: string, items: list<array{label: string, route: string, url: string, active: bool}>}>
     */
    public static function forUser(?User $user): array
    {
        if ($user === null) {
            return [];
        }

        $groups = [];

        foreach (config('admin.navigation', []) as $group) {
            $items = [];

            foreach ($group['items'] as $item) {
                if (! $user->can($item['permission'])) {
                    continue;
                }

                if (! Route::has($item['route'])) {
                    continue;
                }

                $isExternal = (bool) ($item['external'] ?? false);

                $items[] = [
                    'label' => $item['label'],
                    'route' => $item['route'],
                    'url' => route($item['route']),
                    'active' => $isExternal ? false : request()->routeIs($item['route'].'*'),
                    'icon' => self::iconForRoute($item['route']),
                    'external' => $isExternal,
                ];
            }

            if ($items !== []) {
                $groups[] = [
                    'key' => $group['key'],
                    'label' => $group['label'],
                    'items' => $items,
                ];
            }
        }

        return $groups;
    }

    public static function iconForRoute(string $route): string
    {
        return match ($route) {
            'admin.dashboard' => 'dashboard',
            'web.home' => 'branch',
            'admin.activity.index' => 'activity',
            'admin.appointments.index' => 'calendar',
            'admin.consultations.index' => 'consultation',
            'admin.clients.index' => 'users',
            'admin.practitioners.index' => 'users',
            'admin.treatments.index' => 'treatment',
            'admin.branches.index' => 'branch',
            'admin.schedules.index' => 'calendar',
            'admin.students.index' => 'users',
            'admin.trainers.index' => 'academy',
            'admin.courses.index' => 'course',
            'admin.course-enquiries.index' => 'enquiry',
            'admin.physical-enrolment.create' => 'enrolment',
            'admin.course-schedules.sessions.index' => 'calendar',
            'admin.enrolments.index' => 'enrolment',
            'admin.attendance.index' => 'attendance',
            'admin.assessments.index' => 'assessment',
            'admin.certificates.index' => 'certificate',
            'admin.products.index' => 'store',
            'admin.inventory.index' => 'inventory',
            'admin.orders.index' => 'order',
            'admin.deliveries.index' => 'order',
            'admin.reviews.index' => 'content',
            'admin.payments.index' => 'payment',
            'admin.refunds.index' => 'payment',
            'admin.reports.index' => 'report',
            'admin.testimonials.index' => 'content',
            'admin.loyalty.index' => 'payment',
            'admin.referrals.index' => 'users',
            'admin.content.homepage', 'admin.pages.index', 'admin.blog.index', 'admin.faqs.index' => 'content',
            'admin.gallery.index' => 'gallery',
            'admin.media.index' => 'media',
            'admin.translations.index' => 'content',
            'admin.notifications.index' => 'notification',
            'admin.ai.index' => 'content',
            'admin.users.index' => 'users',
            'admin.roles.index' => 'shield',
            'admin.settings.index' => 'settings',
            'admin.audit.index' => 'activity',
            default => 'item',
        };
    }
}
