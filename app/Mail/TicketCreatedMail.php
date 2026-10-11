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

/** Confirmation sent to the customer when they open a ticket. */
class TicketCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Ticket $ticket, public TicketMessage $ticketMessage)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'We received your ticket #'.substr($this->ticket->id, 0, 8));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.tickets.created', with: [
            'ticket' => $this->ticket,
            'ticketMessage' => $this->ticketMessage,
        ]);
    }
}