<?php

namespace App\Services;

use App\Models\MailSettings;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Send email notification if enabled for the event
     */
    public static function sendIfEnabled($event, $mailable, $recipient = null)
    {
        if (!MailSettings::shouldSendEmail($event)) {
            return false; // Email sending disabled for this event
        }

        try {
            if ($recipient) {
                Mail::to($recipient)->send($mailable);
            } else {
                Mail::send($mailable);
            }
            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to send {$event} email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send welcome email on account creation
     */
    public static function sendWelcomeEmail($user)
    {
        if (!MailSettings::shouldSendEmail('account_create')) {
            return false;
        }

        return self::sendIfEnabled('account_create', new \App\Mail\WelcomeEmail($user), $user->email);
    }

    /**
     * Send password reset email
     */
    public static function sendPasswordResetEmail($user, $token)
    {
        if (!MailSettings::shouldSendEmail('forget_password')) {
            return false;
        }

        // You can create a PasswordResetEmail mailable
        // return self::sendIfEnabled('forget_password', new \App\Mail\PasswordResetEmail($user, $token), $user->email);
        return true; // Placeholder for now
    }

    /**
     * Send profile upgrade notification
     */
    public static function sendProfileUpgradeEmail($user, $package)
    {
        if (!MailSettings::shouldSendEmail('profile_upgrade')) {
            return false;
        }

        // You can create a ProfileUpgradeEmail mailable
        // return self::sendIfEnabled('profile_upgrade', new \App\Mail\ProfileUpgradeEmail($user, $package), $user->email);
        return true; // Placeholder for now
    }

    /**
     * Send package purchase confirmation
     */
    public static function sendPackagePurchaseEmail($user, $package, $transaction)
    {
        if (!MailSettings::shouldSendEmail('package_purchase')) {
            return false;
        }

        // You can create a PackagePurchaseEmail mailable
        // return self::sendIfEnabled('package_purchase', new \App\Mail\PackagePurchaseEmail($user, $package, $transaction), $user->email);
        return true; // Placeholder for now
    }

    /**
     * Send wallet transaction notification
     */
    public static function sendWalletTransactionEmail($user, $transaction)
    {
        if (!MailSettings::shouldSendEmail('wallet_transaction')) {
            return false;
        }

        // You can create a WalletTransactionEmail mailable
        // return self::sendIfEnabled('wallet_transaction', new \App\Mail\WalletTransactionEmail($user, $transaction), $user->email);
        return true; // Placeholder for now
    }

    /**
     * Send verification email
     */
    public static function sendVerificationEmail($user, $verificationUrl)
    {
        if (!MailSettings::shouldSendEmail('verification')) {
            return false;
        }

        // You can create a VerificationEmail mailable
        // return self::sendIfEnabled('verification', new \App\Mail\VerificationEmail($user, $verificationUrl), $user->email);
        return true; // Placeholder for now
    }

    /**
     * Send profile archived notification
     */
    public static function sendProfileArchivedEmail($user, $reason = null)
    {
        if (!MailSettings::shouldSendEmail('profile_archived')) {
            return false;
        }

        // You can create a ProfileArchivedEmail mailable
        // return self::sendIfEnabled('profile_archived', new \App\Mail\ProfileArchivedEmail($user, $reason), $user->email);
        return true; // Placeholder for now
    }

    /**
     * Get current notification settings
     */
    public static function getSettings()
    {
        $mailSettings = MailSettings::first();
        return $mailSettings ? $mailSettings->getNotificationSettings() : [];
    }
}