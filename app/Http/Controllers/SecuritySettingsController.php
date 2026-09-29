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

        return view('security.settings', [
            'user' => $user,
            'lastVerification' => $lastVerification,
            'lastOtp' => $lastOtp,
        ]);
    }

    /**
     * Enable 2FA.
     */
    public function enable()
    {
        $user = auth()->user();

        $user->two_factor_enabled = true;
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