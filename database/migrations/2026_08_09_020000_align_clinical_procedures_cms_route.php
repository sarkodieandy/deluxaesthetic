<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('web_pages')
            ->where('route_name', 'web.treatments.index')
            ->update([
                'name' => 'Clinical Procedures',
                'slug' => 'clinical-procedures',
                'route_name' => 'web.clinical.index',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('web_pages')
            ->where('route_name', 'web.clinical.index')
            ->update([
                'name' => 'Treatments',
                'slug' => 'treatments',
                'route_name' => 'web.treatments.index',
                'updated_at' => now(),
            ]);
    }
};
