<?php

namespace Database\Seeders;

use App\Models\TreatmentCategory;
use Illuminate\Database\Seeder;

class ClinicalCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Spa Therapy',
                'slug' => 'spa-therapy',
                'description' => 'Restorative spa and wellness therapies delivered in a calm professional setting.',
                'image_path' => 'assets/web/images/hero/hero-spa-massage.webp',
                'sort_order' => 10,
            ],
            [
                'name' => 'Facial & Skin Treatments',
                'slug' => 'facial-treatments',
                'description' => 'Consultation-led facial and advanced skin protocols tailored to individual needs.',
                'image_path' => 'assets/web/images/treatments/facial-care.webp',
                'sort_order' => 20,
            ],
            [
                'name' => 'Body Procedures',
                'slug' => 'body-treatments',
                'description' => 'Professional body care and contour-focused procedures guided by careful assessment.',
                'image_path' => 'assets/web/images/hero/hero-body-care.webp',
                'sort_order' => 30,
            ],
            [
                'name' => 'Injectable Procedures',
                'slug' => 'injectable-treatments',
                'description' => 'Consultation-led injectable procedures planned around suitability, safety and natural-looking outcomes.',
                'image_path' => 'assets/web/images/hero/hero-botox.webp',
                'sort_order' => 40,
            ],
        ];

        foreach ($categories as $category) {
            // Never overwrite an administrator's later edits, visibility choice, or archived record.
            if (TreatmentCategory::withTrashed()->where('slug', $category['slug'])->exists()) {
                continue;
            }

            TreatmentCategory::create($category + ['is_active' => true]);
        }
    }
}
