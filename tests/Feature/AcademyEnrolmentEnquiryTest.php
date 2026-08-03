<?php

namespace Tests\Feature;

use App\Models\CourseEnquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademyEnrolmentEnquiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_enquiry_does_not_create_enrolment(): void
    {
        $response = $this->post(route('web.enrol.store'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+233552248636',
            'preferred_channel' => 'whatsapp',
            'preferred_date' => now()->addWeek()->toDateString(),
            'professional_background' => 'Beauty therapist',
            'message' => 'I want to ask about fees and class days.',
            'consent' => '1',
        ]);

        $response->assertRedirect(route('web.academy.student-portal.create'));
        $this->assertDatabaseHas('course_enquiries', [
            'email' => 'jane@example.com',
            'status' => 'submitted',
        ]);
        $this->assertDatabaseCount('enrolments', 0);
        $this->assertInstanceOf(CourseEnquiry::class, CourseEnquiry::query()->first());
    }
}
