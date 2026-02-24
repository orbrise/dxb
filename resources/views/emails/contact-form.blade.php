<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contact Form Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f4b827;
            color: #000;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .field {
            margin-bottom: 15px;
        }
        .field-label {
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
        }
        .field-value {
            background-color: #fff;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 3px;
        }
        .message-content {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 0.85em;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>New Contact Form Submission</h1>
    </div>
    
    <div class="content">
        <div class="field">
            <div class="field-label">From:</div>
            <div class="field-value">{{ $name }} &lt;{{ $email }}&gt;</div>
        </div>
        
        <div class="field">
            <div class="field-label">Subject:</div>
            <div class="field-value">{{ $subject }}</div>
        </div>
        
        <div class="field">
            <div class="field-label">Message:</div>
            <div class="field-value message-content">{{ $messageContent }}</div>
        </div>
        
        <div class="footer">
            <p>This message was sent via the contact form on {{ config('app.name') }}.</p>
            <p>Submitted at: {{ now()->format('F j, Y \a\t g:i A') }}</p>
        </div>
    </div>
</body>
</html>
