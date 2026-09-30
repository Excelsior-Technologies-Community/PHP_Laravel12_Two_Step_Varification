<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorVerify
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        if (!$user->two_factor_enabled) {
            return $next($request);
        }

        /**
         * Check lock.
         */
        if ($user->isTwoFactorLocked()) {
            $user->resetTwoFactorCode();

            auth()->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'otp' =>
                        'Two-factor verification is temporarily locked because of too many failed attempts.',
                ]);
        }

        /**
         * No OTP means verification is complete.
         */
        if (!$user->two_factor_code) {
            return $next($request);
        }

        /**
         * Check expiration.
         */
        if ($user->isTwoFactorCodeExpired()) {
            $user->recordSecurityActivity(
                'OTP Expired',
                'The OTP expired before verification was completed.'
            );

            $user->resetTwoFactorCode();

            auth()->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'otp' =>
                        'Your OTP has expired. Please login again.',
                ]);
        }

        /**
         * Force verification.
         */
        if (!$request->is('verify*')) {
            return redirect()->route('verify.index');
        }

        return $next($request);
    }
}