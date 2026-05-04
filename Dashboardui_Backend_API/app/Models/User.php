<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isPatient(): bool
    {
        return $this->role === 'patient';
    }

    public function isCompanion(): bool
    {
        return $this->role === 'companion';
    }

    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    public function isSecretary(): bool
    {
        return $this->role === 'secretary';
    }

    /**
     * Check if user is an admin (clinic / tenant administrator)
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a super admin (platform level)
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * Access to the Laravel admin dashboard (assets, branding, translations UI).
     */
    public function hasAdminPrivileges(): bool
    {
        return in_array($this->role, ['admin', 'superadmin'], true);
    }

    /**
     * Staff who may act on behalf of the practice (not yet wired to routes; use for future policies).
     */
    public function hasStaffPrivileges(): bool
    {
        return in_array($this->role, ['doctor', 'secretary', 'admin', 'superadmin'], true);
    }

    /**
     * @return list<string>
     */
    public static function getAvailableRoles(): array
    {
        return ['superadmin', 'admin', 'doctor', 'secretary', 'patient', 'companion'];
    }
}
