<?php

namespace App\Http\Middleware;

use App\Enums\EnrolmentStatus;
use App\Services\Student\StudentPortalService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveStudentEnrolment
{
    public function __construct(
        private readonly StudentPortalService $portal,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $enrolment = $this->portal->primaryEnrolment($request->user());

        if (! $enrolment || ! in_array($enrolment->status, EnrolmentStatus::fullMaterialAccessStatuses(), true)) {
            return redirect()
                ->route('student.dashboard')
                ->withErrors(['portal' => 'Your enrolment is not active for this section. Contact admissions.']);
        }

        $request->attributes->set('student_enrolment', $enrolment);

        return $next($request);
    }
}
