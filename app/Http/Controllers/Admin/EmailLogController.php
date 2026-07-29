<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\Emails\SendTemplatedEmail;
use App\Models\EmailLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = EmailLog::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(40);

        return view('admin.email-logs.index', compact('logs'));
    }

    public function retry(EmailLog $emailLog): RedirectResponse
    {
        if (! in_array($emailLog->status, ['failed', 'queued'], true)) {
            return back()->withErrors(['retry' => 'Only failed or queued messages can be retried.']);
        }

        $emailLog->update(['status' => 'queued', 'failed_at' => null, 'failure_reason' => null]);
        SendTemplatedEmail::dispatch($emailLog->id);

        return back()->with('status', 'Email queued for retry.');
    }
}
