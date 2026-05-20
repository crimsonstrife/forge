<?php

namespace App\Mail;

use App\Models\FeedbackBoard;
use App\Models\FeedbackMagicLink;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeedbackMagicLinkMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public FeedbackBoard $board,
        public FeedbackMagicLink $magicLink,
        public string $token,
    ) {}

    public function envelope(): Envelope
    {
        $fromAddress = $this->board->mail_from_address ?: config('mail.from.address');
        $fromName = $this->board->mail_from_name ?: config('mail.from.name');

        return new Envelope(
            from: new Address((string) $fromAddress, (string) $fromName),
            subject: 'Sign in to '.$this->board->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.feedback.magic-link',
            with: [
                'board' => $this->board,
                'magicLink' => $this->magicLink,
                'url' => rtrim($this->board->public_url, '/').'/auth/callback?token='.$this->token.'&board='.$this->board->slug,
            ],
        );
    }
}
