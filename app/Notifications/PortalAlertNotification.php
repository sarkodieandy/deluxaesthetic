<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PortalAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array{title: string, message: string, action_url?: string|null, category?: string}  $payload
     */
    public function __construct(
        public array $payload,
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array{title: string, message: string, action_url: string|null, category: string}
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => (string) ($this->payload['title'] ?? 'Notification'),
            'message' => (string) ($this->payload['message'] ?? ''),
            'action_url' => $this->payload['action_url'] ?? null,
            'category' => (string) ($this->payload['category'] ?? 'general'),
        ];
    }
}
