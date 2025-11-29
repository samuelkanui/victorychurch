<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $type === 'registration' ? 'Verify Your Email' : 'Reset Your Password' }}</title>
    <!--[if mso]>
    <style type="text/css">
        body, table, td {font-family: Arial, Helvetica, sans-serif !important;}
    </style>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f3f4f6;">
        <tr>
            <td style="padding: 40px 20px;">
                <!-- Main Container -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);">
                    
                    <!-- Header with Gradient -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%); padding: 50px 40px; text-align: center;">
                            <!-- Logo Circle -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <div style="width: 100px; height: 100px; background-color: #ffffff; border-radius: 50%; margin: 0 auto 25px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);">
                                            <span style="font-size: 42px; font-weight: 800; color: #6366f1; line-height: 100px;">FOV</span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            
                            <h1 style="margin: 0 0 10px; font-size: 32px; font-weight: 700; color: #ffffff; line-height: 1.2;">
                                {{ $type === 'registration' ? 'Welcome to FOV! 🎉' : 'Password Reset 🔐' }}
                            </h1>
                            <p style="margin: 0; font-size: 16px; color: rgba(255, 255, 255, 0.9); font-weight: 500;">
                                Force of Victory Youth Church
                            </p>
                        </td>
                    </tr>

                    <!-- Content Section -->
                    <tr>
                        <td style="padding: 50px 40px;">
                            @if($type === 'registration')
                                <p style="margin: 0 0 25px; font-size: 18px; color: #111827; line-height: 1.6;">
                                    <strong>Thank you for joining our community!</strong>
                                </p>
                                <p style="margin: 0 0 30px; font-size: 16px; color: #4b5563; line-height: 1.6;">
                                    To complete your registration and access your account, please use the verification code below:
                                </p>
                            @else
                                <p style="margin: 0 0 25px; font-size: 18px; color: #111827; line-height: 1.6;">
                                    <strong>Password Reset Request</strong>
                                </p>
                                <p style="margin: 0 0 30px; font-size: 16px; color: #4b5563; line-height: 1.6;">
                                    We received a request to reset your password. Use the verification code below to proceed:
                                </p>
                            @endif

                            <!-- Code Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); border: 3px dashed #8b5cf6; border-radius: 12px; padding: 40px 20px; text-align: center;">
                                        <p style="margin: 0 0 15px; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700;">
                                            YOUR VERIFICATION CODE
                                        </p>
                                        <p style="margin: 0; font-size: 56px; font-weight: 800; color: #7c3aed; letter-spacing: 8px; font-family: 'Courier New', monospace; line-height: 1;">
                                            {{ $code }}
                                        </p>
                                        <p style="margin: 20px 0 0; font-size: 14px; color: #6b7280;">
                                            Expires in <strong style="color: #dc2626;">10 minutes</strong>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Security Notice -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 35px;">
                                <tr>
                                    <td style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-left: 5px solid #f59e0b; border-radius: 8px; padding: 25px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td width="40" valign="top" style="padding-right: 15px;">
                                                    <span style="font-size: 28px;">🔒</span>
                                                </td>
                                                <td>
                                                    <p style="margin: 0 0 10px; font-size: 16px; color: #92400e; font-weight: 700;">
                                                        Security Notice
                                                    </p>
                                                    <p style="margin: 0; font-size: 14px; color: #78350f; line-height: 1.6;">
                                                        Never share this code with anyone. Our team will never ask for your verification code via email, phone, or any other medium.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            @if($type === 'reset')
                                <p style="margin: 30px 0 0; font-size: 14px; color: #6b7280; text-align: center; line-height: 1.6;">
                                    If you didn't request a password reset, please ignore this email or contact our support team.
                                </p>
                            @endif
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 40px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 15px; font-size: 18px; font-weight: 700; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                                Force of Victory Youth Church
                            </p>
                            <p style="margin: 0 0 8px; font-size: 13px; color: #6b7280;">
                                &copy; {{ date('Y') }} FOV. All rights reserved.
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #9ca3af;">
                                This is an automated message, please do not reply to this email.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
