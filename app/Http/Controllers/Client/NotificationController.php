<?php

namespace App\Http\Controllers\Client;

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
            'client.notifications.index',
            'client.notifications.read',
            'client.notifications.read-all',
        );
    }

    public function markRead(Request $request, string $notification): RedirectResponse
    {
        return $this->markNotificationRead($request, $notification, 'client.notifications.index');
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        return $this->markAllNotificationsRead($request, 'client.notifications.index');
    }
}
