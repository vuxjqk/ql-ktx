<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contactData;

    /**
     * Create a new message instance.
     */
    public function __construct(array $contactData)
    {
        $this->contactData = $contactData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: 'no-reply@ktx.huit.edu.vn',
            subject: '[KTX HUIT] Tin nhắn mới từ sinh viên: ' . $this->contactData['subject'],
            replyTo: $this->contactData['email'],
            tags: ['contact-form', 'student-feedback'],
            metadata: [
                'sender_name' => $this->contactData['name'],
                'sender_email' => $this->contactData['email'],
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'student.emails.contact',
            with: [
                'name'    => $this->contactData['name'],
                'email'   => $this->contactData['email'],
                'subject' => $this->contactData['subject'],
                'messageText' => $this->contactData['message'],
                'sent_at' => now()->format('d/m/Y H:i'),
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
