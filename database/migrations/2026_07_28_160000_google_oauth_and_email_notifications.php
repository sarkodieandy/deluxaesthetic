<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
            $table->timestamp('profile_completed_at')->nullable()->after('last_login_at');
            $table->timestamp('accepted_terms_at')->nullable()->after('profile_completed_at');
            $table->timestamp('accepted_privacy_at')->nullable()->after('accepted_terms_at');
            $table->string('last_login_ip', 45)->nullable()->after('accepted_privacy_at');
            $table->boolean('marketing_email_opt_in')->default(false)->after('last_login_ip');
            $table->timestamp('marketing_opt_in_at')->nullable()->after('marketing_email_opt_in');
        });

        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider', 32);
            $table->string('provider_user_id');
            $table->string('provider_email')->nullable();
            $table->string('provider_avatar_url', 2048)->nullable();
            $table->timestamp('linked_at');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_user_id']);
            $table->unique(['user_id', 'provider']);
            $table->index('provider_email');
        });

        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('event')->nullable();
            $table->string('channel')->default('mail');
            $table->string('locale', 5);
            $table->string('name');
            $table->string('subject');
            $table->string('preheader')->nullable();
            $table->longText('body_html');
            $table->longText('body_text')->nullable();
            $table->json('available_variables')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('system_template')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['key', 'locale']);
            $table->index('event');
            $table->index('active');
        });

        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->nullableMorphs('related');
            $table->string('recipient_email');
            $table->string('recipient_name')->nullable();
            $table->string('template_key');
            $table->string('locale', 5)->default('en');
            $table->string('subject');
            $table->string('provider')->default('smtp');
            $table->string('provider_message_id')->nullable();
            $table->string('status')->default('queued');
            $table->unsignedTinyInteger('attempt_count')->default(0);
            $table->timestamp('queued_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('template_key');
            $table->index('recipient_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('social_accounts');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_completed_at',
                'accepted_terms_at',
                'accepted_privacy_at',
                'last_login_ip',
                'marketing_email_opt_in',
                'marketing_opt_in_at',
            ]);
            $table->string('password')->nullable(false)->change();
        });
    }
};
