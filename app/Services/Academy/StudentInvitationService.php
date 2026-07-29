<?php

namespace App\Services\Academy;

use App\Models\Enrolment;
use App\Models\User;
use App\Notifications\StudentPortalInvitationNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StudentInvitationService
{
    public function sendPortalInvitation(User $user, Enrolment $enrolment, User $invitedBy): void
    {
        $profile = $user->studentProfile;
        if (! $profile) {
            return;
        }

        $token = Str::random(64);

        $profile->update([
            'invitation_token' => hash('sha256', $token),
            'invitation_expires_at' => now()->addDays(7),
            'portal_invited_at' => now(),
        ]);

        $user->notify(new StudentPortalInvitationNotification(
            enrolment: $enrolment,
            activationUrl: route('student.activate.show', ['token' => $token]),
            invitedBy: $invitedBy,
        ));

        DB::table('notification_delivery_logs')->insert([
            'user_id' => $user->id,
            'channel' => 'mail',
            'notification_type' => StudentPortalInvitationNotification::class,
            'status' => 'queued',
            'payload' => json_encode(['enrolment_id' => $enrolment->id]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
