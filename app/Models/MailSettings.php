<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'mail_driver',
        'sendmail_path',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'send_on_account_create',
        'send_on_forget_password',
        'send_on_profile_upgrade',
        'send_on_account_upgrade',
        'send_on_profile_archived',
        'send_on_verification',
        'send_on_package_purchase',
        'send_on_wallet_transaction',
    ];

    protected $casts = [
        'mail_port' => 'integer',
        'send_on_account_create' => 'boolean',
        'send_on_forget_password' => 'boolean',
        'send_on_profile_upgrade' => 'boolean',
        'send_on_account_upgrade' => 'boolean',
        'send_on_profile_archived' => 'boolean',
        'send_on_verification' => 'boolean',
        'send_on_package_purchase' => 'boolean',
        'send_on_wallet_transaction' => 'boolean',
    ];

    // Accessor to get decrypted password
    public function getMailPasswordAttribute($value)
    {
        return decrypt($value);
    }

    // Mutator to encrypt password before saving
    public function setMailPasswordAttribute($value)
    {
        $this->attributes['mail_password'] = encrypt($value);
    }

    /**
     * Check if email should be sent for a specific event
     */
    public static function shouldSendEmail($event)
    {
        $settings = static::first();
        if (!$settings) {
            return true; // Default to true if no settings exist
        }
        
        $eventMap = [
            'account_create' => 'send_on_account_create',
            'forget_password' => 'send_on_forget_password',
            'profile_upgrade' => 'send_on_profile_upgrade',
            'account_upgrade' => 'send_on_account_upgrade',
            'profile_archived' => 'send_on_profile_archived',
            'verification' => 'send_on_verification',
            'package_purchase' => 'send_on_package_purchase',
            'wallet_transaction' => 'send_on_wallet_transaction',
            'google_signup' => 'send_on_account_create', // Use same setting as account create
        ];
        
        $field = $eventMap[$event] ?? null;
        
        return $field ? $settings->$field : true;
    }

    /**
     * Get all notification settings as array
     */
    public function getNotificationSettings()
    {
        return [
            'account_create' => $this->send_on_account_create,
            'forget_password' => $this->send_on_forget_password,
            'profile_upgrade' => $this->send_on_profile_upgrade,
            'account_upgrade' => $this->send_on_account_upgrade,
            'profile_archived' => $this->send_on_profile_archived,
            'verification' => $this->send_on_verification,
            'package_purchase' => $this->send_on_package_purchase,
            'wallet_transaction' => $this->send_on_wallet_transaction,
        ];
    }
}