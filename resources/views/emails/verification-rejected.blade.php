<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Listing Not Approved - Massage Republic</title>
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
            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-bottom: 15px;">
                Hi {{ $userName }},
            </p>
            
            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-bottom: 15px;">
                Thank you for listing on Massage Republic. Unfortunately, we are not able to approve your listing.
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
                <a href="{{ $actionLink }}" style="color: #F4B827; text-decoration: none; outline: 0;">Click here</a> to fix your listing and reapply for approval. You can read our <a href="{{ url('/help-for-advertisers') }}" style="color: #F4B827; text-decoration: none; outline: 0;">advice here</a> on creating a good listing.
                @else
                Please fix the issues mentioned above and reapply for approval. You can read our <a href="{{ url('/help-for-advertisers') }}" style="color: #F4B827; text-decoration: none; outline: 0;">advice here</a> on creating a good listing.
                @endif
            </p>
            
            <p style="font-size: 14px; line-height: 1.5; color: #ffffff; margin-top: 25px;">
                Thanks, Claire
            </p>
            
            <!-- Footer -->
            <div style="border-top: 2px solid #333333; margin-top: 30px; padding-top: 15px; text-align: center;">
                <a href="{{ url('/') }}" style="color: #F4B827; outline: 0; text-decoration: underline; font-size: 12px;">Go to MassageRepublic.com</a>
                <span style="color: #bebebe; font-size: 12px;"> - Site blocked? Try: </span>
                <a href="https://escorts.ninja" style="color: #F4B827; outline: 0; text-decoration: underline; font-size: 12px;">Escorts.ninja</a>
                <span style="color: #bebebe; font-size: 12px;"> or </span>
                <a href="https://massagerepublic.tk" style="color: #F4B827; outline: 0; text-decoration: underline; font-size: 12px;">MassageRepublic.tk</a>
            </div>
            
        </div>
    </div>
</body>
</html>
