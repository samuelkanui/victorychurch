<?php

namespace App\Http\Responses;

use App\Services\OtpService;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $user = $request->user();

        // Check if user is inactive (needs OTP verification)
        if (!$user->is_active) {
            // Generate and send OTP
            $this->otpService->generate($user->email, 'registration');

            // Store email in session for OTP verification
            $request->session()->put('otp_email', $user->email);
            $request->session()->put('otp_type', 'registration');

            // Logout the user (they just logged in)
            auth()->logout();

            return redirect()->route('otp.show')
                ->with('status', 'Please verify your email. A verification code has been sent to your email.');
        }

        // User is active, redirect directly based on their role
        $redirectTo = match(true) {
            $user->isAdmin()  => route('admin.dashboard'),
            $user->isLeader() => route('leader.dashboard'),
            default           => route('member.dashboard'),
        };

        return redirect()->intended($redirectTo);
    }
}
