<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $profileName;
    public $userName;
    public $profileUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->profileName = $data['profileName'];
        $this->userName = $data['userName'] ?? $data['profileName'];
        $this->profileUrl = $data['profileUrl'] ?? null;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your listing "' . $this->profileName . '" has been verified - evoory')
                    ->view('emails.verification-approved');
    }
}
