<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile Created - evoory</title>
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
            <h1 style="font-size: 1.5em; margin-bottom: 1em; font-weight: 700; color: #ffffff;">Hello!</h1>
            
            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-bottom: 15px;">
                Congratulations! Your profile <strong>{{ $profileName }}</strong> has been successfully created on evoory.
            </p>
            
            <!-- Profile Info Box -->
            <div style="background-color: #131616; border: 2px solid #C1F11D; border-radius: 5px; padding: 20px; margin: 25px 0;">
                <p style="font-size: 14px; color: #bebebe; margin: 0 0 10px 0;">Profile Details:</p>
                <p style="font-size: 18px; font-weight: bold; color: #C1F11D; margin: 0 0 10px 0;">{{ $profileName }}</p>
                @if($packageName)
                <p style="font-size: 14px; color: #ffffff; margin: 0;">Package: <strong style="color: #C1F11D;">{{ $packageName }}</strong></p>
                @else
                <p style="font-size: 14px; color: #ffffff; margin: 0;">Package: <strong style="color: #C1F11D;">Free Profile</strong></p>
                @endif
            </div>
            
            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-bottom: 15px;">
                You can now start managing your profile and connecting with clients.
            </p>
            
            <!-- View Profile Button -->
            <table cellpadding="0" cellspacing="0" style="display: inline-block; margin: 40px 0 15px 0;">
                <tbody>
                    <tr>
                        <td style="border-radius: 21.5px; background-color: #C1F11D; text-align: center;">
                            <a href="{{ $profileUrl }}" style="outline: 0; color: #000000; text-decoration: none; font-size: 14px; font-family: Arial, Helvetica, sans-serif; font-weight: 600; line-height: 20px; padding: 8px 22px; display: block;">
                                View Your Profile
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-top: 25px;">
                If you're having trouble clicking the "View Your Profile" button, copy and paste the URL below into your web browser:
            </p>
            <p style="font-size: 13px; color: #C1F11D; background: #131616; border: 1px solid #2a2a2a; padding: 10px 12px; border-radius: 5px; word-break: break-all; margin: 10px 0;">
                {{ $profileUrl }}
            </p>
            
            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-top: 25px;">Thank You</p>
            
            <!-- Footer -->
            <p style="font-size: 10pt; line-height: 1.5; color: #bebebe; border-top: 2px solid #2a2a2a; margin-top: 20px; padding-top: 15px; text-align: justify;">
                You're receiving this email because you recently created a new profile on evoory. 
                If you have any questions or need assistance, please contact our support team at 
                <a href="mailto:support@evoory.com" style="color: #C1F11D; text-decoration: none; outline: 0;">support@evoory.com</a>
            </p>
            
            <!-- Bottom Links -->
            <div style="text-align: center; padding-top: 5px; margin-top: 15px; border-top: 2px solid #2a2a2a; font-size: 9pt;">
                <a href="{{ url('/') }}" style="color: #C1F11D; outline: 0; text-decoration: underline;">Go to evoory</a>
                <span style="color: #bebebe;"> | </span>
                <a href="{{ url('/help') }}" style="color: #C1F11D; outline: 0; text-decoration: underline;">Help Center</a>
                <span style="color: #bebebe;"> | </span>
                <a href="{{ url('/contact') }}" style="color: #C1F11D; outline: 0; text-decoration: underline;">Contact Us</a>
            </div>
            
        </div>
    </div>
</body>
</html>
