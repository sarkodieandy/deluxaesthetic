<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View
    {
        $courses = Course::query()
            ->with(['category', 'trainer.user'])
            ->where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->get();

        return view('web.courses.index', compact('courses'));
    }

    public function show(string $slug): View
    {
        $course = Course::query()
            ->with(['category', 'trainer.user'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('web.courses.show', compact('course'));
    }
}
