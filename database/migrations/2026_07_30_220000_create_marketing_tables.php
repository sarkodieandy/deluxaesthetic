<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('client_profiles', 'loyalty_points')) {
            Schema::table('client_profiles', function (Blueprint $table) {
                $table->unsignedInteger('loyalty_points')->default(0)->after('referral_code');
            });
        }

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->string('image_path')->nullable();
            $table->string('mobile_image_path')->nullable();
            $table->string('placement')->default('sitewide');
            $table->string('cta_label')->nullable();
            $table->string('cta_url')->nullable();
            $table->string('coupon_code')->nullable();
            $table->string('background_color', 20)->default('#171613');
            $table->string('text_color', 20)->default('#ffffff');
            $table->unsignedInteger('priority')->default(10);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('context')->nullable();
            $table->text('quote');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->string('image_path')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(10);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_profile_id')->constrained()->cascadeOnDelete();
            $table->integer('points');
            $table->string('type')->default('manual');
            $table->string('description');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_client_profile_id')->constrained('client_profiles')->cascadeOnDelete();
            $table->string('referred_name');
            $table->string('referred_email')->nullable();
            $table->string('referred_phone')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedInteger('reward_points')->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('loyalty_transactions');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('promotions');
        if (Schema::hasColumn('client_profiles', 'loyalty_points')) {
            Schema::table('client_profiles', function (Blueprint $table) {
                $table->dropColumn('loyalty_points');
            });
        }
    }
};
