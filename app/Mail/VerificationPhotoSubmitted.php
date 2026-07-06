<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationPhotoSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $profileName;
    public $profileUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->userName = $data['userName'] ?? '';
        $this->profileName = $data['profileName'];
        $this->profileUrl = $data['profileUrl'];
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Verification Photo Received - Pending Review - evoory')
                    ->view('emails.verification-photo-submitted');
    }
}
