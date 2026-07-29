<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academy\StoreStudentPortalRegistrationRequest;
use App\Services\Academy\StudentPortalRegistrationService;
use App\Support\PortalRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentPortalRegistrationController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()?->hasRole('Student')) {
            return redirect()->route('student.dashboard');
        }

        return view('web.academy.index');
    }

    public function store(
        StoreStudentPortalRegistrationRequest $request,
        StudentPortalRegistrationService $registration,
    ): RedirectResponse {
        $user = $registration->register($request->validated());

        auth()->login($user);

        return PortalRedirect::afterRegistration($user)
            ->with('status', __('web.student_portal.register_success'));
    }
}
