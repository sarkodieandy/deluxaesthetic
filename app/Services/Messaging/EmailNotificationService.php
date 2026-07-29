<?php

namespace App\Services\Messaging;

use App\Jobs\Emails\SendTemplatedEmail;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class EmailNotificationService
{
    public function __construct(
        private readonly EmailTemplateService $templates,
        private readonly EmailVariableService $variables,
    ) {}

    /**
     * @param  array<string, string|null>  $variables
     */
    public function queueTo(
        string $templateKey,
        string $recipientEmail,
        ?string $recipientName,
        array $variables = [],
        ?User $user = null,
        ?Model $related = null,
        ?string $locale = null,
    ): ?EmailLog {
        if (! config('email-notifications.enabled')) {
            return null;
        }

        $locale = $locale ?: ($user?->locale ?? config('email-notifications.default_locale', 'en'));
        $template = $this->templates->resolve($templateKey, $locale);
        if (! $template) {
            Log::warning('Email template missing', ['key' => $templateKey, 'locale' => $locale]);

            return null;
        }

        $merged = $this->variables->mergeDefaults($variables, $user);
        $this->variables->assertAllowed($template, $merged);

        $subject = $this->variables->render($template->subject, $merged);

        $log = EmailLog::create([
            'user_id' => $user?->id,
            'related_type' => $related ? $related->getMorphClass() : null,
            'related_id' => $related?->getKey(),
            'recipient_email' => $recipientEmail,
            'recipient_name' => $recipientName,
            'template_key' => $templateKey,
            'locale' => $template->locale,
            'subject' => $subject,
            'status' => 'queued',
            'attempt_count' => 0,
            'queued_at' => now(),
            'metadata' => ['variables' => array_keys($merged)],
        ]);

        SendTemplatedEmail::dispatch($log->id);

        return $log;
    }

    public function queueToUser(string $templateKey, User $user, array $variables = [], ?Model $related = null): ?EmailLog
    {
        return $this->queueTo($templateKey, $user->email, $user->name, $variables, $user, $related, $user->locale);
    }
}
