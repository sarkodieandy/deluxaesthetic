<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EnrolmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\Enrolment;
use App\Services\Notifications\InAppNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function store(Request $request, InAppNotificationService $notifications): RedirectResponse
    {
        $data = $this->validated($request);
        $data['file_path'] = $request->file('file')?->store('course-materials', 'academy_private');
        $material = CourseMaterial::query()->create($data);
        $student = $material->enrolment?->studentProfile?->user;
        if ($material->is_published && $student) {
            $notifications->notifyUser($student, [
                'title' => 'New course material available',
                'message' => $material->title.' is ready in your student materials.',
                'action_url' => route('student.materials.index', absolute: false),
                'category' => 'course_material',
            ]);
        }

        return redirect()->route('admin.course-materials.edit', $material)->with('status', 'Course material uploaded successfully.');
    }

    public function edit(CourseMaterial $courseMaterial): View
    {
        return $this->formView('admin.course-materials.edit', $courseMaterial);
    }

    public function update(Request $request, CourseMaterial $courseMaterial): RedirectResponse
    {
        $data = $this->validated($request, $courseMaterial);
        $oldPath = null;
        if ($request->hasFile('file')) {
            $oldPath = $courseMaterial->file_path;
            $data['file_path'] = $request->file('file')->store('course-materials', 'academy_private');
        }

        DB::transaction(function () use ($courseMaterial, $data, $oldPath): void {
            $courseMaterial->update($data);
            if ($oldPath && ($data['file_path'] ?? null) !== $oldPath) {
                DB::afterCommit(fn () => $this->deleteMaterialFile($oldPath));
            }
        });

        return back()->with('status', 'Course material updated successfully.');
    }

    public function destroy(CourseMaterial $courseMaterial): RedirectResponse
    {
        DB::transaction(function () use ($courseMaterial): void {
            $path = $courseMaterial->file_path;
            $courseMaterial->delete();
            DB::afterCommit(fn () => $this->deleteMaterialFile($path));
        });

        return redirect()->route('admin.course-materials.index')->with('status', 'Course material removed.');
    }

    public function download(CourseMaterial $courseMaterial): StreamedResponse
    {
        abort_unless($courseMaterial->file_path, 404);
        abort_unless(Storage::disk('academy_private')->exists($courseMaterial->file_path), 404);

        return Storage::disk('academy_private')->download(
            $courseMaterial->file_path,
            basename($courseMaterial->file_path),
        );
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
            'enrolment_id' => ['required', 'integer', Rule::exists('enrolments', 'id')->whereNull('deleted_at')],
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

    private function deleteMaterialFile(?string $path): void
    {
        if (! $path) {
            return;
        }

        Storage::disk('academy_private')->delete($path);
        Storage::disk('public')->delete($path);
    }
}
