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
            // Update existing user with Google ID if not present
            if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'profile_photo_path' => $user->profile_photo_path ?? $googleUser->getAvatar(),
                ]);
            }
        } else {
            // Create new user
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password' => Hash::make(Str::random(24)), // Random secure password
                'role' => 'member', // Default role
                'is_active' => true,
                'email_verified_at' => now(), // Google emails are verified
                'profile_photo_path' => $googleUser->getAvatar(),
            ]);
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
