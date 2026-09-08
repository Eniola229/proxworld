<?php

namespace App\Mail;

use App\Models\AdminInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminInvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public AdminInvitation $invitation)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'You\'ve been added as a ProxWorld admin — set your password');
    }

    public function content(): Content
    {
        $url = url()->temporarySignedRoute(
            'admin.invitations.accept',
            $this->invitation->expires_at,
            ['invitation' => $this->invitation->id, 'token' => $this->invitation->token]
        );

        return new Content(view: 'emails.admin.invitation', with: [
            'invitation' => $this->invitation,
            'admin' => $this->invitation->admin,
            'url' => $url,
        ]);
    }
}
