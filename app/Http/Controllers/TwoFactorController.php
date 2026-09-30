<?php

namespace App\Http\Controllers;

use App\Notifications\SendTwoFactorCode;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    /**
     * Show OTP verification page.
     */
    public function index()
    {
        $user = auth()->user();

        if (!$user->two_factor_enabled) {
            return redirect()->route('dashboard');
        }

        if ($user->isTwoFactorLocked()) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'otp' => 'Too many failed OTP attempts. Please try again after 15 minutes.',
                ]);
        }

        return view('auth.verify', [
            'user' => $user,
            'resendCooldown' => $user->resendCooldownSeconds(),
        ]);
    }

    /**
     * Verify OTP.
     */
    public function store(Request $request)
    {
        $request->validate([
            'two_factor_code' => [
                'required',
                'digits:6',
            ],
        ]);

        $user = auth()->user();

        if (!$user->two_factor_enabled) {
            return redirect()->route('dashboard');
        }

        /**
         * Check lock.
         */
        if ($user->isTwoFactorLocked()) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'otp' => 'Too many failed attempts. Please try again after the lock period.',
                ]);
        }

        /**
         * Check expiration.
         */
        if ($user->isTwoFactorCodeExpired()) {
            $user->recordSecurityActivity(
                'OTP Expired',
                'The user attempted to verify an expired OTP.'
            );

            $user->resetTwoFactorCode();

            return redirect()
                ->route('login')
                ->withErrors([
                    'otp' => 'Your OTP has expired. Please login again to receive a new OTP.',
                ]);
        }

        /**
         * Check OTP.
         */
        if (
            $request->input('two_factor_code')
            !== (string) $user->two_factor_code
        ) {
            $user->registerFailedTwoFactorAttempt();

            if ($user->isTwoFactorLocked()) {
                $user->resetTwoFactorCode();

                auth()->logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()
                    ->route('login')
                    ->withErrors([
                        'otp' => 'Too many failed OTP attempts. Your verification has been locked for 15 minutes.',
                    ]);
            }

            $remaining = max(
                0,
                5 - $user->two_factor_failed_attempts
            );

            return back()
                ->withErrors([
                    'two_factor_code' =>
                        "The OTP is incorrect. {$remaining} attempt(s) remaining.",
                ])
                ->withInput();
        }

        /**
         * Successful verification.
         */
        $user->recordSecurityActivity(
            'OTP Verified',
            'Two-factor authentication was successfully completed.'
        );

        $user->resetTwoFactorCode();
        $user->clearTwoFactorAttempts();

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Two-factor verification completed successfully.'
            );
    }

    /**
     * Resend OTP.
     */
    public function resend()
    {
        $user = auth()->user();

        if (!$user->two_factor_enabled) {
            return redirect()->route('dashboard');
        }

        /**
         * Check lock.
         */
        if ($user->isTwoFactorLocked()) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'otp' => 'OTP verification is temporarily locked.',
                ]);
        }

        /**
         * Check resend cooldown.
         */
        if (!$user->canResendTwoFactorCode()) {
            $seconds = $user->resendCooldownSeconds();

            return redirect()
                ->route('verify.index')
                ->withErrors([
                    'otp' =>
                        "Please wait {$seconds} seconds before requesting another OTP.",
                ]);
        }

        $user->generateTwoFactorCode();

        $user->notify(
            new SendTwoFactorCode()
        );

        $user->recordSecurityActivity(
            'OTP Resent',
            'A new OTP was sent to the registered email address.'
        );

        return redirect()
            ->route('verify.index')
            ->with(
                'message',
                'A new OTP has been sent to your email address.'
            );
    }
}