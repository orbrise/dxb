<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contact Form Submission</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0D1011; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
    <div style="background-color: #0D1011; padding: 20px;">
        <div style="padding: 20px; max-width: 600px; margin: 0 auto; background-color: #0D1011; color: #ffffff;">

            <!-- Header -->
            <div style="border-bottom: 2px solid #2a2a2a; padding-bottom: 5px; margin-bottom: 20px;">
                <a href="{{ url('/') }}" title="evoory" style="color: #C1F11D; text-decoration: none; outline: 0;">
                    <img alt="evoory" src="https://assets.evoory.com/uploads/1776856883_Logo.png" width="180">
                </a>
            </div>

            <h1 style="font-size: 1.5em; margin-bottom: 1em; font-weight: 700; color: #C1F11D;">New Contact Form Submission</h1>

            <div style="background-color: #131616; border: 1px solid #2a2a2a; border-radius: 5px; padding: 20px; margin-bottom: 15px;">
                <p style="font-size: 13px; color: #bebebe; margin: 0 0 4px 0; font-weight: bold;">From:</p>
                <p style="font-size: 14px; color: #ffffff; margin: 0;">{{ $name }} &lt;{{ $email }}&gt;</p>
            </div>

            <div style="background-color: #131616; border: 1px solid #2a2a2a; border-radius: 5px; padding: 20px; margin-bottom: 15px;">
                <p style="font-size: 13px; color: #bebebe; margin: 0 0 4px 0; font-weight: bold;">Subject:</p>
                <p style="font-size: 14px; color: #ffffff; margin: 0;">{{ $subject }}</p>
            </div>

            <div style="background-color: #131616; border: 1px solid #2a2a2a; border-radius: 5px; padding: 20px; margin-bottom: 15px;">
                <p style="font-size: 13px; color: #bebebe; margin: 0 0 4px 0; font-weight: bold;">Message:</p>
                <p style="font-size: 14px; color: #ffffff; margin: 0; white-space: pre-wrap; word-wrap: break-word;">{{ $messageContent }}</p>
            </div>

            <div style="border-top: 2px solid #2a2a2a; margin-top: 30px; padding-top: 15px; font-size: 9pt; color: #bebebe;">
                <p style="margin: 0 0 4px 0;">This message was sent via the contact form on {{ config('app.name') }}.</p>
                <p style="margin: 0;">Submitted at: {{ now()->format('F j, Y \a\t g:i A') }}</p>
            </div>

        </div>
    </div>
</body>
</html>
