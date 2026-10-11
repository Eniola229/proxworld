<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Heads-up for the support team when a customer opens a ticket. */
class NewTicketAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Ticket $ticket, public TicketMessage $ticketMessage)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'New support ticket: '.$this->ticket->subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.tickets.admin-new', with: [
            'ticket' => $this->ticket,
            'ticketMessage' => $this->ticketMessage,
        ]);
    }
}