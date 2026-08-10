<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_pages', function (Blueprint $table): void {
            $table->text('hero_image_url')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('web_pages', function (Blueprint $table): void {
            $table->string('hero_image_url')->nullable()->change();
        });
    }
};
