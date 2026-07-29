<?php

namespace App\Http\Controllers\Student;

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
            'student.notifications.index',
            'student.notifications.read',
            'student.notifications.read-all',
        );
    }

    public function markRead(Request $request, string $notification): RedirectResponse
    {
        return $this->markNotificationRead($request, $notification, 'student.notifications.index');
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        return $this->markAllNotificationsRead($request, 'student.notifications.index');
    }
}
