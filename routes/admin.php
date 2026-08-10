<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AcademyShowcaseController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ConnectedIndexController;
use App\Http\Controllers\Admin\ConsultationController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CourseMaterialController;
use App\Http\Controllers\Admin\CourseEnquiryController;
use App\Http\Controllers\Admin\CourseSessionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmailLogController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\EnrolmentController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\LoyaltyController;
use App\Http\Controllers\Admin\ModulePlaceholderController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PhysicalEnrolmentController;
use App\Http\Controllers\Admin\PractitionerController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\ReferralController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\StudentSupportController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\TreatmentController;
use App\Http\Controllers\Admin\TreatmentCategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WebPageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active.account', 'admin.access'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])
            ->middleware('permission:dashboard.view')
            ->name('dashboard');

        Route::get('/profile', [AccountController::class, 'profile'])->name('profile.edit');
        Route::patch('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
        Route::get('/account', [AccountController::class, 'security'])->name('account.security');
        Route::delete('/account/sessions', [AccountController::class, 'destroyOtherSessions'])->name('account.sessions.destroy');

        Route::middleware('permission:audit.view')->group(function () {
            Route::get('/activity', [AuditLogController::class, 'index'])->name('activity.index');
            Route::get('/audit', [AuditLogController::class, 'index'])->name('audit.index');
        });

        Route::middleware('permission:consultations.view')->group(function () {
            Route::get('/consultations', [ConsultationController::class, 'index'])->name('consultations.index');
            Route::get('/consultations/{consultation}/edit', [ConsultationController::class, 'edit'])->name('consultations.edit');
            Route::put('/consultations/{consultation}', [ConsultationController::class, 'update'])->middleware('permission:consultations.respond')->name('consultations.update');
        });

        Route::middleware('permission:appointments.view')->group(function () {
            Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
            Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
            Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->middleware('permission:appointments.update')->name('appointments.update');
        });

        Route::middleware('permission:clients.view')->group(function () {
            Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
            Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
        });

        Route::middleware('permission:students.view')->get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::post('/students/{student}/approve', [StudentController::class, 'approve'])
            ->middleware('permission:students.activate')->name('students.approve');
        Route::delete('/students/{student}', [StudentController::class, 'destroy'])
            ->middleware('permission:students.archive')->name('students.destroy');
        Route::middleware('permission:courses.view')->get('/trainers', [ConnectedIndexController::class, 'trainers'])->name('trainers.index');
        Route::middleware('permission:course_enquiries.view')->group(function () {
            Route::get('/course-enquiries', [CourseEnquiryController::class, 'index'])->name('course-enquiries.index');
            Route::get('/course-enquiries/{courseEnquiry}', [CourseEnquiryController::class, 'show'])->name('course-enquiries.show');
            Route::put('/course-enquiries/{courseEnquiry}', [CourseEnquiryController::class, 'update'])->middleware('permission:course_enquiries.manage')->name('course-enquiries.update');
            Route::delete('/course-enquiries/{courseEnquiry}', [CourseEnquiryController::class, 'destroy'])->middleware('permission:course_enquiries.manage')->name('course-enquiries.destroy');
        });

        Route::middleware('permission:students.create')->group(function () {
            Route::get('/physical-enrolment/students/create', [PhysicalEnrolmentController::class, 'createStudent'])->name('physical-enrolment.students.create');
            Route::post('/physical-enrolment/students', [PhysicalEnrolmentController::class, 'storeStudent'])->name('physical-enrolment.students.store');
        });

        Route::middleware('permission:enrolments.create')->group(function () {
            Route::get('/physical-enrolment/create', [PhysicalEnrolmentController::class, 'create'])->name('physical-enrolment.create');
            Route::post('/physical-enrolment', [PhysicalEnrolmentController::class, 'store'])->name('physical-enrolment.store');
        });

        Route::post('/physical-enrolment/{enrolment}/activate', [PhysicalEnrolmentController::class, 'activate'])
            ->middleware('permission:enrolments.activate|enrolments.manage')
            ->name('physical-enrolment.activate');

        Route::middleware('permission:enrolments.manage')->group(function () {
            Route::get('/enrolments', [EnrolmentController::class, 'index'])->name('enrolments.index');
            Route::get('/enrolments/{enrolment}/edit', [EnrolmentController::class, 'edit'])->name('enrolments.edit');
            Route::put('/enrolments/{enrolment}', [EnrolmentController::class, 'update'])->name('enrolments.update');
        });

        Route::middleware('permission:materials.manage')->group(function () {
            Route::get('/course-materials/{courseMaterial}/download', [CourseMaterialController::class, 'download'])->name('course-materials.download');
            Route::resource('course-materials', CourseMaterialController::class)->except(['show']);
        });

        Route::middleware('permission:assessments.manage')->group(function () {
            Route::get('/assignments/{assignment}/download', [AssignmentController::class, 'download'])->name('assignments.download');
            Route::resource('assignments', AssignmentController::class)->except(['show']);
            Route::put('/assignment-submissions/{submission}', [AssignmentController::class, 'review'])->name('assignment-submissions.review');
            Route::get('/assignment-submissions/{submission}/download', [AssignmentController::class, 'downloadSubmission'])->name('assignment-submissions.download');
        });

        Route::middleware('permission:notifications.view')->group(function () {
            Route::get('/student-support', [StudentSupportController::class, 'index'])->name('student-support.index');
            Route::get('/student-support/{studentSupport}', [StudentSupportController::class, 'show'])->name('student-support.show');
            Route::put('/student-support/{studentSupport}', [StudentSupportController::class, 'update'])->name('student-support.update');
        });

        Route::middleware('permission:certificates.view')->group(function () {
            Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
            Route::get('/certificates/create', [CertificateController::class, 'create'])->middleware('permission:certificates.issue')->name('certificates.create');
            Route::post('/certificates', [CertificateController::class, 'store'])->middleware('permission:certificates.issue')->name('certificates.store');
            Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificates.download');
            Route::get('/certificates/{certificate}/edit', [CertificateController::class, 'edit'])->name('certificates.edit');
            Route::put('/certificates/{certificate}', [CertificateController::class, 'update'])->middleware('permission:certificates.issue')->name('certificates.update');
        });

        Route::middleware('permission:orders.view')->group(function () {
            Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
            Route::put('/orders/{order}', [OrderController::class, 'update'])->middleware('permission:orders.manage')->name('orders.update');
        });
        Route::middleware('permission:payments.view')->get('/payments', [ConnectedIndexController::class, 'payments'])->name('payments.index');
        Route::middleware('permission:users.manage')->group(function () {
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        });
        Route::middleware('permission:roles.manage')->get('/roles', [ConnectedIndexController::class, 'roles'])->name('roles.index');
        Route::middleware('permission:settings.manage')->get('/settings', [ConnectedIndexController::class, 'settings'])->name('settings.index');
        Route::middleware('permission:inventory.view')->get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::middleware('permission:inventory.adjust')->post('/inventory/{product}/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
        Route::middleware('permission:reports.view')->get('/reports', [ReportController::class, 'index'])->name('reports.index');

        Route::get('/academy-showcase', [AcademyShowcaseController::class, 'index'])
            ->middleware('permission:courses.view')
            ->name('academy-showcase.index');

        Route::get('/academy-showcase/create', [AcademyShowcaseController::class, 'create'])
            ->middleware('permission:courses.create')
            ->name('academy-showcase.create');
        Route::post('/academy-showcase', [AcademyShowcaseController::class, 'store'])
            ->middleware('permission:courses.create')
            ->name('academy-showcase.store');
        Route::get('/academy-showcase/{academyShowcase}/edit', [AcademyShowcaseController::class, 'edit'])
            ->middleware('permission:courses.update')
            ->name('academy-showcase.edit');
        Route::put('/academy-showcase/{academyShowcase}', [AcademyShowcaseController::class, 'update'])
            ->middleware('permission:courses.update')
            ->name('academy-showcase.update');
        Route::delete('/academy-showcase/{academyShowcase}', [AcademyShowcaseController::class, 'destroy'])
            ->middleware('permission:courses.delete')
            ->name('academy-showcase.destroy');

        Route::middleware('permission:courses.view')->group(function () {
            Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
            Route::get('/courses/create', [CourseController::class, 'create'])->middleware('permission:courses.create')->name('courses.create');
            Route::post('/courses', [CourseController::class, 'store'])->middleware('permission:courses.create')->name('courses.store');
            Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->middleware('permission:courses.update')->name('courses.edit');
            Route::put('/courses/{course}', [CourseController::class, 'update'])->middleware('permission:courses.update')->name('courses.update');
            Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->middleware('permission:courses.delete')->name('courses.destroy');
            Route::post('/courses/{course}/schedules', [CourseController::class, 'storeSchedule'])->middleware('permission:courses.update')->name('courses.schedules.store');
            Route::get('/course-schedules/{courseSchedule}/sessions', [CourseSessionController::class, 'index'])->name('course-schedules.sessions.index');
            Route::post('/course-schedules/{courseSchedule}/sessions', [CourseSessionController::class, 'store'])->middleware('permission:courses.update')->name('course-schedules.sessions.store');
        });

        Route::middleware('permission:practitioner_schedules.manage')->group(function () {
            Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
            Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
            Route::post('/schedules/blocked', [ScheduleController::class, 'storeBlocked'])->name('schedules.blocked.store');
            Route::delete('/schedules/blocked/{blockedDate}', [ScheduleController::class, 'destroyBlocked'])->name('schedules.blocked.destroy');
            Route::get('/schedules/{schedule}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
            Route::put('/schedules/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update');
            Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
        });

        Route::middleware('permission:practitioners.manage')->group(function () {
            Route::resource('practitioners', PractitionerController::class)->except(['show']);
        });

        Route::middleware('permission:products.manage')->group(function () {
            Route::resource('products', ProductController::class)->except(['show']);
        });

        Route::middleware('permission:settings.manage')->group(function () {
            Route::resource('branches', BranchController::class)->except(['show']);
        });

        Route::middleware('permission:gallery.manage')->group(function () {
            Route::resource('gallery', GalleryController::class)->parameters(['gallery' => 'gallery'])->except(['show']);
        });

        Route::middleware('permission:content.manage')->group(function () {
            Route::get('/content/homepage', [WebPageController::class, 'home'])->name('content.homepage');
            Route::get('/pages', [WebPageController::class, 'index'])->name('pages.index');
            Route::get('/pages/{page}/edit', [WebPageController::class, 'edit'])->name('pages.edit');
            Route::put('/pages/{page}', [WebPageController::class, 'update'])->name('pages.update');
            Route::get('/pages/{page}/preview', [WebPageController::class, 'preview'])->name('pages.preview');
            Route::resource('promotions', PromotionController::class)->except(['show']);
            Route::resource('testimonials', TestimonialController::class)->except(['show']);
        });

        Route::middleware('permission:blog.manage')->group(function () {
            Route::resource('blog', BlogPostController::class)->except(['show']);
        });

        Route::middleware('permission:loyalty.manage')->group(function () {
            Route::get('/loyalty', [LoyaltyController::class, 'index'])->name('loyalty.index');
            Route::post('/loyalty/{client}/adjust', [LoyaltyController::class, 'adjust'])->name('loyalty.adjust');
            Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals.index');
            Route::post('/referrals', [ReferralController::class, 'store'])->name('referrals.store');
            Route::put('/referrals/{referral}', [ReferralController::class, 'update'])->name('referrals.update');
        });

        Route::middleware('permission:notifications.view')->group(function () {
            Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
            Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
            Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
            Route::get('/email-templates', [EmailTemplateController::class, 'index'])->name('email-templates.index');
            Route::get('/email-templates/{emailTemplate}/edit', [EmailTemplateController::class, 'edit'])->name('email-templates.edit');
            Route::put('/email-templates/{emailTemplate}', [EmailTemplateController::class, 'update'])->name('email-templates.update');
            Route::get('/email-logs', [EmailLogController::class, 'index'])->name('email-logs.index');
            Route::post('/email-logs/{emailLog}/retry', [EmailLogController::class, 'retry'])->name('email-logs.retry');
        });

        Route::get('/treatments', [TreatmentController::class, 'index'])
            ->middleware('permission:treatments.view')->name('treatments.index');
        Route::get('/treatments/create', [TreatmentController::class, 'create'])
            ->middleware('permission:treatments.create')->name('treatments.create');
        Route::post('/treatments', [TreatmentController::class, 'store'])
            ->middleware('permission:treatments.create')->name('treatments.store');
        Route::get('/treatments/{treatment}/edit', [TreatmentController::class, 'edit'])
            ->middleware('permission:treatments.update')->name('treatments.edit');
        Route::put('/treatments/{treatment}', [TreatmentController::class, 'update'])
            ->middleware('permission:treatments.update')->name('treatments.update');
        Route::delete('/treatments/{treatment}', [TreatmentController::class, 'destroy'])
            ->middleware('permission:treatments.delete')->name('treatments.destroy');

        Route::get('/treatment-categories', [TreatmentCategoryController::class, 'index'])
            ->middleware('permission:treatments.view')->name('treatment-categories.index');
        Route::get('/treatment-categories/create', [TreatmentCategoryController::class, 'create'])
            ->middleware('permission:treatments.create')->name('treatment-categories.create');
        Route::post('/treatment-categories', [TreatmentCategoryController::class, 'store'])
            ->middleware('permission:treatments.create')->name('treatment-categories.store');
        Route::get('/treatment-categories/{treatmentCategory}/edit', [TreatmentCategoryController::class, 'edit'])
            ->middleware('permission:treatments.update')->name('treatment-categories.edit');
        Route::put('/treatment-categories/{treatmentCategory}', [TreatmentCategoryController::class, 'update'])
            ->middleware('permission:treatments.update')->name('treatment-categories.update');
        Route::delete('/treatment-categories/{treatmentCategory}', [TreatmentCategoryController::class, 'destroy'])
            ->middleware('permission:treatments.delete')->name('treatment-categories.destroy');

        $placeholders = [
            ['path' => 'attendance', 'name' => 'attendance.index', 'title' => 'Attendance'],
            ['path' => 'assessments', 'name' => 'assessments.index', 'title' => 'Assessments'],
            ['path' => 'deliveries', 'name' => 'deliveries.index', 'title' => 'Deliveries'],
            ['path' => 'reviews', 'name' => 'reviews.index', 'title' => 'Reviews'],
            ['path' => 'refunds', 'name' => 'refunds.index', 'title' => 'Refunds'],
            ['path' => 'faqs', 'name' => 'faqs.index', 'title' => 'FAQs'],
            ['path' => 'media', 'name' => 'media.index', 'title' => 'Media library'],
            ['path' => 'translations', 'name' => 'translations.index', 'title' => 'Translations'],
            ['path' => 'ai', 'name' => 'ai.index', 'title' => 'AI knowledge'],
        ];

        $navigationPermissions = collect(config('admin.navigation', []))
            ->flatMap(fn (array $group) => collect($group['items'] ?? []))
            ->mapWithKeys(fn (array $item) => [$item['route'] => $item['permission'] ?? null]);

        foreach ($placeholders as $item) {
            $route = Route::get('/'.$item['path'], ModulePlaceholderController::class)
                ->defaults('title', $item['title'])
                ->name($item['name']);

            if ($permission = $navigationPermissions->get($item['name'])) {
                $route->middleware('permission:'.$permission);
            }
        }
    });
