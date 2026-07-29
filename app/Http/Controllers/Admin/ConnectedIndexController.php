<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\StudentProfile;
use App\Models\TrainerProfile;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class ConnectedIndexController extends Controller
{
    public function students(): View
    {
        $rows = StudentProfile::query()->with('user')->latest('created_at')->paginate(20);

        return $this->render('Students', 'Academy / Students', ['Name', 'Email'], $rows->through(fn ($student) => [
            $student->user?->name,
            $student->user?->email,
        ]));
    }

    public function trainers(): View
    {
        $rows = TrainerProfile::query()->with('user')->latest('created_at')->paginate(20);

        return $this->render('Trainers', 'Academy / Trainers', ['Name', 'Title'], $rows->through(fn ($trainer) => [
            $trainer->user?->name,
            $trainer->title,
        ]));
    }

    public function courses(): View
    {
        $rows = DB::table('courses')->latest('created_at')->paginate(20);

        return $this->render('Courses', 'Academy / Courses', ['Name', 'Delivery', 'Fee', 'Status'], $rows->through(fn ($course) => [
            $course->name,
            ucfirst(str_replace('_', ' ', $course->delivery_mode ?? 'physical')),
            'GHS '.number_format((float) $course->fee, 2),
            $course->is_active ? 'Active' : 'Hidden',
        ]));
    }

    public function enrolments(): View
    {
        $rows = DB::table('enrolments')
            ->join('student_profiles', 'student_profiles.id', '=', 'enrolments.student_profile_id')
            ->join('users', 'users.id', '=', 'student_profiles.user_id')
            ->join('courses', 'courses.id', '=', 'enrolments.course_id')
            ->select('enrolments.*', 'users.name as student_name', 'courses.name as course_name')
            ->latest('enrolments.created_at')
            ->paginate(20);

        return $this->render('Enrolments', 'Academy / Enrolments', ['Reference', 'Student', 'Course', 'Status', 'Balance'], $rows->through(fn ($row) => [
            $row->reference,
            $row->student_name,
            $row->course_name,
            ucfirst(str_replace('_', ' ', $row->status)),
            'GHS '.number_format((float) $row->outstanding_balance, 2),
        ]));
    }

    public function certificates(): View
    {
        $rows = DB::table('certificates')->latest('created_at')->paginate(20);

        return $this->render('Certificates', 'Academy / Certificates', ['Number', 'Student', 'Course', 'Status', 'Issued'], $rows->through(fn ($row) => [
            $row->number,
            $row->student_name,
            $row->course_name,
            ucfirst($row->status),
            $row->issued_at ? date('d M Y', strtotime($row->issued_at)) : '—',
        ]));
    }

    public function orders(): View
    {
        $rows = DB::table('orders')->join('users', 'users.id', '=', 'orders.user_id')->select('orders.*', 'users.name as user_name')->latest('orders.created_at')->paginate(20);

        return $this->render('Orders', 'Store / Orders', ['Order', 'Client', 'Status', 'Total'], $rows->through(fn ($row) => [
            $row->number,
            $row->user_name,
            ucfirst(str_replace('_', ' ', $row->status)),
            'GHS '.number_format((float) $row->grand_total, 2),
        ]));
    }

    public function payments(): View
    {
        $rows = DB::table('payments')->join('users', 'users.id', '=', 'payments.user_id')->select('payments.*', 'users.name as user_name')->latest('payments.created_at')->paginate(20);

        return $this->render('Payments', 'Finance / Payments', ['Reference', 'Client', 'Gateway', 'Status', 'Amount'], $rows->through(fn ($row) => [
            $row->reference,
            $row->user_name,
            ucfirst($row->gateway),
            ucfirst($row->status),
            'GHS '.number_format((float) $row->amount, 2),
        ]));
    }

    public function users(Request $request): View
    {
        $query = User::query()->with('roles')->latest('created_at');
        $search = trim((string) $request->query('search', ''));
        $roleFilter = trim((string) $request->query('role', ''));

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        if ($roleFilter !== '') {
            $query->role($roleFilter);
        }

        $rows = $query->paginate(20)->withQueryString();
        $roles = Role::query()->orderBy('name')->pluck('name');

        return $this->render(
            'Users',
            'System / Users',
            ['Name', 'Email', 'Roles', 'Status'],
            $rows->through(fn ($user) => [
                $user->name,
                $user->email,
                $user->roles->isNotEmpty()
                    ? new HtmlString($user->roles->map(fn ($role) => '<a href="'.route('admin.users.index', ['role' => $role->name]).'" class="admin-badge admin-badge--small">'.e($role->name).'</a>')->implode(' '))
                    : '—',
                $user->is_active ? 'Active' : 'Inactive',
            ]),
            [
                'search' => $search,
                'role' => $roleFilter,
                'roleOptions' => $roles,
            ]
        );
    }

    public function roles(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $query = Role::query()->withCount(['permissions', 'users'])->orderBy('name');

        if ($search !== '') {
            $query->where('name', 'like', '%'.$search.'%');
        }

        $rows = $query->paginate(20)->withQueryString();

        return $this->render(
            'Roles',
            'System / Roles',
            ['Role', 'Permissions', 'Users'],
            $rows->through(fn ($role) => [
                new HtmlString('<a href="'.route('admin.users.index', ['role' => $role->name]).'">'.e($role->name).'</a>'),
                (string) $role->permissions_count,
                new HtmlString('<a href="'.route('admin.users.index', ['role' => $role->name]).'">'.(string) $role->users_count.'</a>'),
            ]),
            [
                'search' => $search,
            ]
        );
    }

    public function settings(): View
    {
        $rows = Setting::query()->latest('group')->paginate(30);

        return $this->render('Settings', 'System / Settings', ['Group', 'Key', 'Value', 'Public'], $rows->through(fn ($setting) => [
            $setting->group,
            $setting->key,
            str((string) $setting->value)->limit(80),
            $setting->is_public ? 'Yes' : 'No',
        ]));
    }

    private function render(string $title, string $breadcrumb, array $columns, LengthAwarePaginator $rows, array $filters = []): View
    {
        return view('admin.connected.index', compact('title', 'breadcrumb', 'columns', 'rows', 'filters'));
    }
}
