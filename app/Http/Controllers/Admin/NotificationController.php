<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ManagesDatabaseNotifications;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    use ManagesDatabaseNotifications;

    public function index(Request $request): View
    {
        return $this->notificationsIndex(
            $request,
            'admin.notifications.index',
            'admin.notifications.read',
            'admin.notifications.read-all',
        );
    }

    public function markRead(Request $request, string $notification): RedirectResponse
    {
        return $this->markNotificationRead($request, $notification, 'admin.notifications.index');
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        return $this->markAllNotificationsRead($request, 'admin.notifications.index');
    }
}
