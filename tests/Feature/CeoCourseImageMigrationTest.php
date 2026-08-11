<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CeoCourseImageMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_ceo_courses_use_procedure_specific_default_images(): void
    {
        $expected = [
            'advance-aesthetics-class' => 'assets/web/images/academy/academy-advance-fillers.webp',
            'master-class-one' => 'assets/web/images/academy/academy-body-contouring.webp',
            'master-class-two' => 'assets/web/images/academy/academy-master-face.webp',
        ];

        foreach ($expected as $slug => $path) {
            $this->assertDatabaseHas('courses', [
                'slug' => $slug,
                'image_path' => $path,
            ]);
            $this->assertFileExists(public_path($path));
        }
    }

    public function test_image_refresh_migration_preserves_admin_managed_course_images(): void
    {
        Course::query()->where('slug', 'advance-aesthetics-class')->update([
            'image_path' => 'courses/admin-uploaded-advance.webp',
        ]);
        Course::query()->where('slug', 'master-class-one')->update([
            'image_path' => 'assets/web/images/hero/hero-body-care.webp',
        ]);
        Course::query()->where('slug', 'master-class-two')->update([
            'image_path' => null,
        ]);

        $migration = require database_path('migrations/2026_08_10_000000_refresh_ceo_course_default_images.php');
        $migration->up();

        $this->assertDatabaseHas('courses', [
            'slug' => 'advance-aesthetics-class',
            'image_path' => 'courses/admin-uploaded-advance.webp',
        ]);
        $this->assertDatabaseHas('courses', [
            'slug' => 'master-class-one',
            'image_path' => 'assets/web/images/academy/academy-body-contouring.webp',
        ]);
        $this->assertDatabaseHas('courses', [
            'slug' => 'master-class-two',
            'image_path' => 'assets/web/images/academy/academy-master-face.webp',
        ]);
    }
}
