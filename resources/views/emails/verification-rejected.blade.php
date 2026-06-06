<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Listing Not Approved - evoory</title>
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
                Thank you for listing on evoory. Unfortunately, we are not able to approve your listing.
            </p>
            
            <!-- Rejection Reason Box -->
            <div style="margin: 25px 0;">
                <p style="font-size: 14px; color: #ffffff; margin: 0 0 10px 0; font-weight: bold;">
                    Reason of rejection:
                </p>
                <p style="font-size: 14px; color: #ffffff; margin: 0;">
                    {{ $reason }}
                </p>
            </div>
            
            <!-- Action Link -->
            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-bottom: 15px;">
                @if($actionLink)
                <a href="{{ $actionLink }}" style="color: #C1F11D; text-decoration: none; outline: 0;">Click here</a> to fix your listing and reapply for approval. You can read our <a href="{{ url('/help-for-advertisers') }}" style="color: #C1F11D; text-decoration: none; outline: 0;">advice here</a> on creating a good listing.
                @else
                Please fix the issues mentioned above and reapply for approval. You can read our <a href="{{ url('/help-for-advertisers') }}" style="color: #C1F11D; text-decoration: none; outline: 0;">advice here</a> on creating a good listing.
                @endif
            </p>
            
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
