<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SecurityController extends Controller
{
    public function index(): View
    {
        return view('student.security.index');
    }
}
