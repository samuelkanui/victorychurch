<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    $user = auth()->user();
    
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isLeader()) {
        return redirect()->route('leader.dashboard');
    } else {
        // For members, redirect to member dashboard
        return redirect()->route('member.dashboard');
    }
})->middleware(['auth', 'verified', 'active', 'prevent.back'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
require __DIR__.'/leader.php';
require __DIR__.'/member.php';

// OTP Verification Routes
Route::get('verify-otp', [App\Http\Controllers\Auth\OtpVerificationController::class, 'show'])->name('otp.show');
Route::post('verify-otp', [App\Http\Controllers\Auth\OtpVerificationController::class, 'verify'])->name('otp.verify');
Route::post('resend-otp', [App\Http\Controllers\Auth\OtpVerificationController::class, 'resend'])->name('otp.resend');

// Google Authentication Routes
Route::get('auth/google', [App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'handleGoogleCallback']);

