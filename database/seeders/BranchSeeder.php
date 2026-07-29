<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        Branch::updateOrCreate(
            ['slug' => 'accra-flagship'],
            [
                'name' => 'Accra Flagship',
                'email' => config('clinic.email'),
                'phone' => config('clinic.phone'),
                'address_line_1' => 'Accra, Ghana',
                'city' => 'Accra',
                'region' => 'Greater Accra',
                'country' => 'Ghana',
                'opening_hours' => [
                    'monday' => ['09:00', '18:00'],
                    'tuesday' => ['09:00', '18:00'],
                    'wednesday' => ['09:00', '18:00'],
                    'thursday' => ['09:00', '18:00'],
                    'friday' => ['09:00', '18:00'],
                    'saturday' => ['10:00', '16:00'],
                    'sunday' => null,
                ],
                'is_active' => true,
                'is_primary' => true,
            ]
        );
    }
}
