<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Activate Account - evoory</title>
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
            <h1 style="font-size: 1.5em; margin-bottom: 1em; font-weight: 700; color: #ffffff;">Hi {{ $mailData['name'] }},</h1>
            
            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-bottom: 15px;">
                Thank you for registering on evoory.
            </p>
            
            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-bottom: 20px;">
                Click here to confirm your email address:
            </p>
            
            <!-- Activate Button -->
            <table cellpadding="0" cellspacing="0" style="display: inline-block; margin: 20px 0;">
                <tbody>
                    <tr>
                        <td style="border-radius: 21.5px; background-color: #C1F11D; text-align: center;">
                            <a href="{{ url('activate-account/'.$mailData['email'].'/'.$mailData['random']) }}" style="outline: 0; color: #000000; text-decoration: none; font-size: 14px; font-family: Arial, Helvetica, sans-serif; font-weight: 600; line-height: 20px; padding: 8px 22px; display: block;">
                                Activate account
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-top: 25px;">
                Button not working? Copy &amp; paste the link below in your browser:
            </p>
            <p style="font-size: 13px; color: #C1F11D; background: #131616; border: 1px solid #2a2a2a; padding: 10px 12px; border-radius: 5px; word-break: break-all; margin: 10px 0;">
                {{ url('activate-account/'.$mailData['email'].'/'.$mailData['random']) }}
            </p>
            
            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-top: 25px;">Thank You</p>
            
            <!-- Footer -->
            <p style="font-size: 10pt; line-height: 1.5; color: #bebebe; border-top: 2px solid #2a2a2a; margin-top: 20px; padding-top: 15px; text-align: justify;">
                We sent this email because <strong><a href="mailto:{{ $mailData['email'] }}" style="color: #C1F11D; text-decoration: none; outline: 0;">{{ $mailData['email'] }}</a></strong> was registered with us. If you did not register, please ignore this email as someone has done so in error. No action is required, you will stop receiving email from us and we will automatically and permanently delete this email from our records.
            </p>
            
            <!-- Bottom Links -->
            <div style="text-align: center; padding-top: 5px; margin-top: 15px; border-top: 2px solid #2a2a2a; font-size: 9pt;">
                <a href="{{ url('/') }}" style="color: #C1F11D; outline: 0; text-decoration: underline;">Go to evoory.com</a>
            </div>
            
        </div>
    </div>
</body>
</html>

