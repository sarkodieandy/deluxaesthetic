<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('treatment_categories', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('description');
        });

        Schema::table('treatments', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(10)->after('is_featured');
            $table->index(['treatment_category_id', 'is_active', 'sort_order'], 'treatments_catalogue_order_index');
        });

        Schema::table('gallery_items', function (Blueprint $table) {
            $table->text('image_path')->nullable()->change();
            $table->text('before_image_path')->nullable()->change();
            $table->text('after_image_path')->nullable()->change();
            $table->foreignId('treatment_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('gallery_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('treatment_id');
            $table->string('image_path')->nullable()->change();
            $table->string('before_image_path')->nullable()->change();
            $table->string('after_image_path')->nullable()->change();
        });

        Schema::table('treatments', function (Blueprint $table) {
            $table->dropIndex('treatments_catalogue_order_index');
            $table->dropColumn('sort_order');
        });

        Schema::table('treatment_categories', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
