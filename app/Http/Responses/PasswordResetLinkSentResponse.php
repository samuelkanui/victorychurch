<?php

namespace App\Http\Responses;

use App\Services\OtpService;
use Laravel\Fortify\Contracts\PasswordResetResponse as PasswordResetResponseContract;

class PasswordResetLinkSentResponse implements \Laravel\Fortify\Contracts\PasswordResetLinkRequestedResponse
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function toResponse($request)
    {
        // Generate and send OTP instead of reset link
        $this->otpService->generate($request->email, 'reset');

        // Store email in session
        $request->session()->put('otp_email', $request->email);
        $request->session()->put('otp_type', 'reset');

        return redirect()->route('otp.show')->with('status', 'A verification code has been sent to your email.');
    }
}
