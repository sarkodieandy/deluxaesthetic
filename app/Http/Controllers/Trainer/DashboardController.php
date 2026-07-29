<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('trainer.dashboard', [
            'user' => $user,
            'profile' => $user->trainerProfile,
            'metrics' => [
                'classes_today' => 0,
                'active_courses' => 0,
                'pending_assessments' => 0,
                'students_enrolled' => 0,
            ],
        ]);
    }
}
