<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $type === 'registration' ? 'Verify Your Email' : 'Reset Your Password' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 30px;
            margin: 20px 0;
        }
        .code-box {
            background: white;
            border: 2px dashed #4F46E5;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #4F46E5;
            font-family: 'Courier New', monospace;
        }
        .warning {
            background: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 12px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            color: #6B7280;
            font-size: 14px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>{{ $type === 'registration' ? 'Welcome to ' . config('app.name') . '!' : 'Password Reset Request' }}</h2>
        
        @if($type === 'registration')
            <p>Thank you for registering! To complete your registration and access your account, please use the verification code below:</p>
        @else
            <p>We received a request to reset your password. Use the code below to proceed:</p>
        @endif

        <div class="code-box">
            <div class="code">{{ $code }}</div>
        </div>

        <p style="text-align: center; color: #6B7280;">This code will expire in <strong>10 minutes</strong>.</p>

        <div class="warning">
            <strong>⚠️ Security Notice:</strong> Never share this code with anyone. Our team will never ask for your verification code.
        </div>

        @if($type === 'reset')
            <p>If you didn't request a password reset, please ignore this email or contact support if you have concerns.</p>
        @endif
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        <p>This is an automated message, please do not reply to this email.</p>
    </div>
</body>
</html>
