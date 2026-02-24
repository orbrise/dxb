<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProfileCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $profileName;
    public $packageName;
    public $profileUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->profileName = $data['profileName'];
        $this->packageName = $data['packageName'] ?? null;
        $this->profileUrl = $data['profileUrl'];
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Profile Has Been Created - Massage Republic')
                    ->view('emails.profile-created');
    }
}
