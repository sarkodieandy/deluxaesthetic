<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\PractitionerProfile;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SettingsSeeder::class,
            EmailTemplateSeeder::class,
            BranchSeeder::class,
            DemoUserSeeder::class,
            ClinicDemoSeeder::class,
            ProductCategorySeeder::class,
            DemoCertificateSeeder::class,
            AdminSidebarDemoSeeder::class,
        ]);
    }
}
