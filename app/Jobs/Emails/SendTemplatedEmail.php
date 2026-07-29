<?php

namespace App\Jobs\Emails;

use App\Models\EmailLog;
use App\Services\Messaging\EmailTemplateService;
use App\Services\Messaging\EmailVariableService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendTemplatedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries;

    public function __construct(
        public int $emailLogId,
    ) {
        $this->tries = (int) config('email-notifications.retry_attempts', 3);
    }

    /**
     * @return list<int>
     */
    public function backoff(): array
    {
        $base = (int) config('email-notifications.retry_delay_seconds', 300);

        return [$base, $base * 3, $base * 12];
    }

    public function handle(EmailTemplateService $templates, EmailVariableService $variables): void
    {
        if (! config('email-notifications.enabled')) {
            return;
        }

        $log = EmailLog::query()->find($this->emailLogId);
        if (! $log || in_array($log->status, ['sent', 'delivered', 'cancelled'], true)) {
            return;
        }

        $log->update([
            'status' => 'processing',
            'attempt_count' => $log->attempt_count + 1,
        ]);

        $template = $templates->resolve($log->template_key, $log->locale);
        if (! $template) {
            $log->update([
                'status' => 'failed',
                'failed_at' => now(),
                'failure_reason' => 'Template not found',
            ]);

            return;
        }

        $merged = $variables->mergeDefaults([], $log->user);
        $html = $variables->render($template->body_html, $merged);
        $text = $template->body_text
            ? $variables->render($template->body_text, $merged)
            : strip_tags($html);

        try {
            Mail::html($html, function ($message) use ($log, $text) {
                $message->to($log->recipient_email, $log->recipient_name)
                    ->subject($log->subject)
                    ->text($text);

                $reply = config('email-notifications.reply_to');
                if (! empty($reply['address'])) {
                    $message->replyTo($reply['address'], $reply['name'] ?? null);
                }
            });

            $log->update([
                'status' => 'sent',
                'sent_at' => now(),
                'provider' => config('mail.default'),
            ]);
        } catch (Throwable $exception) {
            $log->update([
                'status' => 'failed',
                'failed_at' => now(),
                'failure_reason' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
