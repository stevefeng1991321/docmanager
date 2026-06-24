<?php

namespace App\Mail;

use App\Models\Resource;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User     $user,
        public readonly Resource $document,
        public readonly string   $reason,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your document was not approved');
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.document-rejected');
    }
}
