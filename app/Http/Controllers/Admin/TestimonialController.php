<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Support\GalleryMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function index(): View
    {
        return view('admin.testimonials.index', ['testimonials' => Testimonial::orderByDesc('is_featured')->orderBy('sort_order')->paginate(20)]);
    }

    public function create(): View
    {
        return view('admin.testimonials.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $testimonial = Testimonial::create($this->payload($request) + ['created_by' => $request->user()->id]);

        return redirect()->route('admin.testimonials.edit', $testimonial)->with('status', 'testimonial-created');
    }

    public function edit(Testimonial $testimonial): View
    {
        return view('admin.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update($this->payload($request, $testimonial));

        return back()->with('status', 'testimonial-updated');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        if (GalleryMedia::isLocalStoredPath($testimonial->image_path)) {
            Storage::disk('public')->delete($testimonial->image_path);
        }
        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')->with('status', 'testimonial-deleted');
    }

    private function payload(Request $request, ?Testimonial $testimonial = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'context' => ['nullable', 'string', 'max:120'],
            'quote' => ['required', 'string', 'max:1500'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_featured' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'image_url' => ['nullable', 'url:http,https', 'max:2048'],
        ]);
        $path = $testimonial?->image_path;
        if ($request->file('image')) {
            if (GalleryMedia::isLocalStoredPath($path)) {
                Storage::disk('public')->delete($path);
            }
            $path = $request->file('image')->store('testimonials', 'public');
        } elseif (! empty($data['image_url'])) {
            if (GalleryMedia::isLocalStoredPath($path)) {
                Storage::disk('public')->delete($path);
            }
            $path = $data['image_url'];
        }

        return [...collect($data)->except(['image', 'image_url'])->all(), 'is_featured' => $request->boolean('is_featured'), 'is_active' => $request->boolean('is_active'), 'image_path' => $path];
    }
}
