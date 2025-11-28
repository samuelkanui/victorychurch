<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Google authentication failed.');
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Existing user - update Google ID if not present and log them in
            if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'profile_photo_path' => $user->profile_photo_path ?? $googleUser->getAvatar(),
                ]);
            }
            
            // Check if user is active
            if (!$user->is_active) {
                return redirect()->route('login')
                    ->withErrors(['email' => 'Your account is not active. Please verify your email.']);
            }
            
            Auth::login($user);
            return redirect()->route('dashboard');
        } else {
            // New user - create as inactive and send OTP
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password' => Hash::make(Str::random(24)), // Random secure password
                'role' => 'member', // Default role
                'is_active' => false, // Require OTP verification
                'email_verified_at' => null, // Will be set after OTP verification
                'profile_photo_path' => $googleUser->getAvatar(),
            ]);
            
            // Generate and send OTP
            app(\App\Services\OtpService::class)->generate($user->email, 'registration');
            
            // Store email in session for OTP verification
            session(['otp_email' => $user->email, 'otp_type' => 'registration']);
            
            return redirect()->route('otp.show')
                ->with('status', 'A verification code has been sent to your email.');
        }
    }
}
