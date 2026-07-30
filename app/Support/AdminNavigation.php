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
                $activePattern = str_ends_with($item['route'], '.index')
                    ? substr($item['route'], 0, -6).'.*'
                    : $item['route'].'*';

                $items[] = [
                    'label' => $item['label'],
                    'route' => $item['route'],
                    'url' => route($item['route']),
                    'active' => $isExternal ? false : request()->routeIs($activePattern),
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
            'web.home' => 'globe',
            'admin.activity.index' => 'activity',
            'admin.appointments.index' => 'calendar',
            'admin.consultations.index' => 'consultation',
            'admin.clients.index' => 'users',
            'admin.practitioners.index' => 'practitioner',
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
            'admin.deliveries.index' => 'delivery',
            'admin.reviews.index' => 'review',
            'admin.payments.index' => 'payment',
            'admin.refunds.index' => 'refund',
            'admin.reports.index' => 'report',
            'admin.testimonials.index' => 'review',
            'admin.loyalty.index' => 'loyalty',
            'admin.referrals.index' => 'referral',
            'admin.content.homepage', 'admin.pages.index', 'admin.blog.index', 'admin.faqs.index' => 'content',
            'admin.gallery.index' => 'gallery',
            'admin.media.index' => 'media',
            'admin.translations.index' => 'language',
            'admin.notifications.index' => 'notification',
            'admin.email-templates.index' => 'mail',
            'admin.email-logs.index' => 'mail-check',
            'admin.ai.index' => 'sparkles',
            'admin.users.index' => 'users',
            'admin.roles.index' => 'shield',
            'admin.settings.index' => 'settings',
            'admin.audit.index' => 'activity',
            default => 'item',
        };
    }
}
