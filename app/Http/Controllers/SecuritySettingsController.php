<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SecuritySettingsController extends Controller
{
    /**
     * Show security settings.
     */
    public function index()
    {
        $user = auth()->user();

        $lastVerification = $user->securityActivities()
            ->where('event', 'OTP Verified')
            ->latest()
            ->first();

        $lastOtp = $user->securityActivities()
            ->where('event', 'OTP Generated')
            ->latest()
            ->first();

        $failedAttempts = $user->securityActivities()
            ->where('event', 'OTP Failed')
            ->count();

        return view('security.settings', [
            'user' => $user,
            'lastVerification' => $lastVerification,
            'lastOtp' => $lastOtp,
            'failedAttempts' => $failedAttempts,
            'resendCooldown' => $user->resendCooldownSeconds(),
        ]);
    }

    /**
     * Enable 2FA.
     */
    public function enable()
    {
        $user = auth()->user();

        $user->two_factor_enabled = true;
        $user->two_factor_failed_attempts = 0;
        $user->two_factor_locked_until = null;

        $user->save();

        $user->recordSecurityActivity(
            '2FA Enabled',
            'Two-factor authentication was enabled.'
        );

        return back()->with(
            'success',
            'Two-factor authentication has been enabled.'
        );
    }

    /**
     * Disable 2FA.
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'current_password',
            ],
        ]);

        $user = auth()->user();

        $user->two_factor_enabled = false;
        $user->two_factor_failed_attempts = 0;
        $user->two_factor_locked_until = null;

        $user->resetTwoFactorCode();

        $user->save();

        $user->recordSecurityActivity(
            '2FA Disabled',
            'Two-factor authentication was disabled.'
        );

        return back()->with(
            'success',
            'Two-factor authentication has been disabled.'
        );
    }
}