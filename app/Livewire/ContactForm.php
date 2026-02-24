<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactForm extends Component
{
    public $name = '';
    public $email = '';
    public $subject = '';
    public $message = '';
    public $successMessage = '';
    public $errorMessage = '';

    protected $rules = [
        'name' => 'required|min:2|max:100',
        'email' => 'required|email|max:255',
        'subject' => 'required|min:3|max:200',
        'message' => 'required|min:10|max:5000',
    ];

    protected $messages = [
        'name.required' => 'Please enter your name.',
        'name.min' => 'Name must be at least 2 characters.',
        'email.required' => 'Please enter your email address.',
        'email.email' => 'Please enter a valid email address.',
        'subject.required' => 'Please enter a subject.',
        'subject.min' => 'Subject must be at least 3 characters.',
        'message.required' => 'Please enter your message.',
        'message.min' => 'Message must be at least 10 characters.',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function submit()
    {
        $this->validate();

        try {
            // Get admin email from settings or use default
            $adminEmail = config('mail.admin_email', config('mail.from.address'));
            
            // Send email
            Mail::send('emails.contact-form', [
                'name' => $this->name,
                'email' => $this->email,
                'subject' => $this->subject,
                'messageContent' => $this->message,
            ], function ($mail) use ($adminEmail) {
                $mail->to($adminEmail)
                    ->replyTo($this->email, $this->name)
                    ->subject('Contact Form: ' . $this->subject);
            });

            // Log the contact submission
            Log::info('Contact form submitted', [
                'name' => $this->name,
                'email' => $this->email,
                'subject' => $this->subject,
            ]);

            // Clear form and show success
            $this->reset(['name', 'email', 'subject', 'message']);
            $this->successMessage = 'Thank you for your message! We will get back to you soon.';
            $this->errorMessage = '';

        } catch (\Exception $e) {
            Log::error('Contact form error: ' . $e->getMessage());
            $this->errorMessage = 'Sorry, there was an error sending your message. Please try again later.';
            $this->successMessage = '';
        }
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
