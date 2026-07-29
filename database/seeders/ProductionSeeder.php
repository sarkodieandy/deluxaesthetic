<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Safe first-deploy seeder for production. Does not create demo users or clinic demo data.
 */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SettingsSeeder::class,
            EmailTemplateSeeder::class,
            BranchSeeder::class,
            ProductCategorySeeder::class,
        ]);
    }
}
