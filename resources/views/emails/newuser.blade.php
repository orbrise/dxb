<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Activate Account - Massage Republic</title>
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
            <h1 style="font-size: 1.5em; margin-bottom: 1em; font-weight: 700; color: #ffffff;">Hi {{ $mailData['name'] }},</h1>
            
            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-bottom: 15px;">
                Thank you for registering on Massage Republic.
            </p>
            
            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-bottom: 20px;">
                Click here to confirm your email address:
            </p>
            
            <!-- Activate Button -->
            <table cellpadding="0" cellspacing="0" style="display: inline-block; margin: 20px 0;">
                <tbody>
                    <tr>
                        <td style="border-radius: 3px; background-color: #F4B827; text-align: center;">
                            <a href="{{ url('activate-account/'.$mailData['email'].'/'.$mailData['random']) }}" style="outline: 0; color: #000000; text-decoration: none; font-size: 20px; font-family: Arial, Helvetica, sans-serif; font-weight: bold; line-height: 20px; padding: 12px 30px; display: block; text-shadow: #FDE877 0px 1px 0px;">
                                Activate account
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-top: 25px;">
                Button not working? Copy &amp; paste the link below in your browser:
            </p>
            <p style="font-size: 12px; color: #1155cc; background: #808098; padding: 8px; word-break: break-all; margin: 10px 0;">
                {{ url('activate-account/'.$mailData['email'].'/'.$mailData['random']) }}
            </p>
            
            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-top: 25px;">Thank You</p>
            
            <!-- Footer -->
            <p style="font-size: 10pt; line-height: 1.5; color: #bebebe; border-top: 2px solid #333333; margin-top: 20px; padding-top: 15px; text-align: justify;">
                We sent this email because <strong><a href="mailto:{{ $mailData['email'] }}" style="color: #F4B827; text-decoration: none; outline: 0;">{{ $mailData['email'] }}</a></strong> was registered with us. If you did not register, please ignore this email as someone has done so in error. No action is required, you will stop receiving email from us and we will automatically and permanently delete this email from our records.
            </p>
            
            <!-- Bottom Links -->
            <div style="text-align: center; padding-top: 5px; margin-top: 15px; border-top: 2px solid #333333; font-size: 9pt;">
                <a href="{{ url('/') }}" style="color: #F4B827; outline: 0; text-decoration: underline;">Go to MassageRepublic.com</a>
                <span style="color: #bebebe;"> - Site blocked? Try: </span>
                <a target="_blank" href="https://ae.massagerepublic.co.co" style="color: #F4B827; outline: 0; text-decoration: underline;">Escorts.ninja</a>
                <span style="color: #bebebe;"> or </span>
                <a target="_blank" href="https://ae.massagerepublic.co.co" style="color: #F4B827; outline: 0; text-decoration: underline;">ae.MassageRepublic.co.co</a>
            </div>
            
        </div>
    </div>
</body>
</html>

