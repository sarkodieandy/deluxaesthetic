<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WebPageController extends Controller
{
    public function home(): RedirectResponse
    {
        return redirect()->route('admin.pages.edit', WebPage::where('route_name', 'web.home')->firstOrFail());
    }

    public function index(): View
    {
        return view('admin.pages.index', [
            'pages' => WebPage::with('editor:id,name')->orderBy('name')->get(),
        ]);
    }

    public function edit(WebPage $page): View
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, WebPage $page): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:170'],
            'hero_eyebrow' => ['nullable', 'string', 'max:100'],
            'hero_title' => ['nullable', 'string', 'max:180'],
            'hero_body' => ['nullable', 'string', 'max:600'],
            'hero_image_url' => ['nullable', 'url:http,https', 'max:2048'],
            'is_published' => ['required', 'boolean'],
            'sections' => ['nullable', 'array', 'max:12'],
            'sections.*.eyebrow' => ['nullable', 'string', 'max:100'],
            'sections.*.heading' => ['nullable', 'string', 'max:180'],
            'sections.*.body' => ['nullable', 'string', 'max:3000'],
            'sections.*.image_url' => ['nullable', 'url:http,https', 'max:2048'],
            'sections.*.cta_label' => ['nullable', 'string', 'max:80'],
            'sections.*.cta_url' => ['nullable', 'string', 'max:2048'],
            'sections.*.is_enabled' => ['nullable', Rule::in(['0', '1', 0, 1])],
        ]);

        $sections = collect($data['sections'] ?? [])
            ->map(fn ($section) => [
                'eyebrow' => $section['eyebrow'] ?? null,
                'heading' => $section['heading'] ?? null,
                'body' => $section['body'] ?? null,
                'image_url' => $section['image_url'] ?? null,
                'cta_label' => $section['cta_label'] ?? null,
                'cta_url' => $section['cta_url'] ?? null,
                'is_enabled' => (bool) ($section['is_enabled'] ?? false),
            ])
            ->filter(fn ($section) => collect($section)->except('is_enabled')->filter()->isNotEmpty())
            ->values()
            ->all();

        $published = $request->boolean('is_published');
        $page->update([
            ...collect($data)->except(['sections', 'is_published'])->all(),
            'sections' => $sections,
            'is_published' => $published,
            'published_at' => $published ? ($page->published_at ?? now()) : null,
            'updated_by' => $request->user()->id,
        ]);

        return back()->with('status', 'page-updated');
    }

    public function preview(WebPage $page): RedirectResponse
    {
        return redirect()->route($page->route_name, ['cms_preview' => $page->id]);
    }
}
