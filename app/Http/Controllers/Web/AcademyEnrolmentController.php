<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academy\StoreEnrolmentEnquiryRequest;
use App\Models\Course;
use App\Models\CourseEnquiry;
use App\Services\Notifications\InAppNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AcademyEnrolmentController extends Controller
{
    public function create(): View
    {
        $courseId = request()->integer('course');

        return view('web.academy.enrol', [
            'courses' => Course::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'selectedCourseId' => $courseId ?: old('course_id'),
        ]);
    }

    public function store(
        StoreEnrolmentEnquiryRequest $request,
        InAppNotificationService $notifications,
    ): RedirectResponse
    {
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

        return redirect()
            ->route('web.enrol')
            ->with('status', 'Your course information request was received. Our admissions team will contact you about physical enrolment at the academy.');
    }
}
