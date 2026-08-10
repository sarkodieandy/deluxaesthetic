<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademyShowcaseItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AcademyShowcaseController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->string('type')->toString();

        return view('admin.academy-showcase.index', [
            'items' => AcademyShowcaseItem::query()
                ->when(array_key_exists($type, AcademyShowcaseItem::TYPES), fn ($query) => $query->where('type', $type))
                ->orderBy('type')
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->paginate(20)
                ->withQueryString(),
            'selectedType' => $type,
        ]);
    }

    public function create(): View
    {
        return view('admin.academy-showcase.create');
    }

    public function store(Request $request): RedirectResponse
    {
        AcademyShowcaseItem::create($this->payload($request));
        return redirect()->route('admin.academy-showcase.index')->with('status', 'Academy showcase item published.');
    }

    public function edit(AcademyShowcaseItem $academyShowcase): View
    {
        return view('admin.academy-showcase.edit', ['item' => $academyShowcase]);
    }

    public function update(Request $request, AcademyShowcaseItem $academyShowcase): RedirectResponse
    {
        $previousImage = $academyShowcase->image_path;
        $payload = $this->payload($request, $academyShowcase);

        DB::transaction(function () use ($academyShowcase, $payload, $previousImage): void {
            $academyShowcase->update($payload);

            if ($payload['image_path'] !== $previousImage) {
                DB::afterCommit(fn () => $this->deleteManagedImage($previousImage));
            }
        });

        return redirect()->route('admin.academy-showcase.index')->with('status', 'Academy showcase item updated.');
    }

    public function destroy(AcademyShowcaseItem $academyShowcase): RedirectResponse
    {
        DB::transaction(function () use ($academyShowcase): void {
            $imagePath = $academyShowcase->image_path;
            $academyShowcase->delete();
            DB::afterCommit(fn () => $this->deleteManagedImage($imagePath));
        });

        return back()->with('status', 'Academy showcase item removed.');
    }

    private function payload(Request $request, ?AcademyShowcaseItem $item = null): array
    {
        $bodyRequiredTypes = [
            'training_step', 'student_story', 'skill_review', 'training_country',
            'experience', 'career_benefit',
        ];

        $data = $request->validate([
            'type' => ['required', Rule::in(array_keys(AcademyShowcaseItem::TYPES))],
            'title' => ['required', 'string', 'max:160'],
            'subtitle' => ['nullable', 'string', 'max:200'],
            'body' => [Rule::requiredIf(in_array($request->input('type'), $bodyRequiredTypes, true)), 'nullable', 'string', 'max:3000'],
            'image' => ['nullable', 'image', 'max:8192'],
            'image_url' => ['nullable', 'url:http,https', 'max:2048'],
            'video_url' => [Rule::requiredIf($request->input('type') === 'student_video'), 'nullable', 'url:http,https', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'remove_image' => ['sometimes', 'boolean'],
        ]);

        $path = $item?->image_path;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('academy-showcase', 'public');
            if (! is_string($path) || $path === '') {
                throw new \RuntimeException('The Academy showcase image could not be stored.');
            }
        } elseif ($request->filled('image_url')) {
            $path = $request->string('image_url')->toString();
        } elseif ($request->boolean('remove_image')) {
            $path = null;
        }

        return [
            'type' => $data['type'], 'title' => $data['title'], 'subtitle' => $data['subtitle'] ?? null,
            'body' => $data['body'] ?? null, 'image_path' => $path, 'video_url' => $data['video_url'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 10), 'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function deleteManagedImage(?string $path): void
    {
        if ($path && ! filter_var($path, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($path);
        }
    }
}
