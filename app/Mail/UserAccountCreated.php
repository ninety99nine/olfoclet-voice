<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class UserAccountCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $setupUrl;
    public $organizationName;

    /**
     * Create a new message instance.
     *
     * @param string $email
     * @param string $setupUrl
     * @param string|null $organizationName
     */
    public function __construct(string $email, string $setupUrl, ?string $organizationName = null)
    {
        $this->email = $email;
        $this->setupUrl = $setupUrl;
        $this->organizationName = $organizationName;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Set Up Your Account',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.user-account-created',
            with: [
                'email' => $this->email,
                'setupUrl' => $this->setupUrl,
                'organizationName' => $this->organizationName,
            ],
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
