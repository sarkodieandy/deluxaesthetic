<?php

namespace App\Services\Notifications;

use App\Models\User;
use App\Notifications\PortalAlertNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InAppNotificationService
{
    /**
     * @param  array{title: string, message: string, action_url?: string|null, category?: string}  $payload
     */
    public function notifyUser(User $user, array $payload): void
    {
        $user->notify(new PortalAlertNotification($payload));
    }

    /**
     * Notify staff users who can view notifications / manage students.
     *
     * @param  array{title: string, message: string, action_url?: string|null, category?: string}  $payload
     */
    public function notifyAdmins(array $payload): void
    {
        $admins = $this->adminRecipients();

        if ($admins->isEmpty()) {
            return;
        }

        Notification::send($admins, new PortalAlertNotification($payload));
    }

    /**
     * @return Collection<int, User>
     */
    public function adminRecipients(): Collection
    {
        $byPermission = collect();
        if (Permission::query()
            ->where('name', 'notifications.view')
            ->where('guard_name', 'web')
            ->exists()) {
            $byPermission = User::permission('notifications.view')
                ->where('is_active', true)
                ->get();
        }

        $adminRoles = Role::query()
            ->where('guard_name', 'web')
            ->whereIn('name', ['Super Administrator', 'Clinic Administrator'])
            ->pluck('name')
            ->all();

        $byRole = empty($adminRoles)
            ? collect()
            : User::role($adminRoles)
                ->where('is_active', true)
                ->get();

        return $byPermission
            ->merge($byRole)
            ->unique('id')
            ->values();
    }
}
