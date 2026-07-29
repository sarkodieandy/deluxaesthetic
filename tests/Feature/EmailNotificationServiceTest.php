<?php

namespace Tests\Feature;

use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Services\Messaging\EmailNotificationService;
use Database\Seeders\EmailTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class EmailNotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_queue_creates_log_and_dispatches_job(): void
    {
        Queue::fake();
        $this->seed(EmailTemplateSeeder::class);

        $user = User::factory()->create(['locale' => 'en']);

        $log = app(EmailNotificationService::class)->queueToUser('auth.welcome', $user);

        $this->assertInstanceOf(EmailLog::class, $log);
        $this->assertDatabaseHas('email_logs', [
            'id' => $log->id,
            'template_key' => 'auth.welcome',
            'status' => 'queued',
        ]);
        Queue::assertPushed(\App\Jobs\Emails\SendTemplatedEmail::class);
    }

    public function test_french_template_falls_back_when_missing(): void
    {
        EmailTemplate::query()->create([
            'key' => 'auth.welcome',
            'locale' => 'en',
            'name' => 'Welcome',
            'subject' => 'Welcome',
            'body_html' => '<p>Hello {{recipient_name}}</p>',
            'active' => true,
            'system_template' => true,
        ]);

        $template = app(\App\Services\Messaging\EmailTemplateService::class)->resolve('auth.welcome', 'fr');

        $this->assertNotNull($template);
        $this->assertSame('en', $template->locale);
    }
}
