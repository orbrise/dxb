<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile Created - Massage Republic</title>
</head>
<body style="margin: 0; padding: 0; background-color: #232323; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
    <div style="background-color: #232323; padding: 20px;">
        <div style="padding: 20px; max-width: 670px; margin: 0 auto; background-color: #232323; color: #ffffff;">
            
            <!-- Header -->
            <div style="border-bottom: 2px solid #333333; padding-bottom: 5px; margin-bottom: 20px;">
                <a href="{{ url('/') }}" title="Massage Republic" style="color: #F4B827; text-decoration: none; outline: 0;">
                    <img alt="Massage Republic" src="https://assets.massagerepublic.com.co/assets/images/web/maillogo.gif" width="309">
                </a>
            </div>
            
            <!-- Content -->
            <h1 style="font-size: 1.5em; margin-bottom: 1em; font-weight: 700; color: #ffffff;">Hello!</h1>
            
            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-bottom: 15px;">
                Congratulations! Your profile <strong>{{ $profileName }}</strong> has been successfully created on Massage Republic.
            </p>
            
            <!-- Profile Info Box -->
            <div style="background-color: #2c2c2c; border: 2px solid #F4B827; border-radius: 5px; padding: 20px; margin: 25px 0;">
                <p style="font-size: 14px; color: #bebebe; margin: 0 0 10px 0;">Profile Details:</p>
                <p style="font-size: 18px; font-weight: bold; color: #F4B827; margin: 0 0 10px 0;">{{ $profileName }}</p>
                @if($packageName)
                <p style="font-size: 14px; color: #ffffff; margin: 0;">Package: <strong style="color: #F4B827;">{{ $packageName }}</strong></p>
                @else
                <p style="font-size: 14px; color: #ffffff; margin: 0;">Package: <strong style="color: #F4B827;">Free Profile</strong></p>
                @endif
            </div>
            
            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-bottom: 15px;">
                You can now start managing your profile and connecting with clients.
            </p>
            
            <!-- View Profile Button -->
            <table cellpadding="0" cellspacing="0" style="display: inline-block; margin: 20px 0;">
                <tbody>
                    <tr>
                        <td style="border-radius: 3px; background-color: #F4B827; text-align: center;">
                            <a href="{{ $profileUrl }}" style="outline: 0; color: #000000; text-decoration: none; font-size: 20px; font-family: Arial, Helvetica, sans-serif; font-weight: bold; line-height: 20px; padding: 12px 30px; display: block; text-shadow: #FDE877 0px 1px 0px;">
                                View Your Profile
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-top: 25px;">
                If you're having trouble clicking the "View Your Profile" button, copy and paste the URL below into your web browser:
            </p>
            <p style="font-size: 12px; color: #1155cc; background: #808098; padding: 8px; word-break: break-all; margin: 10px 0;">
                {{ $profileUrl }}
            </p>
            
            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-top: 25px;">Thank You</p>
            
            <!-- Footer -->
            <p style="font-size: 10pt; line-height: 1.5; color: #bebebe; border-top: 2px solid #333333; margin-top: 20px; padding-top: 15px; text-align: justify;">
                You're receiving this email because you recently created a new profile on Massage Republic. 
                If you have any questions or need assistance, please contact our support team at 
                <a href="mailto:support@massagerepublic.com.co" style="color: #F4B827; text-decoration: none; outline: 0;">support@massagerepublic.com.co</a>
            </p>
            
            <!-- Bottom Links -->
            <div style="text-align: center; padding-top: 5px; margin-top: 15px; border-top: 2px solid #333333; font-size: 9pt;">
                <a href="{{ url('/') }}" style="color: #F4B827; outline: 0; text-decoration: underline;">Go to Massage Republic</a>
                <span style="color: #bebebe;"> | </span>
                <a href="{{ url('/help') }}" style="color: #F4B827; outline: 0; text-decoration: underline;">Help Center</a>
                <span style="color: #bebebe;"> | </span>
                <a href="{{ url('/contact') }}" style="color: #F4B827; outline: 0; text-decoration: underline;">Contact Us</a>
            </div>
            
        </div>
    </div>
</body>
</html>
