<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\PractitionerProfile;
use App\Models\PractitionerSchedule;
use App\Models\Treatment;
use App\Models\TreatmentCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ClinicDemoSeeder extends Seeder
{
    public function run(): void
    {
        $facial = TreatmentCategory::updateOrCreate(
            ['slug' => 'facial-treatments'],
            ['name' => 'Facial Treatments', 'description' => 'Clinical facial protocols', 'sort_order' => 1, 'is_active' => true]
        );

        $body = TreatmentCategory::updateOrCreate(
            ['slug' => 'body-treatments'],
            ['name' => 'Body Treatments', 'description' => 'Body contour and restorative care', 'sort_order' => 2, 'is_active' => true]
        );

        $treatments = [
            [
                'category' => $facial,
                'name' => 'Signature Clinical Facial',
                'short_description' => 'Barrier-focused facial with clinical assessment.',
                'price' => 450,
                'duration_minutes' => 60,
                'is_featured' => true,
            ],
            [
                'category' => $facial,
                'name' => 'Clarity Protocol',
                'short_description' => 'Targeted clarity programme for congested skin.',
                'price' => 520,
                'duration_minutes' => 75,
                'is_featured' => true,
            ],
            [
                'category' => $body,
                'name' => 'Body Contour Care',
                'short_description' => 'Structured body treatment with measured protocols.',
                'price' => 680,
                'duration_minutes' => 90,
                'is_featured' => true,
            ],
        ];

        $ceo = PractitionerProfile::query()->where('is_ceo', true)->first();
        $branch = Branch::query()->where('is_primary', true)->first();

        foreach ($treatments as $item) {
            $treatment = Treatment::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'treatment_category_id' => $item['category']->id,
                    'name' => $item['name'],
                    'short_description' => $item['short_description'],
                    'description' => $item['short_description'].' Delivered by trained practitioners at De Lux Aesthetic Clinic.',
                    'benefits' => ['Professional assessment', 'Clear aftercare guidance', 'Measured clinical protocol'],
                    'preparation_instructions' => 'Arrive with clean skin and share relevant medical history with your practitioner.',
                    'aftercare_instructions' => 'Follow the written aftercare plan provided after your session.',
                    'duration_minutes' => $item['duration_minutes'],
                    'price' => $item['price'],
                    'deposit_amount' => round($item['price'] * 0.3, 2),
                    'buffer_after_minutes' => 15,
                    'is_featured' => $item['is_featured'],
                    'is_active' => true,
                ]
            );

            if ($ceo) {
                $treatment->practitioners()->syncWithoutDetaching([$ceo->id]);
            }
        }

        if ($ceo && $branch) {
            foreach ([1, 2, 3, 4, 5] as $day) {
                PractitionerSchedule::updateOrCreate(
                    [
                        'practitioner_profile_id' => $ceo->id,
                        'branch_id' => $branch->id,
                        'day_of_week' => $day,
                        'starts_at' => '09:00:00',
                        'ends_at' => '17:00:00',
                    ],
                    ['is_active' => true]
                );
            }
        }
    }
}
