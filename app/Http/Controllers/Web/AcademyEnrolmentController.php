<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academy\StoreEnrolmentEnquiryRequest;
use App\Models\CourseEnquiry;
use App\Services\Messaging\EmailNotificationService;
use App\Services\Notifications\InAppNotificationService;
use Illuminate\Http\RedirectResponse;

class AcademyEnrolmentController extends Controller
{
    public function create(): RedirectResponse
    {
        $courseId = request()->integer('course');

        return redirect()->route('web.academy.student-portal.create', array_filter([
            'course' => $courseId ?: null,
        ]));
    }

    public function store(
        StoreEnrolmentEnquiryRequest $request,
        InAppNotificationService $notifications,
        EmailNotificationService $email,
    ): RedirectResponse {
        $data = $request->validated();

        $enquiry = CourseEnquiry::create([
            'course_id' => $data['course_id'] ?? null,
            'user_id' => $request->user()?->id,
            'full_name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'preferred_training_date' => $data['preferred_date'] ?? null,
            'professional_background' => $data['professional_background'] ?? null,
            'preferred_contact_method' => $data['preferred_channel'],
            'message' => $data['message'],
            'privacy_consent' => true,
            'status' => 'submitted',
        ]);

        $notifications->notifyAdmins([
            'title' => 'New academy training enquiry',
            'message' => $enquiry->full_name.' submitted an academy enquiry'.($enquiry->course?->name ? ' for '.$enquiry->course->name : '').'.',
            'action_url' => route('admin.course-enquiries.show', $enquiry, absolute: false),
            'category' => 'academy_enquiry',
        ]);

        $email->queueTo(
            'academy.application_received',
            $enquiry->email,
            $enquiry->full_name,
            ['recipient_name' => $enquiry->full_name],
            related: $enquiry,
        );

        return redirect()
            ->route('web.academy.student-portal.create')
            ->with('status', 'Your course information request was received. Our admissions team will contact you about physical enrolment at the academy.');
    }
}
