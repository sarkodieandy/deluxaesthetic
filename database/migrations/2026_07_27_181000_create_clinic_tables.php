<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatment_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('treatments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->json('benefits')->nullable();
            $table->text('suitable_candidates')->nullable();
            $table->text('contraindications')->nullable();
            $table->text('preparation_instructions')->nullable();
            $table->text('aftercare_instructions')->nullable();
            $table->unsignedInteger('duration_minutes')->default(60);
            $table->unsignedInteger('recovery_days')->default(0);
            $table->decimal('price', 12, 2);
            $table->decimal('promotional_price', 12, 2)->nullable();
            $table->decimal('deposit_amount', 12, 2)->nullable();
            $table->unsignedSmallInteger('recommended_sessions')->default(1);
            $table->unsignedInteger('buffer_before_minutes')->default(0);
            $table->unsignedInteger('buffer_after_minutes')->default(15);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('treatment_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->json('benefits')->nullable();
            $table->text('preparation_instructions')->nullable();
            $table->text('aftercare_instructions')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
            $table->unique(['treatment_id', 'locale']);
        });

        Schema::create('treatment_practitioner', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('practitioner_profile_id')->constrained()->cascadeOnDelete();
            $table->unique(['treatment_id', 'practitioner_profile_id'], 'treatment_practitioner_unique');
        });

        Schema::create('practitioner_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practitioner_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week');
            $table->time('starts_at');
            $table->time('ends_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('practitioner_blocked_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practitioner_profile_id')->constrained()->cascadeOnDelete();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->string('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('client_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('treatment_id')->constrained()->restrictOnDelete();
            $table->foreignId('practitioner_profile_id')->constrained()->restrictOnDelete();
            $table->foreignId('branch_id')->constrained()->restrictOnDelete();
            $table->foreignId('booked_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('status')->default('pending');
            $table->decimal('price', 12, 2);
            $table->decimal('deposit_amount', 12, 2)->default(0);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->string('currency', 3)->default('GHS');
            $table->text('client_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->json('consultation_answers')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['practitioner_profile_id', 'starts_at']);
            $table->index(['status', 'starts_at']);
        });

        Schema::create('appointment_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('consultation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('treatment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->date('preferred_date')->nullable();
            $table->string('preferred_channel')->default('email');
            $table->text('description');
            $table->json('attachments')->nullable();
            $table->boolean('consent_accepted')->default(false);
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('internal_notes')->nullable();
            $table->text('client_response')->nullable();
            $table->string('status')->default('submitted');
            $table->date('follow_up_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('consent_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('consentable');
            $table->string('document_key');
            $table->string('version')->nullable();
            $table->timestamp('accepted_at');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consent_records');
        Schema::dropIfExists('consultation_requests');
        Schema::dropIfExists('appointment_status_histories');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('practitioner_blocked_dates');
        Schema::dropIfExists('practitioner_schedules');
        Schema::dropIfExists('treatment_practitioner');
        Schema::dropIfExists('treatment_translations');
        Schema::dropIfExists('treatments');
        Schema::dropIfExists('treatment_categories');
    }
};
