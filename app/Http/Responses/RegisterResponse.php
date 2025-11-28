<?php

namespace App\Http\Responses;

use App\Services\OtpService;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
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

        // Generate and send OTP
        $this->otpService->generate($user->email, 'registration');

        // Store email in session for OTP verification
        $request->session()->put('otp_email', $user->email);
        $request->session()->put('otp_type', 'registration');

        // Logout the user (Fortify auto-logs them in)
        auth()->logout();

        return redirect()->route('otp.show')->with('status', 'A verification code has been sent to your email.');
    }
}
