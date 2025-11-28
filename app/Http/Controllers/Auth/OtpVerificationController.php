<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Inertia\Inertia;

class OtpVerificationController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show the OTP verification form
     */
    public function show(Request $request)
    {
        $email = $request->session()->get('otp_email');
        $type = $request->session()->get('otp_type', 'registration');

        if (!$email) {
            return redirect()->route('login');
        }

        return Inertia::render('auth/VerifyOtp', [
            'email' => $email,
            'type' => $type,
        ]);
    }

    /**
     * Verify the OTP code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
            'type' => 'required|in:registration,reset',
        ]);

        if (!$this->otpService->verify($request->email, $request->code, $request->type)) {
            return back()->withErrors(['code' => 'Invalid or expired verification code.']);
        }

        if ($request->type === 'registration') {
            // Mark email as verified and activate account
            $user = User::where('email', $request->email)->first();
            
            if ($user) {
                $user->email_verified_at = now();
                $user->is_active = true; // Activate the account
                $user->save();

                Auth::login($user);
                $request->session()->forget(['otp_email', 'otp_type']);

                return redirect()->route('dashboard');
            }
        } else {
            // For password reset, redirect to reset password form
            $request->session()->put('reset_email_verified', $request->email);
            $request->session()->forget(['otp_email', 'otp_type']);

            return redirect()->route('password.reset.form');
        }

        return back()->withErrors(['code' => 'Verification failed.']);
    }

    /**
     * Resend OTP code
     */
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'type' => 'required|in:registration,reset',
        ]);

        $this->otpService->generate($request->email, $request->type);

        return back()->with('status', 'A new verification code has been sent to your email.');
    }
}
