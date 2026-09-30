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
            'two_factor_locked_until' => 'datetime',
            'two_factor_last_sent_at' => 'datetime',
        ];
    }

    /**
     * Security activities.
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

        $this->two_factor_last_sent_at = now();

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
     * Reset current OTP.
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
     * Check OTP expiration.
     */
    public function isTwoFactorCodeExpired(): bool
    {
        return !$this->two_factor_expires_at ||
            $this->two_factor_expires_at->isPast();
    }

    /**
     * Check active OTP.
     */
    public function hasActiveTwoFactorCode(): bool
    {
        return $this->two_factor_enabled &&
            !empty($this->two_factor_code) &&
            $this->two_factor_expires_at &&
            $this->two_factor_expires_at->isFuture();
    }

    /**
     * Check whether 2FA is currently locked.
     */
    public function isTwoFactorLocked(): bool
    {
        if (!$this->two_factor_locked_until) {
            return false;
        }

        if ($this->two_factor_locked_until->isPast()) {
            $this->clearTwoFactorLock();

            return false;
        }

        return true;
    }

    /**
     * Register failed OTP attempt.
     */
    public function registerFailedTwoFactorAttempt(): void
    {
        $this->two_factor_failed_attempts++;

        if ($this->two_factor_failed_attempts >= 5) {
            $this->two_factor_locked_until = now()->addMinutes(15);

            $this->securityActivities()->create([
                'event' => 'OTP Locked',
                'description' => 'OTP verification was locked for 15 minutes after 5 failed attempts.',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        $this->save();

        $this->securityActivities()->create([
            'event' => 'OTP Failed',
            'description' => 'An incorrect OTP was entered. Failed attempts: '
                . $this->two_factor_failed_attempts
                . '/5.',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Clear OTP lock.
     */
    public function clearTwoFactorLock(): void
    {
        $this->two_factor_failed_attempts = 0;
        $this->two_factor_locked_until = null;

        $this->save();
    }

    /**
     * Clear attempts after successful verification.
     */
    public function clearTwoFactorAttempts(): void
    {
        $this->two_factor_failed_attempts = 0;
        $this->two_factor_locked_until = null;

        $this->save();
    }

    /**
     * Remaining OTP resend cooldown in seconds.
     */
    public function resendCooldownSeconds(): int
    {
        if (!$this->two_factor_last_sent_at) {
            return 0;
        }

        $availableAt = $this->two_factor_last_sent_at
            ->copy()
            ->addSeconds(60);

        return max(
            0,
            now()->diffInSeconds($availableAt, false)
        );
    }

    /**
     * Check whether resend is allowed.
     */
    public function canResendTwoFactorCode(): bool
    {
        return $this->resendCooldownSeconds() <= 0;
    }

    /**
     * Record security activity.
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