<?php

namespace App\Mail;

use App\Models\Resource;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User     $user,
        public readonly Resource $document,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your document has been published');
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.document-approved');
    }
}
