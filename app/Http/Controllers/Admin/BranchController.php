<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Clinic\StoreBranchRequest;
use App\Http\Requests\Admin\Clinic\UpdateBranchRequest;
use App\Models\Appointment;
use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(): View
    {
        $branches = Branch::query()
            ->orderByDesc('is_primary')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.branches.index', compact('branches'));
    }

    public function create(): View
    {
        return view('admin.branches.create');
    }

    public function store(StoreBranchRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $isPrimary = $request->boolean('is_primary');

        $branch = Branch::create($this->payload($data, $isPrimary));

        if ($isPrimary) {
            $this->clearOtherPrimaryFlags($branch->id);
        }

        return redirect()
            ->route('admin.branches.index')
            ->with('status', 'Branch created successfully.');
    }

    public function edit(Branch $branch): View
    {
        return view('admin.branches.edit', compact('branch'));
    }

    public function update(UpdateBranchRequest $request, Branch $branch): RedirectResponse
    {
        $data = $request->validated();
        $isPrimary = $request->boolean('is_primary');

        if (! $isPrimary && $branch->is_primary && ! Branch::query()->where('id', '!=', $branch->id)->where('is_primary', true)->exists()) {
            return back()
                ->withInput()
                ->withErrors(['is_primary' => 'At least one branch must remain marked as primary.']);
        }

        $branch->update($this->payload($data, $isPrimary, $branch));

        if ($isPrimary) {
            $this->clearOtherPrimaryFlags($branch->id);
        }

        return redirect()
            ->route('admin.branches.index')
            ->with('status', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        if ($branch->is_primary) {
            return back()->withErrors(['branch' => 'Cannot remove the primary branch. Set another branch as primary first.']);
        }

        if (Appointment::query()->where('branch_id', $branch->id)->exists()) {
            return back()->withErrors([
                'branch' => 'This branch has appointments on record. Deactivate it instead of deleting.',
            ]);
        }

        $branch->delete();

        return redirect()
            ->route('admin.branches.index')
            ->with('status', 'Branch removed.');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $data, bool $isPrimary, ?Branch $existing = null): array
    {
        $slug = filled($data['slug'] ?? null)
            ? Str::slug($data['slug'])
            : $this->uniqueSlug($data['name'], $existing?->id);

        return [
            'name' => $data['name'],
            'slug' => $slug,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'address_line_1' => $data['address_line_1'] ?? null,
            'address_line_2' => $data['address_line_2'] ?? null,
            'city' => $data['city'] ?? null,
            'region' => $data['region'] ?? null,
            'country' => $data['country'] ?? 'Ghana',
            'postal_code' => $data['postal_code'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'hours_summary' => $data['hours_summary'] ?? null,
            'map_embed_url' => $data['map_embed_url'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 10),
            'is_active' => request()->boolean('is_active', true),
            'is_primary' => $isPrimary,
        ];
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'branch';
        $slug = $base;
        $i = 2;

        while (
            Branch::withTrashed()
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    private function clearOtherPrimaryFlags(int $branchId): void
    {
        Branch::query()
            ->where('id', '!=', $branchId)
            ->where('is_primary', true)
            ->update(['is_primary' => false]);
    }
}
