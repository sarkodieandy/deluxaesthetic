<?php

namespace Tests\Feature;

use App\Models\AcademyShowcaseItem;
use App\Models\Course;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademyCeoExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_academy_content_is_database_managed_while_countries_remain_home_only(): void
    {
        $this->assertSame(5, AcademyShowcaseItem::query()->where('type', 'training_country')->count());
        $this->assertSame(4, AcademyShowcaseItem::query()->where('type', 'training_step')->count());
        $this->assertSame(5, AcademyShowcaseItem::query()->where('type', 'career_benefit')->count());

        $ghana = AcademyShowcaseItem::query()
            ->where('type', 'training_country')
            ->where('title', 'Ghana')
            ->firstOrFail();
        $ghana->update(['body' => 'A managed Ghana Academy description.']);

        $this->get(route('web.academy.index'))
            ->assertOk()
            ->assertDontSee('A managed Ghana Academy description.')
            ->assertDontSee('id="countries"', false)
            ->assertSee('Clinical theory')
            ->assertSee('Customer service');

        $ghana->update(['is_active' => false]);

        $this->get(route('web.academy.index'))
            ->assertOk()
            ->assertDontSee('A managed Ghana Academy description.');
    }

    public function test_featured_courses_are_premium_paths_in_admin_order_and_secondary_courses_stay_secondary(): void
    {
        $basic = Course::query()->where('slug', 'basic-aesthetics-class')->firstOrFail();
        $masterTwo = Course::query()->where('slug', 'master-class-two')->firstOrFail();
        $basic->update(['sort_order' => 90]);
        $masterTwo->update(['sort_order' => 1]);

        $secondary = Course::query()->where('slug', 'non-injectables-class')->firstOrFail();
        $secondary->update(['is_featured' => false, 'name' => 'Secondary Skin Programme']);

        $response = $this->get(route('web.academy.index'))->assertOk();
        $html = $response->getContent();

        $this->assertLessThan(strpos($html, 'Basic Class'), strpos($html, 'Master Class 2'));
        $response->assertSee('Secondary Skin Programme');
        $response->assertSee('View outline');
        $response->assertSee(route('web.courses.show', $secondary->slug), false);
        $response->assertSee('class="academy-course-premium', false);
        $response->assertSee('What you will learn');
        $response->assertSee('loading="lazy"', false);
    }

    public function test_student_proof_video_certification_and_experience_render_in_distinct_sections(): void
    {
        AcademyShowcaseItem::query()->create([
            'type' => 'student_story',
            'title' => 'Past Student A',
            'body' => 'A verified graduate story.',
            'is_active' => true,
        ]);
        AcademyShowcaseItem::query()->create([
            'type' => 'skill_review',
            'title' => 'Student B',
            'body' => 'My practical injection mapping improved.',
            'is_active' => true,
        ]);
        AcademyShowcaseItem::query()->create([
            'type' => 'student_video',
            'title' => 'Student training reflection',
            'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
            'is_active' => true,
        ]);
        AcademyShowcaseItem::query()->create([
            'type' => 'certification',
            'title' => 'Academy Certificate',
            'subtitle' => 'Completion credential',
            'body' => 'Issued after the required practical assessment.',
            'is_active' => true,
        ]);
        AcademyShowcaseItem::query()->create([
            'type' => 'experience',
            'title' => 'Regional masterclass delivery',
            'subtitle' => 'West Africa',
            'body' => 'A verified Academy training milestone.',
            'is_active' => true,
        ]);
        AcademyShowcaseItem::query()->create([
            'type' => 'skill_review',
            'title' => 'Hidden review',
            'body' => 'This review must remain private.',
            'is_active' => false,
        ]);

        $this->get(route('web.academy.index'))
            ->assertOk()
            ->assertSee('id="past-students"', false)
            ->assertSee('id="skill-reviews"', false)
            ->assertSee('id="student-videos"', false)
            ->assertSee('youtube-nocookie.com/embed/dQw4w9WgXcQ', false)
            ->assertSee('Completion credential')
            ->assertSee('Issued after the required practical assessment.')
            ->assertSee('A verified Academy training milestone.')
            ->assertDontSee('This review must remain private.');
    }

    public function test_admin_requires_a_video_url_for_student_video_content(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Super Administrator');

        $this->actingAs($admin)->post(route('admin.academy-showcase.store'), [
            'type' => 'student_video',
            'title' => 'Missing video',
            'sort_order' => 10,
            'is_active' => '1',
        ])->assertSessionHasErrors('video_url');

        $this->assertDatabaseMissing('academy_showcase_items', ['title' => 'Missing video']);
    }

    public function test_course_detail_uses_managed_curriculum_and_physical_application_link(): void
    {
        $course = Course::query()->where('slug', 'basic-aesthetics-class')->firstOrFail();
        $course->update(['learning_outcomes' => [[
            'name' => 'Managed Practical Module',
            'topics' => ['Managed safety topic'],
        ]]]);

        $this->get(route('web.courses.show', $course->slug))
            ->assertOk()
            ->assertSee('Managed Practical Module')
            ->assertSee('Managed safety topic')
            ->assertSee('"@context":"https://schema.org"', false)
            ->assertDontSee('<?php', false)
            ->assertSee(route('web.academy.student-portal.create', ['course' => $course->id]), false);
    }
}
