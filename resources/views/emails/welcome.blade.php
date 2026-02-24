<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to DXB Platform</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #232323;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .button {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to Massage Republic Platform!</h1>
        <p>Your account has been successfully created</p>
    </div>
    
    <div class="content">
        <h2>Hello{{ $user ? ' ' . $user->name : '' }}!</h2>
        
        <p>Welcome to the Massage Republic Platform! We're excited to have you join our community.</p>
        
        <p>Your account is now active and you can start exploring all the features we have to offer:</p>
        
        <ul>
            <li>Create and manage your profile</li>
            <li>Connect with other users</li>
            <li>Purchase packages and upgrades</li>
            <li>And much more!</li>
        </ul>
        
        @if($user)
        <p><strong>Account Details:</strong></p>
        <ul>
            <li><strong>Name:</strong> {{ $user->name }}</li>
            <li><strong>Email:</strong> {{ $user->email }}</li>
            <li><strong>Registration Date:</strong> {{ $user->created_at->format('F j, Y') }}</li>
        </ul>
        @endif
        
        <div style="text-align: center;">
            <a href="{{ url('/') }}" class="button">Visit Platform</a>
        </div>
        
        <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>
        
        <p>Best regards,<br>
        The DXB Platform Team</p>
    </div>
    
    <div class="footer">
        <p>This email was sent from Massage Republic Platform using your configured SMTP settings.</p>
        <p>© {{ date('Y') }} DXB Platform. All rights reserved.</p>
    </div>
</body>
</html>