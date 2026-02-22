<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\SubscriptionStatus;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'google_id',
        'avatar',
        'role',
        'subscription_status',
        'subscription_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'       => 'datetime',
            'password'                => 'hashed',
            'role'                    => UserRole::class,
            'subscription_status'     => SubscriptionStatus::class,
            'subscription_expires_at' => 'datetime',
        ];
    }

    // ---------------------------------------------------------------
    // Filament admin panel access
    // ---------------------------------------------------------------

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === UserRole::Admin;
    }

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function psychologist(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Psychologist::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(UserActivityLog::class);
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isPsychologist(): bool
    {
        return $this->role === UserRole::Psychologist;
    }
}
