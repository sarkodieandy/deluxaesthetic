<?php

namespace App\Services\Auth;

use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class LoginPortalService
{
    public function isAdminAccount(User $user): bool
    {
        return $user->hasAnyRole(config('admin.roles', []));
    }

    public function isCustomerOnly(User $user): bool
    {
        return $user->hasRole('Client')
            && ! $this->isAdminAccount($user)
            && ! $user->hasRole('Student');
    }

    public function prepare(User $user): User
    {
        if ($this->isAdminAccount($user) || $this->isCustomerOnly($user)) {
            return $user;
        }

        return DB::transaction(function () use ($user): User {
            if (! $user->hasRole('Student')) {
                $user->assignRole(Role::findOrCreate('Student', 'web'));
            }

            StudentProfile::query()->firstOrCreate(
                ['user_id' => $user->id],
                [
                    'student_number' => sprintf('STU-%s-%06d', now()->format('Y'), $user->id),
                    'phone' => $user->phone,
                ],
            );

            return $user->refresh()->load('roles', 'studentProfile');
        });
    }
}
