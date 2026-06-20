<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent after a profile owner successfully claims an imported profile.
 *
 * The user picked their own email + the system generated a password during
 * the claim flow; this email is the record of those credentials. Structure
 * mirrors GoogleSignup so future maintenance touches both consistently.
 */
class ProfileClaimCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your profile is now under your account',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.profile-claim-credentials',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
