<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseEnquiry;
use Illuminate\View\View;

class CourseEnquiryController extends Controller
{
    public function index(): View
    {
        $enquiries = CourseEnquiry::query()->with(['course', 'assignedTo'])->latest()->paginate(20);

        return view('admin.course-enquiries.index', compact('enquiries'));
    }

    public function show(CourseEnquiry $courseEnquiry): View
    {
        $courseEnquiry->load(['course', 'assignedTo', 'user']);

        return view('admin.course-enquiries.show', compact('courseEnquiry'));
    }
}
