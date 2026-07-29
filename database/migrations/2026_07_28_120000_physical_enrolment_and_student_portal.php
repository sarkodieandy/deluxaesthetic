<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_enquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->date('preferred_training_date')->nullable();
            $table->string('professional_background')->nullable();
            $table->string('preferred_contact_method')->default('whatsapp');
            $table->text('message')->nullable();
            $table->boolean('privacy_consent')->default(false);
            $table->string('status')->default('submitted');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('converted_student_profile_id')->nullable()->constrained('student_profiles')->nullOnDelete();
            $table->foreignId('converted_enrolment_id')->nullable()->constrained('enrolments')->nullOnDelete();
            $table->text('internal_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
        });

        Schema::create('course_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_schedule_id')->constrained()->cascadeOnDelete();
            $table->date('session_date');
            $table->time('starts_at')->nullable();
            $table->time('ends_at')->nullable();
            $table->string('topic')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('trainer_profile_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('scheduled');
            $table->boolean('is_practical')->default(false);
            $table->boolean('is_assessment')->default(false);
            $table->text('announcement')->nullable();
            $table->timestamps();

            $table->unique(['course_schedule_id', 'session_date', 'starts_at'], 'course_sessions_unique_slot');
        });

        Schema::create('material_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrolment_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('downloaded_at');
            $table->timestamps();
        });

        Schema::create('student_support_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrolment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category');
            $table->string('subject');
            $table->text('message');
            $table->string('status')->default('open');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_response')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('notification_delivery_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel');
            $table->string('notification_type');
            $table->string('status')->default('queued');
            $table->json('payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('portrait_path')->nullable()->after('student_number');
            $table->string('phone')->nullable()->after('portrait_path');
            $table->string('address_line_1')->nullable()->after('phone');
            $table->string('address_line_2')->nullable()->after('address_line_1');
            $table->string('city')->nullable()->after('address_line_2');
            $table->string('region')->nullable()->after('city');
            $table->string('country')->nullable()->after('region');
            $table->string('emergency_contact_name')->nullable()->after('country');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->json('notification_preferences')->nullable()->after('emergency_contact_phone');
            $table->timestamp('profile_completed_at')->nullable()->after('notification_preferences');
            $table->timestamp('portal_invited_at')->nullable()->after('profile_completed_at');
            $table->timestamp('portal_activated_at')->nullable()->after('portal_invited_at');
            $table->string('invitation_token')->nullable()->unique()->after('portal_activated_at');
            $table->timestamp('invitation_expires_at')->nullable()->after('invitation_token');
        });

        Schema::table('enrolments', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('course_schedule_id')->constrained()->nullOnDelete();
            $table->foreignId('trainer_profile_id')->nullable()->after('branch_id')->constrained()->nullOnDelete();
            $table->date('enrolment_date')->nullable()->after('trainer_profile_id');
            $table->date('physical_verification_date')->nullable()->after('enrolment_date');
            $table->foreignId('verified_by')->nullable()->after('physical_verification_date')->constrained('users')->nullOnDelete();
            $table->decimal('discount', 12, 2)->default(0)->after('fee');
            $table->decimal('deposit_required', 12, 2)->nullable()->after('discount');
            $table->timestamp('activated_at')->nullable()->after('confirmed_at');
            $table->foreignId('activated_by')->nullable()->after('activated_at')->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable()->after('activated_by');
            $table->text('internal_notes')->nullable()->after('documents');
        });
    }

    public function down(): void
    {
        Schema::table('enrolments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
            $table->dropConstrainedForeignId('trainer_profile_id');
            $table->dropConstrainedForeignId('verified_by');
            $table->dropConstrainedForeignId('activated_by');
            $table->dropColumn([
                'enrolment_date', 'physical_verification_date', 'discount', 'deposit_required',
                'activated_at', 'completed_at', 'internal_notes',
            ]);
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'portrait_path', 'phone', 'address_line_1', 'address_line_2', 'city', 'region', 'country',
                'emergency_contact_name', 'emergency_contact_phone', 'notification_preferences',
                'profile_completed_at', 'portal_invited_at', 'portal_activated_at',
                'invitation_token', 'invitation_expires_at',
            ]);
        });

        Schema::dropIfExists('notification_delivery_logs');
        Schema::dropIfExists('student_support_requests');
        Schema::dropIfExists('material_downloads');
        Schema::dropIfExists('course_sessions');
        Schema::dropIfExists('course_enquiries');
    }
};
