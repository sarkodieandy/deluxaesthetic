<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Support\GalleryMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PromotionController extends Controller
{
    public function index(): View
    {
        return view('admin.promotions.index', ['promotions' => Promotion::latest()->paginate(20)]);
    }

    public function create(): View
    {
        return view('admin.promotions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $promotion = Promotion::create($this->payload($request) + ['created_by' => $request->user()->id]);

        return redirect()->route('admin.promotions.edit', $promotion)->with('status', 'promotion-created');
    }

    public function edit(Promotion $promotion): View
    {
        return view('admin.promotions.edit', compact('promotion'));
    }

    public function update(Request $request, Promotion $promotion): RedirectResponse
    {
        $promotion->update($this->payload($request, $promotion));

        return back()->with('status', 'promotion-updated');
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        $this->deleteLocal($promotion->image_path);
        $this->deleteLocal($promotion->mobile_image_path);
        $promotion->delete();

        return redirect()->route('admin.promotions.index')->with('status', 'promotion-deleted');
    }

    private function payload(Request $request, ?Promotion $promotion = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:140'],
            'subtitle' => ['nullable', 'string', 'max:300'],
            'placement' => ['required', Rule::in(['sitewide', 'home', 'store', 'academy'])],
            'cta_label' => ['nullable', 'string', 'max:60'],
            'cta_url' => ['nullable', 'string', 'max:2048'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'background_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'text_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'priority' => ['required', 'integer', 'min:0', 'max:999'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['required', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'image_url' => ['nullable', 'url:http,https', 'max:2048'],
            'mobile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'mobile_image_url' => ['nullable', 'url:http,https', 'max:2048'],
        ]);

        return [
            ...collect($data)->except(['image', 'image_url', 'mobile_image', 'mobile_image_url'])->all(),
            'is_active' => $request->boolean('is_active'),
            'coupon_code' => $data['coupon_code'] ? strtoupper($data['coupon_code']) : null,
            'image_path' => $this->resolveMedia($request->file('image'), $data['image_url'] ?? null, $promotion?->image_path, 'promotions/desktop'),
            'mobile_image_path' => $this->resolveMedia($request->file('mobile_image'), $data['mobile_image_url'] ?? null, $promotion?->mobile_image_path, 'promotions/mobile'),
        ];
    }

    private function resolveMedia(?UploadedFile $file, ?string $url, ?string $existing, string $directory): ?string
    {
        if ($file) {
            $this->deleteLocal($existing);

            return $file->store($directory, 'public');
        }
        if ($url) {
            $this->deleteLocal($existing);

            return $url;
        }

        return $existing;
    }

    private function deleteLocal(?string $path): void
    {
        if (GalleryMedia::isLocalStoredPath($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
