<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Academy\StoreCourseRequest;
use App\Http\Requests\Admin\Academy\UpdateCourseRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View
    {
        $courses = DB::table('courses')->whereNull('deleted_at')->latest('created_at')->paginate(15);

        return view('admin.courses.index', compact('courses'));
    }

    public function create(): View
    {
        return view('admin.courses.create');
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $categoryId = $this->resolveCategory($data['category_name']);

        DB::table('courses')->insert([
            'course_category_id' => $categoryId,
            'trainer_profile_id' => null,
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name']),
            'description' => $data['description'] ?? null,
            'entry_requirements' => $data['entry_requirements'] ?? null,
            'delivery_mode' => 'physical',
            'duration_hours' => (int) $data['duration_hours'],
            'venue' => $data['venue'] ?? null,
            'max_students' => (int) ($data['max_students'] ?? 20),
            'waiting_list_capacity' => (int) ($data['waiting_list_capacity'] ?? 5),
            'fee' => $data['fee'],
            'deposit_amount' => $data['deposit_amount'] ?? null,
            'image_path' => $request->file('image')?->store('courses', 'public'),
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active', true),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.courses.index')->with('status', 'Course added successfully.');
    }

    public function edit(int $course): View
    {
        $course = DB::table('courses')->where('id', $course)->whereNull('deleted_at')->firstOrFail();
        $category = DB::table('course_categories')->where('id', $course->course_category_id)->first();
        $schedules = DB::table('course_schedules')
            ->where('course_id', $course->id)
            ->orderByDesc('starts_on')
            ->get();

        return view('admin.courses.edit', compact('course', 'category', 'schedules'));
    }

    public function update(UpdateCourseRequest $request, int $course): RedirectResponse
    {
        $record = DB::table('courses')->where('id', $course)->whereNull('deleted_at')->firstOrFail();
        $data = $request->validated();
        $categoryId = $this->resolveCategory($data['category_name']);

        $payload = [
            'course_category_id' => $categoryId,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'entry_requirements' => $data['entry_requirements'] ?? null,
            'duration_hours' => (int) $data['duration_hours'],
            'venue' => $data['venue'] ?? null,
            'max_students' => (int) ($data['max_students'] ?? 20),
            'waiting_list_capacity' => (int) ($data['waiting_list_capacity'] ?? 5),
            'fee' => $data['fee'],
            'deposit_amount' => $data['deposit_amount'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active', true),
            'updated_at' => now(),
        ];

        if ($request->hasFile('image')) {
            $payload['image_path'] = $request->file('image')->store('courses', 'public');
        }

        DB::table('courses')->where('id', $record->id)->update($payload);

        return redirect()->route('admin.courses.index')->with('status', 'Course updated successfully.');
    }

    public function destroy(int $course): RedirectResponse
    {
        DB::table('courses')->where('id', $course)->update(['deleted_at' => now()]);

        return redirect()->route('admin.courses.index')->with('status', 'Course removed.');
    }

    private function resolveCategory(string $name): int
    {
        $slug = Str::slug($name) ?: 'courses';
        $existing = DB::table('course_categories')->where('slug', $slug)->first();
        if ($existing) return $existing->id;

        return DB::table('course_categories')->insertGetId([
            'name' => $name,
            'slug' => $slug,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'course';
        $slug = $base;
        $i = 2;
        while (DB::table('courses')->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }
        return $slug;
    }
}
