<?php

namespace App\Services\Notifications;

use App\Models\User;
use App\Notifications\PortalAlertNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

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
        $byPermission = User::permission('notifications.view')
            ->where('is_active', true)
            ->get();

        $byRole = User::role(['Super Administrator', 'Clinic Administrator'])
            ->where('is_active', true)
            ->get();

        return $byPermission
            ->merge($byRole)
            ->unique('id')
            ->values();
    }
}
