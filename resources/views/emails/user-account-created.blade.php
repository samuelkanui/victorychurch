<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Your Account Has Been Created</title>
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
                                Welcome to FOV! 🎉
                            </h1>
                            <p style="margin: 0; font-size: 16px; color: rgba(255, 255, 255, 0.9); font-weight: 500;">
                                Force of Victory Youth Church
                            </p>
                        </td>
                    </tr>

                    <!-- Content Section -->
                    <tr>
                        <td style="padding: 50px 40px;">
                            <p style="margin: 0 0 15px; font-size: 20px; color: #111827; font-weight: 700;">
                                Hello {{ $user->name }}! 👋
                            </p>
                            <p style="margin: 0 0 30px; font-size: 16px; color: #4b5563; line-height: 1.6;">
                                An administrator has created an account for you. Your account has been successfully set up with the following credentials:
                            </p>

                            <!-- Credentials Card -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); border: 2px solid #8b5cf6; border-radius: 12px; padding: 30px;">
                                        
                                        <!-- Email -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 20px;">
                                            <tr>
                                                <td style="background-color: #ffffff; border-radius: 8px; padding: 20px;">
                                                    <p style="margin: 0 0 8px; font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; font-weight: 700;">
                                                        📧 EMAIL ADDRESS
                                                    </p>
                                                    <p style="margin: 0; font-size: 18px; color: #111827; font-weight: 600; font-family: 'Courier New', monospace; word-break: break-all;">
                                                        {{ $user->email }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Password -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 20px;">
                                            <tr>
                                                <td style="background-color: #ffffff; border-radius: 8px; padding: 20px;">
                                                    <p style="margin: 0 0 8px; font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; font-weight: 700;">
                                                        🔑 TEMPORARY PASSWORD
                                                    </p>
                                                    <p style="margin: 0; font-size: 18px; color: #111827; font-weight: 600; font-family: 'Courier New', monospace;">
                                                        {{ $password }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Role -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="background-color: #ffffff; border-radius: 8px; padding: 20px;">
                                                    <p style="margin: 0 0 12px; font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; font-weight: 700;">
                                                        👤 ACCOUNT ROLE
                                                    </p>
                                                    @php
                                                        $roleColors = [
                                                            'admin' => 'background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; border: 2px solid #dc2626;',
                                                            'leader' => 'background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1e40af; border: 2px solid #3b82f6;',
                                                            'member' => 'background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46; border: 2px solid #10b981;'
                                                        ];
                                                        $roleStyle = $roleColors[$user->role] ?? $roleColors['member'];
                                                    @endphp
                                                    <span style="display: inline-block; padding: 8px 20px; border-radius: 20px; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; {{ $roleStyle }}">
                                                        {{ ucfirst($user->role) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 35px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ config('app.url') }}/login" style="display: inline-block; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: #ffffff; padding: 18px 45px; text-decoration: none; border-radius: 10px; font-weight: 700; font-size: 16px; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);">
                                            🚀 Login to Your Account
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Divider -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 35px 0;">
                                <tr>
                                    <td style="border-top: 1px solid #e5e7eb;"></td>
                                </tr>
                            </table>

                            <!-- Security Notice -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-left: 5px solid #f59e0b; border-radius: 8px; padding: 25px;">
                                        <p style="margin: 0 0 15px; font-size: 16px; color: #92400e; font-weight: 700;">
                                            🔒 Important Security Notice
                                        </p>
                                        <ul style="margin: 0; padding-left: 20px;">
                                            <li style="margin: 8px 0; font-size: 14px; color: #78350f; line-height: 1.6;">
                                                <strong>Change your password immediately</strong> after your first login
                                            </li>
                                            <li style="margin: 8px 0; font-size: 14px; color: #78350f; line-height: 1.6;">
                                                Never share your password with anyone, including church staff
                                            </li>
                                            <li style="margin: 8px 0; font-size: 14px; color: #78350f; line-height: 1.6;">
                                                Keep this email secure or delete it after changing your password
                                            </li>
                                            <li style="margin: 8px 0; font-size: 14px; color: #78350f; line-height: 1.6;">
                                                Contact your administrator if you notice any suspicious activity
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 30px 0 0; font-size: 14px; color: #6b7280; text-align: center; line-height: 1.6;">
                                If you have any questions or need assistance, please contact your church administrator.
                            </p>
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
