<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePractitionerRequest;
use App\Http\Requests\Admin\UpdatePractitionerRequest;
use App\Models\PractitionerProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PractitionerController extends Controller
{
    public function index(): View
    {
        $practitioners = PractitionerProfile::query()
            ->with('user')
            ->orderByDesc('is_ceo')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(12);

        return view('admin.practitioners.index', compact('practitioners'));
    }

    public function create(): View
    {
        return view('admin.practitioners.create');
    }

    public function store(StorePractitionerRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $request) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make(Str::password(16)),
                'email_verified_at' => now(),
                'locale' => 'en',
                'is_active' => true,
            ]);
            $user->assignRole('Practitioner');

            PractitionerProfile::create([
                'user_id' => $user->id,
                'slug' => $this->uniqueSlug($data['name']),
                'title' => $data['title'] ?? $data['professional_title'],
                'professional_title' => $data['professional_title'],
                'biography' => $data['biography'] ?? null,
                'years_experience' => (int) ($data['years_experience'] ?? 0),
                'photo_path' => $this->storePhoto($request->file('photo')),
                'is_ceo' => $request->boolean('is_ceo'),
                'is_featured' => $request->boolean('is_featured', true),
                'is_active' => $request->boolean('is_active', true),
                'sort_order' => (int) ($data['sort_order'] ?? 10),
                'social_links' => $this->socialFrom($data),
                'qualifications' => [],
                'certifications' => [],
                'specialities' => [],
            ]);
        });

        return redirect()
            ->route('admin.practitioners.index')
            ->with('status', 'Team member added successfully.');
    }

    public function edit(PractitionerProfile $practitioner): View
    {
        $practitioner->load('user');

        return view('admin.practitioners.edit', compact('practitioner'));
    }

    public function update(UpdatePractitionerRequest $request, PractitionerProfile $practitioner): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $request, $practitioner) {
            $practitioner->user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
            ]);

            $payload = [
                'title' => $data['title'] ?? $data['professional_title'],
                'professional_title' => $data['professional_title'],
                'biography' => $data['biography'] ?? null,
                'years_experience' => (int) ($data['years_experience'] ?? 0),
                'is_ceo' => $request->boolean('is_ceo'),
                'is_featured' => $request->boolean('is_featured'),
                'is_active' => $request->boolean('is_active'),
                'sort_order' => (int) ($data['sort_order'] ?? 10),
                'social_links' => $this->socialFrom($data),
            ];

            if ($request->hasFile('photo')) {
                $this->deleteStoredPhoto($practitioner->photo_path);
                $payload['photo_path'] = $this->storePhoto($request->file('photo'));
            }

            $practitioner->update($payload);
        });

        return redirect()
            ->route('admin.practitioners.index')
            ->with('status', 'Team member updated successfully.');
    }

    public function destroy(PractitionerProfile $practitioner): RedirectResponse
    {
        if ($practitioner->is_ceo) {
            return back()->with('status', 'The CEO profile cannot be deleted.');
        }

        $this->deleteStoredPhoto($practitioner->photo_path);
        $practitioner->delete();

        return redirect()
            ->route('admin.practitioners.index')
            ->with('status', 'Team member removed.');
    }

    private function storePhoto(?UploadedFile $file): ?string
    {
        if (! $file) {
            return null;
        }

        return $file->store('team', 'public');
    }

    private function deleteStoredPhoto(?string $path): void
    {
        if (! $path || str_starts_with($path, 'assets/')) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function socialFrom(array $data): array
    {
        return array_filter([
            'facebook' => $data['social_facebook'] ?? null,
            'instagram' => $data['social_instagram'] ?? null,
            'twitter' => $data['social_twitter'] ?? null,
            'linkedin' => $data['social_linkedin'] ?? null,
        ]);
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'team-member';
        $slug = $base;
        $i = 2;

        while (PractitionerProfile::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
