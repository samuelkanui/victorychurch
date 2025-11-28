<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\Otp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    /**
     * Generate and send OTP to the given email
     */
    public function generate(string $email, string $type): string
    {
        // Delete any existing OTPs for this email and type
        Otp::where('identifier', $email)
            ->where('type', $type)
            ->delete();

        // Generate 6-digit code
        $code = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Store hashed version
        Otp::create([
            'identifier' => $email,
            'token' => Hash::make($code),
            'type' => $type,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send email
        Mail::to($email)->send(new OtpMail($code, $type));

        return $code; // Only for testing, remove in production
    }

    /**
     * Verify OTP code
     */
    public function verify(string $email, string $code, string $type): bool
    {
        $otp = Otp::where('identifier', $email)
            ->where('type', $type)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            return false;
        }

        if (!Hash::check($code, $otp->token)) {
            return false;
        }

        // Delete used OTP
        $otp->delete();

        return true;
    }

    /**
     * Check if OTP exists and is not expired
     */
    public function exists(string $email, string $type): bool
    {
        return Otp::where('identifier', $email)
            ->where('type', $type)
            ->where('expires_at', '>', now())
            ->exists();
    }
}
