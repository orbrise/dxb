<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use App\Models\MailSettings;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Configure mail settings from database
        $this->configureMailFromDatabase();
    }

    /**
     * Configure mail settings from database
     */
    private function configureMailFromDatabase(): void
    {
        try {
            // Check if mail_settings table exists and get settings
            $mailSettings = MailSettings::first();
            
            if ($mailSettings) {
                $mailDriver = $mailSettings->mail_driver ?? 'smtp';

                if ($mailDriver === 'smtp') {
                    // Configure SMTP settings with full configuration
                    Config::set('mail.mailers.smtp', [
                        'transport' => 'smtp',
                        'host' => $mailSettings->mail_host,
                        'port' => $mailSettings->mail_port,
                        'encryption' => $mailSettings->mail_encryption === 'none' ? null : $mailSettings->mail_encryption,
                        'username' => $mailSettings->mail_username,
                        'password' => $mailSettings->mail_password,
                        'timeout' => null,
                        'local_domain' => null,
                    ]);
                    
                    // Set SMTP as the default mailer
                    Config::set('mail.default', 'smtp');
                    
                    // Also set environment variables for compatibility
                    $_ENV['MAIL_MAILER'] = 'smtp';
                    $_ENV['MAIL_HOST'] = $mailSettings->mail_host;
                    $_ENV['MAIL_PORT'] = $mailSettings->mail_port;
                    $_ENV['MAIL_USERNAME'] = $mailSettings->mail_username;
                    $_ENV['MAIL_PASSWORD'] = $mailSettings->mail_password;
                    $_ENV['MAIL_ENCRYPTION'] = $mailSettings->mail_encryption === 'none' ? 'null' : $mailSettings->mail_encryption;
                } else {
                    // Configure Sendmail settings
                    $sendmailPath = $mailSettings->sendmail_path ?? '/usr/sbin/sendmail -bs';
                    
                    Config::set('mail.mailers.sendmail', [
                        'transport' => 'sendmail',
                        'path' => $sendmailPath,
                    ]);
                    
                    // Set sendmail as the default mailer
                    Config::set('mail.default', 'sendmail');
                    
                    // Set environment variables for compatibility
                    $_ENV['MAIL_MAILER'] = 'sendmail';
                    $_ENV['SENDMAIL_PATH'] = $sendmailPath;
                }

                // Configure global from address (common for both drivers)
                Config::set('mail.from.address', $mailSettings->mail_from_address);
                Config::set('mail.from.name', $mailSettings->mail_from_name);
                
                $_ENV['MAIL_FROM_ADDRESS'] = $mailSettings->mail_from_address;
                $_ENV['MAIL_FROM_NAME'] = $mailSettings->mail_from_name;
            }
        } catch (\Exception $e) {
            // If there's any error (like table doesn't exist), fall back to .env settings
            // This prevents errors during migrations or when the table doesn't exist yet
            \Log::info('Mail settings not loaded from database: ' . $e->getMessage());
        }
    }
}
