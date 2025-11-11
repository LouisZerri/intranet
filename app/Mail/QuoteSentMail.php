<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class QuoteSentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $quote;
    private $pdfContent;

    /**
     * Create a new message instance.
     */
    public function __construct(Quote $quote, $pdfContent)
    {
        $this->quote = $quote;
        $this->pdfContent = $pdfContent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Devis ' . $this->quote->quote_number . ' - ' . config('app.name', 'GEST\'IMMO'),
            replyTo: [$this->quote->user->email],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.quotes.sent',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfContent, 'devis-' . $this->quote->quote_number . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}