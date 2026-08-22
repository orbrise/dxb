<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProfileUpgraded extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $profileName;
    public $packageName;
    public $duration;
    public $profileUrl;
    public $expiryDate;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->userName = $data['userName'];
        $this->profileName = $data['profileName'];
        $this->packageName = $data['packageName'];
        $this->duration = $data['duration'];
        $this->profileUrl = $data['profileUrl'];
        // Derived from $duration so callers don't need to pass expiry separately.
        // If a caller ever supplies an explicit expiryDate, honour it.
        $this->expiryDate = $data['expiryDate']
            ?? \Carbon\Carbon::now()->addDays((int) $this->duration)->format('M d, Y');
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Profile Has Been Upgraded - Evoory')
                    ->view('emails.profile-upgraded');
    }
}
