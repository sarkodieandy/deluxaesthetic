<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Cleansers', 'description' => 'Facial cleansers and purifying washes'],
            ['name' => 'Toners & Essences', 'description' => 'Toners, mists, and essences'],
            ['name' => 'Serums & Treatments', 'description' => 'Targeted serums and clinical treatments'],
            ['name' => 'Moisturisers', 'description' => 'Day and night moisturisers and creams'],
            ['name' => 'Eye Care', 'description' => 'Eye creams and under-eye treatments'],
            ['name' => 'Sunscreen & SPF', 'description' => 'Daily SPF and sun protection'],
            ['name' => 'Masks & Exfoliants', 'description' => 'Face masks, peels, and exfoliators'],
            ['name' => 'Body Care', 'description' => 'Body lotions, oils, and spa body products'],
            ['name' => 'Hair Care', 'description' => 'Professional hair and scalp products'],
            ['name' => 'Nail Care', 'description' => 'Nail treatments, polish, and manicure care'],
            ['name' => 'Makeup & Cosmetics', 'description' => 'Clinic makeup and finishing cosmetics'],
            ['name' => 'Post-Treatment Care', 'description' => 'Aftercare for injectables, peels, and procedures'],
            ['name' => 'Devices & Tools', 'description' => 'At-home devices, rollers, and tools'],
            ['name' => 'Supplements', 'description' => 'Beauty and wellness supplements'],
            ['name' => 'Kits & Sets', 'description' => 'Curated product kits and gift sets'],
            ['name' => 'Professional / Backbar', 'description' => 'In-clinic professional-use products'],
            ['name' => 'Skincare Retail', 'description' => 'Professional retail skincare'],
        ];

        foreach ($categories as $category) {
            $slug = Str::slug($category['name']);

            ProductCategory::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
