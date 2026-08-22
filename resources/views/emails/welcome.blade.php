<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to evoory</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0D1011; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
    <div style="background-color: #0D1011; padding: 20px;">
        <div style="padding: 20px; max-width: 670px; margin: 0 auto; background-color: #0D1011; color: #ffffff;">

            <!-- Header -->
            <div style="border-bottom: 2px solid #2a2a2a; padding-bottom: 5px; margin-bottom: 20px;">
                <a href="{{ url('/') }}" title="evoory" style="color: #C1F11D; text-decoration: none; outline: 0;">
                    <img alt="evoory" src="https://assets.evoory.com/uploads/1776856883_Logo.png" width="180">
                </a>
            </div>

            <!-- Content -->
            <h1 style="font-size: 1.5em; margin-bottom: 1em; font-weight: 700; color: #ffffff;">Welcome to evoory{{ $user ? ', ' . $user->name : '' }}!</h1>

            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-bottom: 15px;">
                Your account has been successfully created. We're excited to have you join our community.
            </p>

            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-bottom: 15px;">
                Your account is now active and you can start exploring all the features we have to offer:
            </p>

            <ul style="color: #ffffff; font-size: 14px; line-height: 1.8; margin-bottom: 20px;">
                <li>Create and manage your profile</li>
                <li>Connect with other users</li>
                <li>Purchase packages and upgrades</li>
                <li>And much more!</li>
            </ul>

            @if($user)
            <!-- Account Details Box -->
            <div style="background-color: #131616; border: 2px solid #C1F11D; border-radius: 5px; padding: 20px; margin: 25px 0;">
                <p style="font-size: 14px; color: #bebebe; margin: 0 0 10px 0;">Account Details:</p>
                <p style="font-size: 14px; color: #ffffff; margin: 0 0 5px 0;"><strong style="color: #C1F11D;">Name:</strong> {{ $user->name }}</p>
                <p style="font-size: 14px; color: #ffffff; margin: 0 0 5px 0;"><strong style="color: #C1F11D;">Email:</strong> {{ $user->email }}</p>
                <p style="font-size: 14px; color: #ffffff; margin: 0;"><strong style="color: #C1F11D;">Registration Date:</strong> {{ $user->created_at->format('F j, Y') }}</p>
            </div>
            @endif

            <!-- Visit Button -->
            <table cellpadding="0" cellspacing="0" style="display: inline-block; margin: 20px 0;">
                <tbody>
                    <tr>
                        <td style="border-radius: 21.5px; background-color: #C1F11D; text-align: center;">
                            <a href="{{ url('/') }}" style="outline: 0; color: #000000; text-decoration: none; font-size: 14px; font-family: Arial, Helvetica, sans-serif; font-weight: 600; line-height: 20px; padding: 8px 22px; display: block;">
                                Visit evoory
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-top: 25px;">
                If you have any questions or need assistance, please don't hesitate to contact our support team.
            </p>

            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-top: 25px;">Best regards,<br>The evoory Team</p>

            <!-- Footer -->
            <p style="font-size: 10pt; line-height: 1.5; color: #bebebe; border-top: 2px solid #2a2a2a; margin-top: 20px; padding-top: 15px; text-align: justify;">
                You're receiving this email because you recently created an account on evoory.
                If you have any questions, please contact our support team at
                <a href="mailto:support@evoory.com" style="color: #C1F11D; text-decoration: none; outline: 0;">support@evoory.com</a>
            </p>

            <!-- Bottom Links -->
            <div style="text-align: center; padding-top: 5px; margin-top: 15px; border-top: 2px solid #2a2a2a; font-size: 9pt;">
                <a href="{{ url('/') }}" style="color: #C1F11D; outline: 0; text-decoration: underline;">Go to evoory.com</a>
                <span style="color: #bebebe;"> &copy; {{ date('Y') }} evoory. All rights reserved.</span>
            </div>

        </div>
    </div>
</body>
</html>
