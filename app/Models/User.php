<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_code',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
            'two_factor_expires_at' => 'datetime',
        ];
    }

    /**
     * Security activity relationship.
     */
    public function securityActivities(): HasMany
    {
        return $this->hasMany(SecurityActivity::class);
    }

    /**
     * Generate a new two-factor OTP.
     */
    public function generateTwoFactorCode(): void
    {
        if (!$this->two_factor_enabled) {
            return;
        }

        $this->timestamps = false;

        $this->two_factor_code = random_int(100000, 999999);

        $this->two_factor_expires_at = now()->addMinutes(10);

        $this->save();

        $this->timestamps = true;

        $this->securityActivities()->create([
            'event' => 'OTP Generated',
            'description' => 'A new two-factor authentication OTP was generated.',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Reset the current OTP.
     */
    public function resetTwoFactorCode(): void
    {
        $this->timestamps = false;

        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;

        $this->save();

        $this->timestamps = true;
    }

    /**
     * Check whether the current OTP is expired.
     */
    public function isTwoFactorCodeExpired(): bool
    {
        return !$this->two_factor_expires_at ||
            $this->two_factor_expires_at->isPast();
    }

    /**
     * Check whether an OTP is currently active.
     */
    public function hasActiveTwoFactorCode(): bool
    {
        return $this->two_factor_enabled &&
            !empty($this->two_factor_code) &&
            $this->two_factor_expires_at &&
            $this->two_factor_expires_at->isFuture();
    }

    /**
     * Record a security activity.
     */
    public function recordSecurityActivity(
        string $event,
        ?string $description = null
    ): void {
        $this->securityActivities()->create([
            'event' => $event,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}