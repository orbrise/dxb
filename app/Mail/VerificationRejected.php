<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $profileName;
    public $userName;
    public $reason;
    public $actionLink;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->profileName = $data['profileName'];
        $this->userName = $data['userName'] ?? $data['profileName'];
        $this->reason = $data['reason'];
        $this->actionLink = $data['actionLink'] ?? null;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Listing "' . $this->profileName . '" not approved - please fix')
                    ->view('emails.verification-rejected');
    } 
}
