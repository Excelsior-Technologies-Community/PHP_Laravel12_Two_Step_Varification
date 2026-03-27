<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    // 1. OTP Enter karva mate nu Form dekhadva
    public function index()
    {
        return view('auth.verify');
    }

    // 2. OTP Check (Verify) karva mate
    public function store(Request $request)
{
    $request->validate(['two_factor_code' => 'required|integer']);

    $user = auth()->user();

    if($request->input('two_factor_code') == $user->two_factor_code) {
        $user->resetTwoFactorCode();
        return redirect()->route('dashboard');
    }

    return redirect()->back()->withErrors(['two_factor_code' => 'OTP sacho nathi.']);
}
    // 3. Jo user ne fari OTP joito hoy (Resend)
    public function resend()
    {
        $user = auth()->user();
        $user->generateTwoFactorCode();
        $user->notify(new \App\Notifications\SendTwoFactorCode($user));

        return redirect()->back()->withMessage('Navo OTP tamara email par mokli devama avyo che.');
    }
}