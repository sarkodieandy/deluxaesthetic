<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentSupportRequest;
use App\Services\Notifications\InAppNotificationService;
use App\Services\Student\StudentPortalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SupportController extends Controller
{
    public function __construct(
        private readonly StudentPortalService $portal,
        private readonly InAppNotificationService $notifications,
    ) {}

    public function index(Request $request): View
    {
        $profileId = $request->user()->studentProfile?->id;

        return view('student.support.index', [
            'requests' => StudentSupportRequest::query()
                ->when($profileId, fn ($q) => $q->where('student_profile_id', $profileId))
                ->latest()
                ->paginate(15),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $profile = $request->user()->studentProfile;
        abort_unless($profile, 403);

        $data = $request->validate([
            'category' => ['required', 'string', 'max:80'],
            'subject' => ['required', 'string', 'max:190'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $enrolment = $this->portal->primaryEnrolment($request->user());

        $support = StudentSupportRequest::create([
            'reference' => 'SUP-'.Str::upper(Str::random(8)),
            'student_profile_id' => $profile->id,
            'enrolment_id' => $enrolment?->id,
            ...$data,
            'status' => 'open',
        ]);

        $this->notifications->notifyAdmins([
            'title' => 'Student support request',
            'message' => $request->user()->name.' submitted '.$support->reference.': '.$support->subject,
            'action_url' => route('admin.student-support.index', absolute: false),
            'category' => 'support',
        ]);

        $this->notifications->notifyUser($request->user(), [
            'title' => 'Support request received',
            'message' => 'We received '.$support->reference.'. Admissions will respond soon.',
            'action_url' => route('student.support.index', absolute: false),
            'category' => 'support',
        ]);

        return back()->with('status', 'Support request submitted. Admissions will respond soon.');
    }
}
