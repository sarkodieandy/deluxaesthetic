<?php

namespace App\Listeners\Notifications;

use App\Events\EnrolmentActivated;
use App\Events\StudentAccountActivated;
use App\Services\Notifications\InAppNotificationService;

class SendPortalInAppNotifications
{
    public function __construct(
        private readonly InAppNotificationService $notifications,
    ) {}

    public function handleEnrolmentActivated(EnrolmentActivated $event): void
    {
        $enrolment = $event->enrolment->loadMissing(['studentProfile.user', 'course']);
        $student = $enrolment->studentProfile?->user;

        if ($student) {
            $this->notifications->notifyUser($student, [
                'title' => 'Enrolment activated',
                'message' => 'Your enrolment for '.($enrolment->course?->name ?? 'your course').' is now active. Open the student portal to view your schedule and materials.',
                'action_url' => route('student.dashboard', absolute: false),
                'category' => 'enrolment',
            ]);
        }

        $this->notifications->notifyAdmins([
            'title' => 'Enrolment activated',
            'message' => ($student?->name ?? 'A student').' was activated for '.($enrolment->course?->name ?? 'a course').' by '.$event->activatedBy->name.'.',
            'action_url' => route('admin.enrolments.index', absolute: false),
            'category' => 'enrolment',
        ]);
    }

    public function handleStudentAccountActivated(StudentAccountActivated $event): void
    {
        $this->notifications->notifyUser($event->user, [
            'title' => 'Student account ready',
            'message' => 'Your academy student account is active. Complete your profile and check your course workspace.',
            'action_url' => route('student.profile.edit', absolute: false),
            'category' => 'account',
        ]);
    }
}
