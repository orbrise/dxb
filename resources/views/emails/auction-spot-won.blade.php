<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>You Won the Spot - evoory</title>
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
            <h1 style="font-size: 1.5em; margin-bottom: 1em; font-weight: 700; color: #ffffff;">Congratulations, {{ $userName }}!</h1>

            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-bottom: 15px;">
                You've won the auction for <strong>Spot #{{ $spotNumber }}</strong> —
                {{ ucfirst($gender) }} escorts in {{ $cityName }}. Your profile
                <strong>{{ $profileName }}</strong> is now featured at the top of the listing.
            </p>

            <!-- Winner Info Box -->
            <div style="background-color: #131616; border: 2px solid #C1F11D; border-radius: 5px; padding: 20px; margin: 25px 0;">
                <p style="font-size: 14px; color: #bebebe; margin: 0 0 10px 0;">Spot Details</p>
                <p style="font-size: 18px; font-weight: bold; color: #C1F11D; margin: 0 0 10px 0;">
                    Spot #{{ $spotNumber }} — {{ ucfirst($gender) }} escorts in {{ $cityName }}
                </p>
                <p style="font-size: 14px; color: #ffffff; margin: 0 0 5px 0;">Featured profile: <strong>{{ $profileName }}</strong></p>
                <p style="font-size: 14px; color: #ffffff; margin: 0 0 5px 0;">Winning bid: <strong style="color: #C1F11D;">${{ number_format($winningBid, 2) }}</strong></p>
                <p style="font-size: 14px; color: #ffffff; margin: 0 0 5px 0;">Duration: <strong style="color: #C1F11D;">{{ $durationDays }} days</strong></p>
                @if($spotExpiryDate)
                <p style="font-size: 14px; color: #ffffff; margin: 0;">Featured until: <strong>{{ $spotExpiryDate }}</strong></p>
                @endif
            </div>

            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-bottom: 15px;">
                <strong>What happens next:</strong>
            </p>
            <ul style="color: #ffffff; font-size: 14px; line-height: 1.8; margin-bottom: 20px;">
                <li>Your profile is now pinned to the top spot in {{ $cityName }}</li>
                <li>You'll get maximum visibility on the city listing for the full {{ $durationDays }} days</li>
                <li>Expect a noticeable jump in profile views, calls, and messages</li>
                <li>Make sure your photos, description, and rates are up to date to convert that extra traffic</li>
            </ul>

            <!-- View Profile Button -->
            <table cellpadding="0" cellspacing="0" style="display: inline-block; margin: 20px 0;">
                <tbody>
                    <tr>
                        <td style="border-radius: 21.5px; background-color: #C1F11D; text-align: center;">
                            <a href="{{ $profileUrl }}" style="outline: 0; color: #000000; text-decoration: none; font-size: 14px; font-family: Arial, Helvetica, sans-serif; font-weight: 600; line-height: 20px; padding: 8px 22px; display: block;">
                                View Your Featured Profile
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-top: 25px;">
                If you're having trouble clicking the "View Your Featured Profile" button, copy and paste the URL below into your web browser:
            </p>
            <p style="font-size: 13px; color: #C1F11D; background: #131616; border: 1px solid #2a2a2a; padding: 10px 12px; border-radius: 5px; word-break: break-all; margin: 10px 0;">
                {{ $profileUrl }}
            </p>

            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-top: 25px;">Thank you for choosing evoory — enjoy the spot!</p>

            <!-- Footer -->
            <p style="font-size: 10pt; line-height: 1.5; color: #bebebe; border-top: 2px solid #2a2a2a; margin-top: 20px; padding-top: 15px; text-align: justify;">
                You're receiving this email because you won an auction spot on evoory.
                If you have any questions about your featured spot, please contact our support team at
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
