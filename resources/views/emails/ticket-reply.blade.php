@extends('emails.layout')

@section('title', 'Support ticket reply')
@section('preheader', 'Our support team has responded to your ticket.')

@section('content')
    <h1 style="margin:0 0 8px; font-size:20px; font-weight:700; color:#111827;">Support ticket reply</h1>
    <p style="margin:0 0 20px; font-size:14px; font-weight:600; color:#6b7280;">You have a new reply to your support ticket</p>

    <p style="margin:0 0 8px; font-size:15px; color:#4b5563;">Hello <strong style="color:#111827;">{{ $ticket->user->name }}</strong>,</p>
    <p style="margin:0 0 20px; font-size:15px; color:#4b5563;">Our support team has responded to your ticket.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f9fafb; border:1px solid #eef1f6; border-radius:8px; margin:0 0 20px;">
        <tr>
            <td style="padding:16px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="padding:0 0 8px; font-size:13px; color:#374151;"><strong>Ticket #:</strong> {{ $ticket->id }}</td>
                    </tr>
                    <tr>
                        <td style="padding:0 0 8px; font-size:13px; color:#374151;"><strong>Subject:</strong> {{ $ticket->subject }}</td>
                    </tr>
                    <tr>
                        <td style="font-size:13px; color:#374151;">
                            <strong>Status:</strong>
                            <span style="display:inline-block; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:700; color:#ffffff;
                                background-color:
                                @if($ticket->status === 'open') #f59e0b
                                @elseif($ticket->status === 'in_progress') #2563eb
                                @elseif($ticket->status === 'closed') #16a34a
                                @else #6b7280
                                @endif;">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#eff6ff; border:1px solid #dbeafe; border-radius:8px; margin:0 0 24px;">
        <tr>
            <td style="padding:20px;">
                <p style="margin:0 0 10px; font-size:13px; font-weight:700; color:#1e3a8a;">Support Team Reply</p>
                <p style="margin:0; font-size:14px; color:#1f2937; white-space:pre-wrap;">{{ $ticketMessage->message }}</p>
            </td>
        </tr>
    </table>

    @include('emails.components.button', ['url' => route('support.show', $ticket->id), 'label' => 'View Full Ticket & Reply'])

    <p style="margin:24px 0 0; font-size:13px; color:#9ca3af;">
        If you have any questions, please reply to this ticket or contact our support team.
    </p>
@endsection
