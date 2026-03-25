<?php


namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly array $formData) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Contact Form Submission — ' . $this->formData['service'],
            replyTo: [$this->formData['email']],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',   // resources/views/emails/contact.blade.php
        );
    }
}
