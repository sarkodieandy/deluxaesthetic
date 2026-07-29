<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailTemplateController extends Controller
{
    public function index(Request $request): View
    {
        $templates = EmailTemplate::query()
            ->when($request->filled('locale'), fn ($q) => $q->where('locale', $request->string('locale')))
            ->when($request->filled('event'), fn ($q) => $q->where('event', $request->string('event')))
            ->orderBy('key')
            ->orderBy('locale')
            ->paginate(30);

        return view('admin.email-templates.index', compact('templates'));
    }

    public function edit(EmailTemplate $emailTemplate): View
    {
        return view('admin.email-templates.edit', ['template' => $emailTemplate]);
    }

    public function update(Request $request, EmailTemplate $emailTemplate): RedirectResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'preheader' => ['nullable', 'string', 'max:255'],
            'body_html' => ['required', 'string'],
            'body_text' => ['nullable', 'string'],
            'active' => ['sometimes', 'boolean'],
        ]);

        $emailTemplate->update([
            ...$data,
            'active' => $request->boolean('active'),
            'updated_by' => $request->user()->id,
        ]);

        return redirect()->route('admin.email-templates.index')->with('status', 'Template updated.');
    }
}
