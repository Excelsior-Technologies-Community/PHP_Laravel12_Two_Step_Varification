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

        return view('auth.verify', [
            'user' => $user,
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

        /*
         * Check OTP expiration first.
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

        /*
         * Check OTP value.
         */
        if ($request->input('two_factor_code') !== (string) $user->two_factor_code) {

            $user->recordSecurityActivity(
                'OTP Failed',
                'An incorrect OTP was entered.'
            );

            return back()
                ->withErrors([
                    'two_factor_code' => 'The OTP you entered is incorrect.',
                ])
                ->withInput();
        }

        /*
         * Successful verification.
         */
        $user->recordSecurityActivity(
            'OTP Verified',
            'Two-factor authentication was successfully completed.'
        );

        $user->resetTwoFactorCode();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Two-factor verification completed successfully.');
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