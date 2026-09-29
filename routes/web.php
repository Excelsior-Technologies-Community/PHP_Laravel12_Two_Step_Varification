<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SecurityActivityController;
use App\Http\Controllers\SecuritySettingsController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})
    ->middleware(['auth', 'verified', 'twofactor'])
    ->name('dashboard');


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [
        ProfileController::class,
        'edit'
    ])->name('profile.edit');

    Route::patch('/profile', [
        ProfileController::class,
        'update'
    ])->name('profile.update');

    Route::delete('/profile', [
        ProfileController::class,
        'destroy'
    ])->name('profile.destroy');


    /*
    |--------------------------------------------------------------------------
    | Two-Factor Verification
    |--------------------------------------------------------------------------
    */

    Route::get('/verify', [
        TwoFactorController::class,
        'index'
    ])->name('verify.index');

    Route::post('/verify', [
        TwoFactorController::class,
        'store'
    ])->name('verify.store');

    Route::get('/verify/resend', [
        TwoFactorController::class,
        'resend'
    ])->name('verify.resend');


    /*
    |--------------------------------------------------------------------------
    | Security Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/security/dashboard', [
        SecurityActivityController::class,
        'dashboard'
    ])->name('security.dashboard');


    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */

    Route::get('/security/settings', [
        SecuritySettingsController::class,
        'index'
    ])->name('security.settings');

    Route::post('/security/enable', [
        SecuritySettingsController::class,
        'enable'
    ])->name('security.enable');

    Route::post('/security/disable', [
        SecuritySettingsController::class,
        'disable'
    ])->name('security.disable');
});


require __DIR__.'/auth.php';