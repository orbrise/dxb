<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class WeeklyNewsletter extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $reviews;
    public $listings;
    public $questions;
    public $cityNames;
    public $genders;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, array $newsletterData)
    {
        $this->user = $user;
        $this->reviews = $newsletterData['reviews'];
        $this->listings = $newsletterData['listings'];
        $this->questions = $newsletterData['questions'];
        $this->cityNames = $newsletterData['cityNames'];
        $this->genders = $newsletterData['genders'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $cityList = implode(', ', $this->cityNames);
        
        return new Envelope(
            subject: "Weekly Update - {$cityList} - Massage Republic",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly-newsletter',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
