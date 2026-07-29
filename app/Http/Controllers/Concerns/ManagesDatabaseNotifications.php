<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

trait ManagesDatabaseNotifications
{
    protected function notificationsIndex(Request $request, string $view, string $markReadRoute, string $markAllRoute): View
    {
        $user = $request->user();

        return view($view, [
            'notifications' => $user->notifications()->paginate(20),
            'unreadCount' => $user->unreadNotifications()->count(),
            'markReadRoute' => $markReadRoute,
            'markAllRoute' => $markAllRoute,
        ]);
    }

    protected function markNotificationRead(Request $request, string $notificationId, string $fallbackRoute): RedirectResponse
    {
        $notification = $this->ownedNotification($request, $notificationId);
        $notification->markAsRead();

        $url = $notification->data['action_url'] ?? null;

        if (is_string($url) && $url !== '') {
            return redirect()->to($url);
        }

        return redirect()->route($fallbackRoute);
    }

    protected function markAllNotificationsRead(Request $request, string $fallbackRoute): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return redirect()->route($fallbackRoute)->with('status', 'All notifications marked as read.');
    }

    protected function ownedNotification(Request $request, string $notificationId): DatabaseNotification
    {
        /** @var DatabaseNotification $notification */
        $notification = $request->user()
            ->notifications()
            ->where('id', $notificationId)
            ->firstOrFail();

        return $notification;
    }
}
