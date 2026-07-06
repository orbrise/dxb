<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Listing Verified - evoory</title>
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
            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-bottom: 15px;">
                Hi {{ $userName }},
            </p>

            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-bottom: 15px;">
                Great news &mdash; your verification photo for <strong>{{ $profileName }}</strong> has been approved.
            </p>

            <!-- Status Box -->
            <div style="background-color: #131616; border: 2px solid #C1F11D; border-radius: 5px; padding: 20px; margin: 25px 0;">
                <p style="font-size: 14px; color: #bebebe; margin: 0 0 10px 0;">Verification Status:</p>
                <p style="font-size: 18px; font-weight: bold; color: #C1F11D; margin: 0 0 10px 0;">Verified</p>
                <p style="font-size: 14px; color: #ffffff; margin: 0;">Profile: <strong style="color: #C1F11D;">{{ $profileName }}</strong></p>
            </div>

            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-bottom: 10px;">
                <strong>What this means for you:</strong>
            </p>
            <ul style="font-size: 14px; color: #ffffff; padding-left: 20px; margin: 0 0 20px;">
                <li style="margin-bottom: 6px;">Your profile now displays a "Verified" badge on your listing.</li>
                <li style="margin-bottom: 6px;">Verified listings are shown ahead of non-verified ones in search results.</li>
                <li>Users trust verified profiles more, so you can expect more enquiries.</li>
            </ul>

            @if($profileUrl)
            <!-- View Profile Button -->
            <table cellpadding="0" cellspacing="0" style="display: inline-block; margin: 20px 0;">
                <tbody>
                    <tr>
                        <td style="border-radius: 3px; background-color: #C1F11D; text-align: center;">
                            <a href="{{ $profileUrl }}" style="outline: 0; color: #000000; text-decoration: none; font-size: 20px; font-family: Arial, Helvetica, sans-serif; font-weight: bold; line-height: 20px; padding: 12px 30px; display: block; text-shadow: #FDE877 0px 1px 0px;">
                                View Your Profile
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-top: 25px;">
                If you're having trouble clicking the button, copy and paste the URL below into your web browser:
            </p>
            <p style="font-size: 12px; color: #1155cc; background: #808098; padding: 8px; word-break: break-all; margin: 10px 0;">
                {{ $profileUrl }}
            </p>
            @endif

            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-top: 25px;">
                Thanks, Claire
            </p>

            <!-- Footer -->
            <div style="border-top: 2px solid #2a2a2a; margin-top: 30px; padding-top: 15px; text-align: center;">
                <a href="{{ url('/') }}" style="color: #C1F11D; outline: 0; text-decoration: underline; font-size: 12px;">Go to evoory.com</a>
            </div>

        </div>
    </div>
</body>
</html>
