<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MailSettingsController extends Controller
{
    public function index()
    {
        $mailSettings = MailSettings::first();
        return view('admin.mail-settings.index', compact('mailSettings'));
    }

    public function store(Request $request)
    {
        // Convert checkbox values to boolean BEFORE validation
        $notificationSettings = [
            'send_on_account_create',
            'send_on_forget_password',
            'send_on_profile_upgrade',
            'send_on_account_upgrade',
            'send_on_profile_archived',
            'send_on_verification',
            'send_on_package_purchase',
            'send_on_wallet_transaction'
        ];
        
        foreach ($notificationSettings as $setting) {
            $request->merge([$setting => $request->has($setting) ? 1 : 0]);
        }

        $validationRules = [
            'mail_driver' => 'required|in:smtp,sendmail',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
            'send_on_account_create' => 'boolean',
            'send_on_forget_password' => 'boolean',
            'send_on_profile_upgrade' => 'boolean',
            'send_on_account_upgrade' => 'boolean',
            'send_on_profile_archived' => 'boolean',
            'send_on_verification' => 'boolean',
            'send_on_package_purchase' => 'boolean',
            'send_on_wallet_transaction' => 'boolean',
        ];

        // Add SMTP-specific validation if SMTP is selected
        if ($request->mail_driver === 'smtp') {
            $validationRules = array_merge($validationRules, [
                'mail_host' => 'required|string|max:255',
                'mail_port' => 'required|integer|min:1|max:65535',
                'mail_username' => 'required|string|max:255',
                'mail_password' => 'required|string|max:255',
                'mail_encryption' => 'required|in:tls,ssl,none',
            ]);
        } else {
            // Sendmail specific validation
            $validationRules['sendmail_path'] = 'nullable|string|max:255';
        }

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $mailSettings = MailSettings::first();
        
        // Get base data
        $data = $request->only([
            'mail_driver',
            'mail_from_address',
            'mail_from_name',
            'send_on_account_create',
            'send_on_forget_password',
            'send_on_profile_upgrade',
            'send_on_account_upgrade',
            'send_on_profile_archived',
            'send_on_verification',
            'send_on_package_purchase',
            'send_on_wallet_transaction'
        ]);

        // Add driver-specific data
        if ($request->mail_driver === 'smtp') {
            $data = array_merge($data, $request->only([
                'mail_host',
                'mail_port',
                'mail_username',
                'mail_password',
                'mail_encryption',
            ]));
            // Keep sendmail_path if it exists, otherwise set default
            if (!isset($data['sendmail_path']) && $mailSettings) {
                $data['sendmail_path'] = $mailSettings->sendmail_path;
            } elseif (!isset($data['sendmail_path'])) {
                $data['sendmail_path'] = '/usr/sbin/sendmail -bs';
            }
        } else {
            // Sendmail is selected
            $data['sendmail_path'] = $request->sendmail_path ?? '/usr/sbin/sendmail -bs';
            
            // Keep existing SMTP settings if they exist, don't clear them
            if ($mailSettings) {
                $data['mail_host'] = $mailSettings->mail_host;
                $data['mail_port'] = $mailSettings->mail_port;
                $data['mail_username'] = $mailSettings->mail_username;
                $data['mail_password'] = $mailSettings->mail_password;
                $data['mail_encryption'] = $mailSettings->mail_encryption;
            }
        }
        
        if ($mailSettings) {
            $mailSettings->update($data);
        } else {
            MailSettings::create($data);
        }

        // Clear configuration cache to ensure new settings are used
        \Artisan::call('config:clear');
        
        return redirect()->back()->with('success', 'Mail settings updated successfully!');
    }

    public function testConnection(Request $request)
    {
        $diagnostics = [];
        
        try {
            $mailDriver = $request->mail_driver ?? 'smtp';

            if ($mailDriver === 'sendmail') {
                // Test Sendmail
                $diagnostics[] = "Testing Sendmail configuration...";
                
                $sendmailPath = $request->sendmail_path ?? '/usr/sbin/sendmail -bs';
                $diagnostics[] = "Sendmail path: {$sendmailPath}";

                // Check if sendmail binary exists
                $sendmailBinary = explode(' ', $sendmailPath)[0];
                if (!file_exists($sendmailBinary)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Sendmail binary not found at: {$sendmailBinary}",
                        'diagnostics' => $diagnostics,
                        'suggestions' => [
                            "Common sendmail paths: /usr/sbin/sendmail, /usr/bin/sendmail",
                            "Check if sendmail is installed: which sendmail",
                            "You may need to install sendmail or use SMTP instead"
                        ]
                    ]);
                }
                $diagnostics[] = "✓ Sendmail binary found";

                // Configure test mail settings for sendmail
                $originalMailer = config('mail.default');
                $originalConfig = config('mail.mailers.sendmail');
                $originalFrom = config('mail.from');

                config([
                    'mail.default' => 'sendmail',
                    'mail.mailers.sendmail.path' => $sendmailPath,
                    'mail.from.address' => $request->mail_from_address,
                    'mail.from.name' => $request->mail_from_name,
                ]);

                // Purge the mail manager to use new config
                app('mail.manager')->purge();

                // Send a test email
                \Mail::raw('This is a test email using Sendmail from your DXB application. If you received this email, your Sendmail configuration is working correctly!', function ($message) use ($request) {
                    $message->to($request->mail_from_address)
                        ->subject('Test Email - DXB Sendmail Configuration Test');
                });
                
                $diagnostics[] = "✓ Email sent successfully using Sendmail";

                // Restore original configuration
                config([
                    'mail.default' => $originalMailer,
                    'mail.mailers.sendmail' => $originalConfig,
                    'mail.from' => $originalFrom,
                ]);
                
                // Purge mail manager again to restore original config
                app('mail.manager')->purge();

                return response()->json([
                    'success' => true, 
                    'message' => 'Test email sent successfully using Sendmail! Check your inbox at ' . $request->mail_from_address,
                    'diagnostics' => $diagnostics
                ]);
            }

            // SMTP Testing (existing code)
            // Step 1: DNS Resolution Test
            $host = $request->mail_host;
            $port = $request->mail_port;
            
            $diagnostics[] = "Testing DNS resolution for: {$host}";
            $ip = gethostbyname($host);
            if ($ip === $host) {
                return response()->json([
                    'success' => false, 
                    'message' => "DNS Resolution Failed: Cannot resolve hostname '{$host}'. Please check if the hostname is correct.",
                    'diagnostics' => $diagnostics,
                    'suggestions' => [
                        "Try using 'smtp.{$host}' instead of 'mail.{$host}'",
                        "Check with your hosting provider for the correct SMTP hostname",
                        "Common alternatives: smtp.gmail.com, smtp-mail.outlook.com, etc.",
                        "If using cPanel, try: mail.yourdomain.com or yourdomain.com"
                    ]
                ]);
            }
            $diagnostics[] = "✓ DNS resolved to: {$ip}";
            
            // Step 2: Port Connection Test
            $diagnostics[] = "Testing connection to {$host}:{$port}";
            $connection = @fsockopen($host, $port, $errno, $errstr, 10);
            if (!$connection) {
                return response()->json([
                    'success' => false,
                    'message' => "Port Connection Failed: Cannot connect to {$host}:{$port}. Error: {$errstr} ({$errno})",
                    'diagnostics' => $diagnostics,
                    'suggestions' => [
                        "Common SMTP ports: 587 (TLS), 465 (SSL), 25 (usually blocked)",
                        "Try port 465 with SSL encryption if 587 fails",
                        "Check if your server/firewall allows outgoing connections on port {$port}",
                        "Contact your hosting provider about SMTP restrictions"
                    ]
                ]);
            }
            fclose($connection);
            $diagnostics[] = "✓ Port connection successful";
            
            // Step 3: SMTP Authentication Test
            $diagnostics[] = "Testing SMTP configuration...";
            
            // Temporarily store current mail settings
            $originalMailer = config('mail.default');
            $originalConfig = config('mail.mailers.smtp');
            $originalFrom = config('mail.from');

            // Configure test mail settings
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.host' => $request->mail_host,
                'mail.mailers.smtp.port' => $request->mail_port,
                'mail.mailers.smtp.username' => $request->mail_username,
                'mail.mailers.smtp.password' => $request->mail_password,
                'mail.mailers.smtp.encryption' => $request->mail_encryption === 'none' ? null : $request->mail_encryption,
                'mail.from.address' => $request->mail_from_address,
                'mail.from.name' => $request->mail_from_name,
            ]);

            // Purge the mail manager to use new config
            app('mail.manager')->purge();

            // Send a test email
            \Mail::raw('This is a test email from your DXB application. If you received this email, your SMTP configuration is working correctly!', function ($message) use ($request) {
                $message->to($request->mail_from_address)
                    ->subject('Test Email - DXB Mail Configuration Test');
            });
            
            $diagnostics[] = "✓ Email sent successfully";

            // Restore original configuration
            config([
                'mail.default' => $originalMailer,
                'mail.mailers.smtp' => $originalConfig,
                'mail.from' => $originalFrom,
            ]);
            
            // Purge mail manager again to restore original config
            app('mail.manager')->purge();

            return response()->json([
                'success' => true, 
                'message' => 'Test email sent successfully! Check your inbox at ' . $request->mail_from_address,
                'diagnostics' => $diagnostics
            ]);
        } catch (\Exception $e) {
            // Restore original configuration on error
            if (isset($originalMailer)) {
                config([
                    'mail.default' => $originalMailer,
                    'mail.mailers.smtp' => $originalConfig ?? [],
                    'mail.from' => $originalFrom ?? [],
                ]);
                app('mail.manager')->purge();
            }
            
            $diagnostics[] = "✗ SMTP Error: " . $e->getMessage();
            
            return response()->json([
                'success' => false, 
                'message' => 'SMTP Connection/Authentication Failed: ' . $e->getMessage(),
                'diagnostics' => $diagnostics,
                'suggestions' => [
                    "Check username and password are correct",
                    "Try different encryption: TLS (port 587) or SSL (port 465)",
                    "Some servers require 'no encryption' on port 25 or 587",
                    "Verify that SMTP authentication is enabled for this account",
                    "Check if the email account exists and is not suspended"
                ]
            ]);
        }
    }
}