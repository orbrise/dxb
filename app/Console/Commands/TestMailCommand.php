<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\MailSettings;

class TestMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email? : Email address to send test to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email using the configured mail settings from database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mailSettings = MailSettings::first();
        
        if (!$mailSettings) {
            $this->error('No mail settings found in database. Please configure mail settings in admin panel first.');
            return 1;
        }

        $email = $this->argument('email') ?: $mailSettings->mail_from_address;
        
        $this->info('Testing mail configuration...');
        $this->info('Host: ' . $mailSettings->mail_host);
        $this->info('Port: ' . $mailSettings->mail_port);
        $this->info('Username: ' . $mailSettings->mail_username);
        $this->info('Encryption: ' . $mailSettings->mail_encryption);
        $this->info('From: ' . $mailSettings->mail_from_address . ' (' . $mailSettings->mail_from_name . ')');
        $this->info('Sending test email to: ' . $email);
        
        try {
            // Create a sample user object for the email
            $sampleUser = (object) [
                'name' => 'Test User',
                'email' => $email,
                'created_at' => now(),
            ];
            
            Mail::to($email)->send(new \App\Mail\WelcomeEmail($sampleUser));
            
            $this->info('✅ Test email sent successfully!');
            $this->info('📧 Check your inbox at: ' . $email);
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed to send test email: ' . $e->getMessage());
            return 1;
        }
    }
}
