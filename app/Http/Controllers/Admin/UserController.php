<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $role = trim((string) $request->query('role', ''));
        $query = User::query()
            ->with('roles')
            ->whereHas('roles', fn ($q) => $q->whereIn('name', config('admin.roles', [])))
            ->latest();

        if (! $request->user()->hasRole('Super Administrator')) {
            $query->whereDoesntHave('roles', fn ($q) => $q->where('name', 'Super Administrator'));
        }

        if ($search !== '') {
            $query->where(fn ($q) => $q
                ->where('name', 'like', '%'.$search.'%')
                ->orWhere('email', 'like', '%'.$search.'%')
                ->orWhere('phone', 'like', '%'.$search.'%'));
        }
        if ($role !== '') {
            $query->role($role);
        }

        return view('admin.users.index', [
            'users' => $query->paginate(20)->withQueryString(),
            'roles' => $this->assignableRoles($request),
            'filters' => compact('search', 'role'),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.users.create', ['roles' => $this->assignableRoles($request)]);
    }

    public function store(Request $request): RedirectResponse
    {
        $roles = $this->assignableRoles($request);
        $data = $request->validate($this->rules($roles));

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'email_verified_at' => now(),
                'locale' => $data['locale'],
                'is_active' => $data['is_active'],
                'profile_completed_at' => now(),
            ]);
            $user->syncRoles([$data['role']]);

            return $user;
        });

        return redirect()->route('admin.users.edit', $user)->with('status', 'user-created');
    }

    public function edit(Request $request, User $user): View
    {
        $this->ensureCanManage($request, $user);

        return view('admin.users.edit', [
            'managedUser' => $user->load('roles'),
            'roles' => $this->assignableRoles($request, $user),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->ensureCanManage($request, $user);
        $roles = $this->assignableRoles($request, $user);
        $data = $request->validate($this->rules($roles, $user));

        if ($request->user()->is($user) && ! $data['is_active']) {
            return back()->withInput()->withErrors(['is_active' => 'You cannot deactivate your own account.']);
        }
        if ($request->user()->is($user)
            && $user->hasRole('Super Administrator')
            && $data['role'] !== 'Super Administrator') {
            return back()->withInput()->withErrors(['role' => 'You cannot remove your own Super Administrator role.']);
        }

        DB::transaction(function () use ($user, $data) {
            $user->fill([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'locale' => $data['locale'],
                'is_active' => $data['is_active'],
            ]);
            if ($user->isDirty('email')) {
                $user->email_verified_at = now();
            }
            if (! empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            $user->save();
            $user->syncRoles([$data['role']]);
        });

        return back()->with('status', 'user-updated');
    }

    private function rules($roles, ?User $user = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user?->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'role' => ['required', Rule::in($roles->pluck('name')->all())],
            'locale' => ['required', Rule::in(config('clinic.supported_locales', ['en', 'fr']))],
            'is_active' => ['required', 'boolean'],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::defaults()],
        ];
    }

    private function assignableRoles(Request $request, ?User $user = null)
    {
        $roles = Role::query()
            ->whereIn('name', config('admin.roles', []))
            ->orderBy('name');

        if (! $request->user()->hasRole('Super Administrator')) {
            $roles->where('name', '!=', 'Super Administrator');
        }

        return $roles->get();
    }

    private function ensureCanManage(Request $request, User $user): void
    {
        abort_if(
            $user->hasRole('Super Administrator') && ! $request->user()->hasRole('Super Administrator'),
            403,
        );
    }
}
