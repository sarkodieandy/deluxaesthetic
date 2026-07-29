<?php

namespace App\Notifications;

use App\Models\Enrolment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentPortalInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Enrolment $enrolment,
        public string $activationUrl,
        public User $invitedBy,
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your De Lux Academy student portal invitation')
            ->greeting('Hello '.$notifiable->name)
            ->line('Your physical enrolment has been activated for: '.$this->enrolment->course?->name)
            ->line('Student number: '.($notifiable->studentProfile?->student_number ?? 'Pending'))
            ->action('Set up portal access', $this->activationUrl)
            ->line('This secure link expires in 7 days. Do not share it.')
            ->line('If you did not enrol at our academy, contact '.config('clinic.email').'.');
    }

    /**
     * @return array{title: string, message: string, action_url: string, category: string}
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Student portal invitation',
            'message' => 'Your enrolment for '.($this->enrolment->course?->name ?? 'your course').' is ready. Set up portal access to continue.',
            'action_url' => $this->activationUrl,
            'category' => 'invitation',
        ];
    }
}
