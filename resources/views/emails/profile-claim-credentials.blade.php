<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your profile is now under your account</title>
</head>
<body style="margin:0;padding:0;background:#0D1011;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#fff;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#0D1011;">
        <tr>
            <td align="center" style="padding:40px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:560px;background:#15191B;border:1px solid #23292B;border-radius:12px;">
                    <tr>
                        <td style="padding:32px 32px 16px;">
                            <h1 style="margin:0 0 8px;font-size:22px;font-weight:600;color:#C1F11D;">Profile claim verified</h1>
                            <p style="margin:0;font-size:15px;line-height:1.6;color:#cfd3d6;">
                                Hi {{ $mailData['name'] ?? 'there' }}, your ownership of {{ $mailData['profile_count'] ?? 1 }} profile{{ ($mailData['profile_count'] ?? 1) > 1 ? 's' : '' }} on evoory has been verified and the profile{{ ($mailData['profile_count'] ?? 1) > 1 ? 's are' : ' is' }} now linked to your account.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 32px 24px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#0D1011;border:1px solid #23292B;border-radius:8px;">
                                <tr>
                                    <td style="padding:18px 20px;">
                                        <p style="margin:0 0 10px;font-size:12px;letter-spacing:0.06em;text-transform:uppercase;color:#8b9298;">Your login credentials</p>
                                        <p style="margin:0 0 6px;font-size:14px;color:#cfd3d6;"><strong style="color:#fff;">Email:</strong> {{ $mailData['email'] }}</p>
                                        <p style="margin:0;font-size:14px;color:#cfd3d6;"><strong style="color:#fff;">Password:</strong> <span style="font-family:Menlo,Consolas,monospace;color:#C1F11D;">{{ $mailData['password'] }}</span></p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 32px 28px;">
                            <p style="margin:0 0 16px;font-size:14px;line-height:1.6;color:#cfd3d6;">
                                For security, please sign in and change this password from your account settings.
                            </p>
                            <a href="{{ url('/sign-in') }}" style="display:inline-block;background:#C1F11D;color:#000;text-decoration:none;padding:10px 22px;border-radius:24px;font-weight:600;font-size:14px;">Sign in</a>
                        </td>
                    </tr>
                </table>
                <p style="margin:16px 0 0;font-size:11px;color:#6b6f74;">
                    If you didn't expect this email, please contact support immediately.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
