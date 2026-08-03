<?php

use App\Http\Controllers\Student\ActivationController;
use App\Http\Controllers\Student\AssessmentController;
use App\Http\Controllers\Student\AssignmentController;
use App\Http\Controllers\Student\AttendanceController;
use App\Http\Controllers\Student\CalendarController;
use App\Http\Controllers\Student\CertificateController;
use App\Http\Controllers\Student\CourseController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\MaterialController;
use App\Http\Controllers\Student\NotificationController;
use App\Http\Controllers\Student\PaymentController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Student\SecurityController;
use App\Http\Controllers\Student\SupportController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('/student/activate/{token}', [ActivationController::class, 'show'])->name('student.activate.show');
    Route::post('/student/activate/{token}', [ActivationController::class, 'store'])->name('student.activate.store');
});

Route::middleware(['auth', 'active.account', 'student.role'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/course', [CourseController::class, 'show'])->name('course.show');
        Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
        Route::get('/materials', [MaterialController::class, 'index'])->name('materials.index');
        Route::get('/materials/{material}/download', [MaterialController::class, 'download'])->name('materials.download');
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');
        Route::get('/assignments/{assignment}/download', [AssignmentController::class, 'download'])->name('assignments.download');
        Route::post('/assignments/{assignment}/submit', [AssignmentController::class, 'submit'])->name('assignments.submit');
        Route::get('/assessments', [AssessmentController::class, 'index'])->name('assessments.index');
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/receipt/{payment}', [PaymentController::class, 'receipt'])->name('payments.receipt');
        Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificates.download');
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
        Route::get('/support', [SupportController::class, 'index'])->name('support.index');
        Route::post('/support', [SupportController::class, 'store'])->name('support.store');
        Route::get('/security', [SecurityController::class, 'index'])->name('security.index');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    });
