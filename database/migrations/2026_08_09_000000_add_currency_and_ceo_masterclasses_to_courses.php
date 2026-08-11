<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('currency', 3)->default('GHS')->after('fee');
            $table->unsignedInteger('sort_order')->default(100)->after('currency');
        });

        $now = now();
        $categories = [];

        foreach ([
            'injectable-masterclasses' => 'Injectable Masterclasses',
            'skin-regeneration' => 'Skin & Regeneration',
        ] as $slug => $name) {
            $category = DB::table('course_categories')->where('slug', $slug)->first();
            if ($category) {
                DB::table('course_categories')->where('id', $category->id)->update([
                    'name' => $name,
                    'is_active' => true,
                    'deleted_at' => null,
                    'updated_at' => $now,
                ]);
                $categories[$slug] = $category->id;
            } else {
                $categories[$slug] = DB::table('course_categories')->insertGetId([
                    'name' => $name,
                    'slug' => $slug,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $courses = [
            [
                'category' => 'injectable-masterclasses',
                'name' => 'Basic Class',
                'slug' => 'basic-aesthetics-class',
                'description' => 'Your entry point into injectable aesthetics, covering the three core Botox zones and skin booster fundamentals.',
                'fee' => 1200,
                'image_path' => 'assets/web/images/academy/academy-botox.webp',
                'sort_order' => 10,
                'outcomes' => [
                    ['name' => 'Crows Feet Botox', 'topics' => ['Periorbital Anatomy and Muscle Assessment', 'Injection Techniques and Dosage', 'Complication Management and Aftercare']],
                    ['name' => 'Glabella Botox', 'topics' => ['Glabellar Anatomy and Facial Assessment', 'Injection Protocols and Safety', 'Managing Outcomes and Complications']],
                    ['name' => 'Frontalis Botox', 'topics' => ['Forehead Anatomy and Patient Evaluation', 'Injection Mapping and Treatment Techniques', 'Complication Prevention and Follow-up']],
                    ['name' => 'Skin Boosters', 'topics' => ['Skin Booster Fundamentals', 'Injection Techniques and Treatment Protocols', 'Aftercare, Results, and Complication Management']],
                ],
            ],
            [
                'category' => 'skin-regeneration',
                'name' => 'Non-Injectables',
                'slug' => 'non-injectables-class',
                'description' => 'Regenerative and corrective skin protocols for texture, pigmentation, stretch marks and renewal.',
                'fee' => 1200,
                'image_path' => 'assets/web/images/academy/academy-skin.webp',
                'sort_order' => 20,
                'outcomes' => [
                    ['name' => 'PRP for Stretch Marks', 'topics' => ['PRP Fundamentals and Patient Selection', 'PRP Preparation & Application Techniques for Stretch Marks', 'Treatment Protocols, Combination Therapies & Expected Outcomes']],
                    ['name' => 'Hyperpigmentation', 'topics' => ['Causes and Types of Hyperpigmentation', 'Clinical Assessment & Treatment Planning', 'Professional Treatment Protocols and Homecare']],
                    ['name' => 'Microneedling', 'topics' => ['Skin Anatomy and Microneedling Fundamentals', 'Device Selection, Needle Depth & Treatment Protocols', 'Combination Therapies, Aftercare & Complication Prevention']],
                    ['name' => 'Mesotherapy', 'topics' => ['Principles of Mesotherapy & Skin Anatomy', 'Mesotherapy Products, Protocols & Techniques', 'Treatment Planning, Safety & Post-Treatment Care']],
                    ['name' => 'Chemical Peel', 'topics' => ['Skin Assessment & Patient Selection', 'Types of Chemical Peels and Indications', 'Peel Application, Aftercare & Complication Management']],
                ],
            ],
            [
                'category' => 'injectable-masterclasses',
                'name' => 'Advance Class',
                'slug' => 'advance-aesthetics-class',
                'description' => 'Precision injectable techniques for complex facial zones, advanced fillers, therapeutic Botox and contouring.',
                'fee' => 2000,
                'image_path' => 'assets/web/images/academy/academy-advance-fillers.webp',
                'sort_order' => 30,
                'outcomes' => [
                    ['name' => 'Migraine Botox', 'topics' => ['Patient Assessment and Head & Neck Anatomy', 'Injection Protocols and Treatment Techniques', 'Post-Treatment Care and Complication Management']],
                    ['name' => 'Nasolabial Fold Filler', 'topics' => ['Facial Anatomy and Patient Assessment', 'Injection Techniques and Product Selection', 'Complication Prevention and Management']],
                    ['name' => 'Tear Trough Filler', 'topics' => ['Periorbital Anatomy and Patient Selection', 'Injection Techniques and Safety', 'Complication Management and Aftercare']],
                    ['name' => 'Chin Contouring', 'topics' => ['Chin Anatomy and Facial Proportions', 'Injection Techniques and Treatment Planning', 'Complication Prevention and Post-Treatment Care']],
                ],
            ],
            [
                'category' => 'injectable-masterclasses',
                'name' => 'Master Class 1',
                'slug' => 'master-class-one',
                'description' => 'Expert-level body contouring with butt and hand fillers, fat dissolving and hyaluronidase management.',
                'fee' => 3500,
                'image_path' => 'assets/web/images/academy/academy-body-contouring.webp',
                'sort_order' => 40,
                'outcomes' => [
                    ['name' => 'Butt Fillers', 'topics' => ['Patient Assessment and Gluteal Anatomy', 'Injection Techniques and Treatment Protocols', 'Complication Prevention and Post-Treatment Care']],
                    ['name' => 'Hand Fillers', 'topics' => ['Hand Anatomy and Ageing Assessment', 'Injection Techniques and Product Selection', 'Complication Management and Aftercare']],
                    ['name' => 'Fat Dissolver', 'topics' => ['Patient Consultation and Treatment Planning', 'Injection Techniques and Clinical Protocols', 'Managing Results and Complications']],
                    ['name' => 'Filler Dissolver', 'topics' => ['Hyaluronidase Fundamentals and Patient Assessment', 'Dissolving Techniques and Treatment Protocols', 'Post-Treatment Care and Complication Management']],
                ],
            ],
            [
                'category' => 'injectable-masterclasses',
                'name' => 'Master Class 2',
                'slug' => 'master-class-two',
                'description' => 'A complete facial filler and thread-lifting programme covering PDO, lip, nose and mono thread techniques.',
                'fee' => 3500,
                'image_path' => 'assets/web/images/academy/academy-master-face.webp',
                'sort_order' => 50,
                'outcomes' => [
                    ['name' => 'PDO Face Lift', 'topics' => ['Facial Anatomy and Patient Assessment', 'PDO Thread Lifting Techniques', 'Complication Prevention and Post-Treatment Care']],
                    ['name' => 'Lip Fillers', 'topics' => ['Lip Anatomy and Facial Assessment', 'Injection Techniques and Lip Enhancement', 'Complication Management and Aftercare']],
                    ['name' => 'Nose Fillers', 'topics' => ['Nasal Anatomy and Patient Evaluation', 'Injection Techniques and Nose Contouring', 'Complication Prevention and Emergency Management']],
                    ['name' => 'Mono PDO Thread', 'topics' => ['Skin Anatomy and Patient Assessment', 'Mono Thread Insertion Techniques', 'Complication Management and Maintenance']],
                ],
            ],
        ];

        foreach ($courses as $course) {
            $existing = DB::table('courses')->where('slug', $course['slug'])->first();
            if ($existing) {
                // Existing records may already contain production-owned copy and
                // imagery. Fill only missing editorial fields while applying the
                // confirmed CEO package fee/currency and deterministic ordering.
                DB::table('courses')->where('id', $existing->id)->update([
                    'description' => $existing->description ?: $course['description'],
                    'learning_outcomes' => $existing->learning_outcomes
                        ?: json_encode($course['outcomes'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'delivery_mode' => $existing->delivery_mode ?: 'physical',
                    'duration_hours' => max(1, (int) ($existing->duration_hours ?: 8)),
                    'max_students' => max(1, (int) ($existing->max_students ?: 20)),
                    'waiting_list_capacity' => max(0, (int) ($existing->waiting_list_capacity ?? 5)),
                    'fee' => $course['fee'],
                    'currency' => 'USD',
                    'sort_order' => $course['sort_order'],
                    'image_path' => $existing->image_path ?: $course['image_path'],
                    'updated_at' => $now,
                ]);
            } else {
                DB::table('courses')->insert([
                    'course_category_id' => $categories[$course['category']],
                    'trainer_profile_id' => null,
                    'name' => $course['name'],
                    'slug' => $course['slug'],
                    'description' => $course['description'],
                    'learning_outcomes' => json_encode($course['outcomes'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'delivery_mode' => 'physical',
                    'duration_hours' => 8,
                    'venue' => null,
                    'max_students' => 20,
                    'waiting_list_capacity' => 5,
                    'fee' => $course['fee'],
                    'currency' => 'USD',
                    'sort_order' => $course['sort_order'],
                    'image_path' => $course['image_path'],
                    'is_featured' => true,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['currency', 'sort_order']);
        });
    }
};
