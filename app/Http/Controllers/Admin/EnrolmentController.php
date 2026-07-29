<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Academy\UpdateEnrolmentRequest;
use App\Models\Enrolment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EnrolmentController extends Controller
{
    public function index(): View
    {
        $enrolments = DB::table('enrolments')
            ->join('student_profiles', 'student_profiles.id', '=', 'enrolments.student_profile_id')
            ->join('users', 'users.id', '=', 'student_profiles.user_id')
            ->join('courses', 'courses.id', '=', 'enrolments.course_id')
            ->select('enrolments.*', 'users.name as student_name', 'courses.name as course_name')
            ->whereNull('enrolments.deleted_at')
            ->latest('enrolments.created_at')
            ->paginate(20);

        return view('admin.enrolments.index', compact('enrolments'));
    }

    public function edit(Enrolment $enrolment): View
    {
        $enrolment->load(['studentProfile.user', 'course']);

        return view('admin.enrolments.edit', [
            'enrolment' => $enrolment,
            'studentName' => $enrolment->studentProfile?->user?->name,
            'studentEmail' => $enrolment->studentProfile?->user?->email,
            'courseName' => $enrolment->course?->name,
        ]);
    }

    public function update(UpdateEnrolmentRequest $request, Enrolment $enrolment): RedirectResponse
    {
        $data = $request->validated();
        $previousStatus = $enrolment->status;

        DB::transaction(function () use ($data, $enrolment, $request, $previousStatus) {
            $enrolment->update([
                'status' => $data['status'],
                'amount_paid' => $data['amount_paid'] ?? $enrolment->amount_paid,
                'outstanding_balance' => $data['outstanding_balance'] ?? $enrolment->outstanding_balance,
                'confirmed_at' => $data['status'] === 'active' ? ($enrolment->confirmed_at ?? now()) : $enrolment->confirmed_at,
            ]);

            if ($previousStatus !== $data['status']) {
                DB::table('enrolment_status_histories')->insert([
                    'enrolment_id' => $enrolment->id,
                    'from_status' => $previousStatus,
                    'to_status' => $data['status'],
                    'changed_by' => $request->user()?->id,
                    'notes' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()->route('admin.enrolments.edit', $enrolment)->with('status', 'Enrolment updated successfully.');
    }
}
