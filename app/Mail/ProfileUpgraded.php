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
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Profile Has Been Upgraded - Massage Republic')
                    ->view('emails.profile-upgraded');
    }
}
