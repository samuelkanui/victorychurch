<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Account Has Been Created</title>
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
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .credentials-box {
            background: white;
            border: 2px solid #4F46E5;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .credential-item {
            margin: 15px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .credential-label {
            font-weight: bold;
            color: #6B7280;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .credential-value {
            font-size: 16px;
            color: #1F2937;
            margin-top: 5px;
            font-family: 'Courier New', monospace;
        }
        .login-button {
            display: inline-block;
            background: #4F46E5;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
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
        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .role-admin { background: #FEE2E2; color: #991B1B; }
        .role-leader { background: #DBEAFE; color: #1E40AF; }
        .role-member { background: #D1FAE5; color: #065F46; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Welcome to {{ config('app.name') }}!</h2>
            <p>An administrator has created an account for you.</p>
        </div>

        <p>Hello <strong>{{ $user->name }}</strong>,</p>
        
        <p>Your account has been successfully created with the following details:</p>

        <div class="credentials-box">
            <div class="credential-item">
                <div class="credential-label">Email Address</div>
                <div class="credential-value">{{ $user->email }}</div>
            </div>
            
            <div class="credential-item">
                <div class="credential-label">Temporary Password</div>
                <div class="credential-value">{{ $password }}</div>
            </div>
            
            <div class="credential-item">
                <div class="credential-label">Account Role</div>
                <div>
                    <span class="role-badge role-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
                </div>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ config('app.url') }}/login" class="login-button">
                Login to Your Account
            </a>
        </div>

        <div class="warning">
            <strong>⚠️ Important Security Notice:</strong>
            <ul style="margin: 10px 0;">
                <li>Please change your password immediately after your first login</li>
                <li>Never share your password with anyone</li>
                <li>Keep this email secure or delete it after changing your password</li>
            </ul>
        </div>

        <p>If you have any questions or need assistance, please contact your administrator.</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        <p>This is an automated message, please do not reply to this email.</p>
    </div>
</body>
</html>
