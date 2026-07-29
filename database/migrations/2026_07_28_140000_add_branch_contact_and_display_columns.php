<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->string('whatsapp')->nullable()->after('phone');
            $table->string('address_line_2')->nullable()->after('address_line_1');
            $table->string('postal_code')->nullable()->after('region');
            $table->text('map_embed_url')->nullable()->after('longitude');
            $table->string('hours_summary')->nullable()->after('opening_hours');
            $table->unsignedInteger('sort_order')->default(10)->after('is_primary');
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp',
                'address_line_2',
                'postal_code',
                'map_embed_url',
                'hours_summary',
                'sort_order',
            ]);
        });
    }
};
