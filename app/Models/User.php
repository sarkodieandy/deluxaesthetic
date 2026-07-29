<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'name',
    'email',
    'password',
    'phone',
    'locale',
    'is_active',
    'last_login_at',
    'last_login_ip',
    'profile_completed_at',
    'accepted_terms_at',
    'accepted_privacy_at',
    'marketing_email_opt_in',
    'marketing_opt_in_at',
])]
#[Hidden(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'profile_completed_at' => 'datetime',
            'accepted_terms_at' => 'datetime',
            'accepted_privacy_at' => 'datetime',
            'marketing_email_opt_in' => 'boolean',
            'marketing_opt_in_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function clientProfile(): HasOne
    {
        return $this->hasOne(ClientProfile::class);
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function socialAccounts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function hasUsablePassword(): bool
    {
        return filled($this->password);
    }

    public function practitionerProfile(): HasOne
    {
        return $this->hasOne(PractitionerProfile::class);
    }

    public function trainerProfile(): HasOne
    {
        return $this->hasOne(TrainerProfile::class);
    }

    public function portalHomeRoute(): string
    {
        $staffRoles = config('admin.roles', []);

        if ($this->hasRole('Student') && ! $this->hasAnyRole($staffRoles)) {
            return 'student.dashboard';
        }

        if ($this->hasRole('Client') && ! $this->hasAnyRole($staffRoles)) {
            return 'web.home';
        }

        return match (true) {
            $this->hasRole('Super Administrator'),
            $this->hasRole('Clinic Administrator'),
            $this->hasAnyRole(['Receptionist', 'Finance Officer', 'Store Manager', 'Content Manager', 'Support Agent']) => 'admin.dashboard',
            $this->hasAnyRole(['Practitioner', 'Therapist']) => 'practitioner.dashboard',
            $this->hasRole('Trainer') => 'trainer.dashboard',
            $this->hasRole('Student') => 'student.dashboard',
            default => 'web.home',
        };
    }
}
