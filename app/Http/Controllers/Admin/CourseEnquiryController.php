<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnquiry;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseEnquiryController extends Controller
{
    public function index(Request $request): View
    {
        $enquiries = CourseEnquiry::query()->with(['course', 'assignedTo'])
            ->when($request->filled('q'), fn ($query) => $query->where(fn ($inner) => $inner
                ->where('full_name', 'like', '%'.$request->string('q')->trim().'%')
                ->orWhere('email', 'like', '%'.$request->string('q')->trim().'%')
                ->orWhere('phone', 'like', '%'.$request->string('q')->trim().'%')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.course-enquiries.index', [
            'enquiries' => $enquiries,
            'statuses' => $this->statuses(),
        ]);
    }

    public function show(CourseEnquiry $courseEnquiry): View
    {
        $courseEnquiry->load(['course', 'assignedTo', 'user']);

        return view('admin.course-enquiries.show', [
            'courseEnquiry' => $courseEnquiry,
            'courses' => Course::query()->orderBy('name')->get(['id', 'name']),
            'staff' => User::query()->where('is_active', true)
                ->whereHas('roles', fn ($query) => $query->whereIn('name', config('admin.roles', [])))
                ->orderBy('name')->get(['id', 'name']),
            'statuses' => $this->statuses(),
        ]);
    }

    public function update(Request $request, CourseEnquiry $courseEnquiry): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'course_id' => ['nullable', Rule::exists('courses', 'id')->whereNull('deleted_at')],
            'preferred_training_date' => ['nullable', 'date'],
            'preferred_contact_method' => ['required', Rule::in(['whatsapp', 'phone', 'email'])],
            'professional_background' => ['nullable', 'string', 'max:1000'],
            'message' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in(array_keys($this->statuses()))],
            'assigned_to' => ['nullable', Rule::exists('users', 'id')->where('is_active', true)],
            'internal_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $courseEnquiry->update($data);

        return back()->with('status', 'Enquiry reviewed and updated successfully.');
    }

    public function destroy(CourseEnquiry $courseEnquiry): RedirectResponse
    {
        if ($courseEnquiry->converted_enrolment_id) {
            return back()->withErrors(['delete' => 'This enquiry has been converted to an enrolment and must be retained for the admissions record.']);
        }

        $courseEnquiry->delete();

        return redirect()->route('admin.course-enquiries.index')->with('status', 'Enquiry deleted successfully.');
    }

    /** @return array<string, string> */
    private function statuses(): array
    {
        return [
            'submitted' => 'New submission',
            'reviewing' => 'Under review',
            'contacted' => 'Applicant contacted',
            'qualified' => 'Qualified for enrolment',
            'converted' => 'Converted to student',
            'closed' => 'Closed',
        ];
    }
}
