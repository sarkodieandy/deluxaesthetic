<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Enrolment;
use App\Models\User;
use App\Services\Academy\CertificateIssuanceService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoCertificateSeeder extends Seeder
{
    public function run(): void
    {
        $studentUser = User::query()->where('email', env('SEED_STUDENT_EMAIL', 'student@deluxaesthetic.test'))->first();

        if (! $studentUser?->studentProfile) {
            $this->command?->warn('Demo student not found. Run DemoUserSeeder first.');

            return;
        }

        $profile = $studentUser->studentProfile;

        $existingEnrolment = Enrolment::query()
            ->where('reference', 'ENR-DEMO-CERT-001')
            ->first();

        if ($existingEnrolment?->hasIssuedCertificate()) {
            $certificate = $existingEnrolment->certificates()->where('status', 'issued')->first();
            $this->printPreviewInstructions($certificate, $studentUser);

            return;
        }

        $category = CourseCategory::updateOrCreate(
            ['slug' => 'professional-aesthetics'],
            [
                'name' => 'Professional Aesthetics',
                'level' => 'CPD',
                'description' => 'Professional aesthetic training programmes.',
                'is_active' => true,
            ]
        );

        $course = Course::updateOrCreate(
            ['slug' => 'advanced-facial-aesthetics'],
            [
                'course_category_id' => $category->id,
                'name' => 'Advanced Facial Aesthetics',
                'description' => 'Hands-on facial aesthetics programme with clinical assessment and professional standards.',
                'entry_requirements' => 'Basic beauty or healthcare background recommended.',
                'delivery_mode' => 'physical',
                'duration_hours' => 24,
                'venue' => 'De Lux Training Academy, East Legon',
                'max_students' => 12,
                'waiting_list_capacity' => 4,
                'fee' => 3500,
                'deposit_amount' => 1400,
                'is_featured' => true,
                'is_active' => true,
            ]
        );

        $scheduleId = DB::table('course_schedules')->updateOrInsert(
            [
                'course_id' => $course->id,
                'starts_on' => now()->subMonths(2)->toDateString(),
            ],
            [
                'branch_id' => null,
                'ends_on' => now()->subWeeks(2)->toDateString(),
                'start_time' => '09:00:00',
                'end_time' => '16:00:00',
                'capacity' => 12,
                'enrolled_count' => 1,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $schedule = DB::table('course_schedules')
            ->where('course_id', $course->id)
            ->where('starts_on', now()->subMonths(2)->toDateString())
            ->first();

        if (! $schedule) {
            $this->command?->error('Could not create demo course schedule.');

            return;
        }

        $enrolment = Enrolment::updateOrCreate(
            ['reference' => 'ENR-DEMO-CERT-001'],
            [
                'student_profile_id' => $profile->id,
                'course_id' => $course->id,
                'course_schedule_id' => $schedule->id,
                'status' => 'completed',
                'fee' => 3500,
                'amount_paid' => 3500,
                'outstanding_balance' => 0,
                'currency' => 'GHS',
                'policies_accepted' => true,
                'confirmed_at' => now()->subMonths(2),
            ]
        );

        /** @var CertificateIssuanceService $issuer */
        $issuer = app(CertificateIssuanceService::class);

        $certificate = $issuer->createForEnrolment($enrolment, [
            'completion_date' => now()->subWeeks(2)->toDateString(),
            'signatory' => config('clinic.ceo.name', 'Dr Evelyn Ejaife'),
            'issue' => true,
        ]);

        $this->printPreviewInstructions($certificate, $studentUser);
    }

    private function printPreviewInstructions(?Certificate $certificate, User $studentUser): void
    {
        if (! $certificate) {
            return;
        }

        $pdfUrl = $certificate->pdf_path
            ? url('storage/'.$certificate->pdf_path)
            : '(PDF not generated)';

        $this->command?->newLine();
        $this->command?->info('Demo certificate ready.');
        $this->command?->line('  Certificate number: '.$certificate->number);
        $this->command?->line('  Student: '.$certificate->student_name);
        $this->command?->line('  Course: '.$certificate->course_name);
        $this->command?->line('  PDF URL: '.$pdfUrl);
        $this->command?->newLine();
        $this->command?->line('View in admin: '.url('/admin/certificates/'.$certificate->id.'/edit'));
        $this->command?->line('Download (admin): '.url('/admin/certificates/'.$certificate->id.'/download'));
        $this->command?->line('Student portal: '.url('/student/certificates'));
        $this->command?->line('  Login: '.$studentUser->email.' / your SEED_STUDENT_PASSWORD (or shared seed password)');
        $this->command?->newLine();
    }
}
