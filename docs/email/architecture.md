# Email notification architecture

## Components

- **`EmailNotificationService`** — queue templated mail, create `email_logs` rows.
- **`EmailTemplateService`** — resolve template by `key` + locale with English fallback.
- **`EmailVariableService`** — whitelisted `{{variables}}`, HTML escaping.
- **`SendTemplatedEmail`** job — database queue, retries with backoff.

## Configuration

- `config/email-notifications.php` — toggles, retry policy, department addresses.
- `QUEUE_CONNECTION=database` (default in `.env.example`).

## Managed hosting cron

Run scheduler every minute; process queue in bounded batches:

```bash
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

Register in `routes/console.php`:

```php
Schedule::command('queue:work --stop-when-empty --max-jobs=25')->everyMinute()->withoutOverlapping();
```

## Admin

- **Email templates** — `/admin/email-templates` (edit subject/body EN/FR seeds).
- **Email logs** — `/admin/email-logs` with retry for failed/queued items.

## Event map (initial)

| Event | Template key |
|-------|----------------|
| `Registered` (email/password) | `auth.welcome` |
| `SocialAccountRegistered` | `auth.welcome` |
| `GoogleAccountLinked` | `auth.google_linked` |
| `GoogleAccountUnlinked` | `auth.google_unlinked` |

Additional business templates (appointments, academy, orders) are listed in `docs/email/template-inventory.md` and should be wired in Phase 5 listeners.
