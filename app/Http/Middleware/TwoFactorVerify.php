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

        /*
         * If 2FA is disabled, allow access.
         */
        if (!$user->two_factor_enabled) {
            return $next($request);
        }

        /*
         * No OTP means the user has already completed
         * verification.
         */
        if (!$user->two_factor_code) {
            return $next($request);
        }

        /*
         * Check OTP expiration.
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
                    'otp' => 'Your OTP has expired. Please login again.',
                ]);
        }

        /*
         * Prevent dashboard access until OTP is verified.
         */
        if (!$request->is('verify*')) {
            return redirect()->route('verify.index');
        }

        return $next($request);
    }
}