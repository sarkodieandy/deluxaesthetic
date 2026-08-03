<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EnrolmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\Enrolment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CourseMaterialController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.course-materials.index', [
            'materials' => CourseMaterial::query()->with(['course', 'enrolment.studentProfile.user'])
                ->when($request->integer('course'), fn ($query, $course) => $query->where('course_id', $course))
                ->latest()->paginate(20)->withQueryString(),
            'courses' => Course::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(): View
    {
        return $this->formView('admin.course-materials.create', new CourseMaterial);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['file_path'] = $request->file('file')?->store('course-materials', 'public');
        $material = CourseMaterial::query()->create($data);

        return redirect()->route('admin.course-materials.edit', $material)->with('status', 'Course material uploaded successfully.');
    }

    public function edit(CourseMaterial $courseMaterial): View
    {
        return $this->formView('admin.course-materials.edit', $courseMaterial);
    }

    public function update(Request $request, CourseMaterial $courseMaterial): RedirectResponse
    {
        $data = $this->validated($request, $courseMaterial);
        if ($request->hasFile('file')) {
            if ($courseMaterial->file_path) {
                Storage::disk('public')->delete($courseMaterial->file_path);
            }
            $data['file_path'] = $request->file('file')->store('course-materials', 'public');
        }
        $courseMaterial->update($data);

        return back()->with('status', 'Course material updated successfully.');
    }

    public function destroy(CourseMaterial $courseMaterial): RedirectResponse
    {
        if ($courseMaterial->file_path) {
            Storage::disk('public')->delete($courseMaterial->file_path);
        }
        $courseMaterial->delete();

        return redirect()->route('admin.course-materials.index')->with('status', 'Course material removed.');
    }

    private function formView(string $view, CourseMaterial $material): View
    {
        return view($view, [
            'material' => $material,
            'courses' => Course::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'enrolments' => Enrolment::query()->with(['course:id,name', 'studentProfile.user:id,name,email'])
                ->whereIn('status', EnrolmentStatus::portalAccessStatuses())->latest()->get(),
        ]);
    }

    private function validated(Request $request, ?CourseMaterial $material = null): array
    {
        $data = $request->validate([
            'course_id' => ['required', 'integer', Rule::exists('courses', 'id')->whereNull('deleted_at')],
            'enrolment_id' => ['nullable', 'integer', Rule::exists('enrolments', 'id')->whereNull('deleted_at')],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'type' => ['required', Rule::in(['document', 'video', 'link', 'worksheet', 'guide'])],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,jpg,jpeg,png,mp4', 'max:51200'],
            'external_url' => ['nullable', 'url', 'max:2048'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        if ($data['enrolment_id'] ?? null) {
            $enrolment = Enrolment::query()->findOrFail($data['enrolment_id']);
            if ((int) $enrolment->course_id !== (int) $data['course_id']) {
                throw ValidationException::withMessages(['enrolment_id' => 'The selected student is not enrolled in this course.']);
            }
        }

        if (! $request->hasFile('file') && blank($data['external_url'] ?? null) && blank($material?->file_path)) {
            throw ValidationException::withMessages(['file' => 'Upload a file or provide an external resource URL.']);
        }

        $data['is_published'] = $request->boolean('is_published');
        unset($data['file']);

        return $data;
    }
}
