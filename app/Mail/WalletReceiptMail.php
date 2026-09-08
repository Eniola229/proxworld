<?php

namespace App\Mail;

use App\Models\WalletTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WalletReceiptMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public WalletTransaction $transaction)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Wallet Funded Successfully');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.wallet.receipt', with: ['transaction' => $this->transaction]);
    }
}
