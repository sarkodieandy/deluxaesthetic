<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',
            'appointments.view', 'appointments.create', 'appointments.update', 'appointments.approve',
            'appointments.reschedule', 'appointments.cancel', 'appointments.complete', 'appointments.refund', 'appointments.calendar',
            'consultations.view', 'consultations.assign', 'consultations.respond', 'consultations.close',
            'clients.view', 'clients.create', 'clients.update', 'clients.archive', 'clients.view_private_records', 'clients.manage',
            'students.view', 'students.create', 'students.update', 'students.activate', 'students.suspend', 'students.archive', 'students.manage',
            'course_enquiries.view', 'course_enquiries.manage',
            'practitioners.manage', 'practitioner_schedules.manage',
            'treatments.view', 'treatments.create', 'treatments.update', 'treatments.publish', 'treatments.delete',
            'courses.view', 'courses.create', 'courses.update', 'courses.publish', 'courses.delete', 'courses.manage',
            'enrolments.view', 'enrolments.create', 'enrolments.approve', 'enrolments.activate', 'enrolments.transfer', 'enrolments.withdraw', 'enrolments.cancel', 'enrolments.manage',
            'attendance.manage', 'assessments.manage', 'materials.manage',
            'certificates.view', 'certificates.issue', 'certificates.revoke', 'certificates.reissue',
            'products.view', 'products.create', 'products.update', 'products.publish', 'products.delete', 'products.manage',
            'inventory.view', 'inventory.adjust', 'inventory.export', 'inventory.manage',
            'orders.view', 'orders.update', 'orders.cancel', 'orders.refund', 'orders.manage', 'orders.fulfill',
            'payments.view', 'payments.record', 'payments.reconcile', 'payments.export',
            'refunds.view', 'refunds.approve', 'refunds.process', 'refunds.manage',
            'content.manage', 'blog.manage', 'gallery.manage', 'media.manage', 'menus.manage', 'translations.manage',
            'notifications.view', 'notifications.send', 'notifications.retry', 'notification_templates.manage', 'notifications.manage',
            'ai_knowledge.manage', 'ai_conversations.view', 'ai.manage',
            'reports.view', 'reports.export', 'reports.financial',
            'users.manage', 'roles.manage', 'permissions.manage', 'settings.manage', 'audit.view',
            'reviews.moderate', 'coupons.manage', 'loyalty.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $all = $permissions;

        $roles = [
            'Super Administrator' => $all,
            'Clinic Administrator' => array_diff($all, ['roles.manage', 'permissions.manage']),
            'Receptionist' => [
                'dashboard.view', 'appointments.view', 'appointments.create', 'appointments.update', 'appointments.cancel', 'appointments.calendar',
                'consultations.view', 'clients.view', 'clients.manage', 'clients.create', 'clients.update',
                'students.view', 'students.create', 'course_enquiries.view', 'enrolments.view', 'enrolments.create',
            ],
            'Practitioner' => [
                'dashboard.view', 'appointments.view', 'appointments.update', 'appointments.complete', 'appointments.calendar', 'clients.view',
            ],
            'Therapist' => [
                'dashboard.view', 'appointments.view', 'appointments.update', 'appointments.complete', 'appointments.calendar', 'clients.view',
            ],
            'Trainer' => [
                'dashboard.view', 'courses.view', 'enrolments.manage', 'attendance.manage', 'assessments.manage', 'materials.manage',
                'students.view', 'students.update', 'certificates.view',
            ],
            'Finance Officer' => [
                'dashboard.view', 'payments.view', 'payments.reconcile', 'refunds.view', 'refunds.approve', 'reports.view', 'reports.financial', 'appointments.view',
            ],
            'Store Manager' => [
                'dashboard.view', 'products.view', 'products.manage', 'inventory.view', 'inventory.manage', 'orders.view', 'orders.manage', 'orders.fulfill', 'reviews.moderate', 'coupons.manage',
            ],
            'Content Manager' => [
                'dashboard.view', 'content.manage', 'blog.manage', 'gallery.manage', 'media.manage', 'translations.manage',
            ],
            'Support Agent' => [
                'dashboard.view', 'clients.view', 'notifications.view', 'notifications.manage', 'ai.manage', 'ai_conversations.view', 'appointments.view', 'consultations.view',
            ],
            'Client' => [],
            'Student' => [],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::findOrCreate($roleName);
            $role->syncPermissions($rolePermissions);
        }
    }
}
