@extends('emails.layout')

@section('title', 'Ticket received')
@section('preheader', 'We have received your support ticket and will reply shortly.')

@section('content')
    <h1 style="margin:0 0 8px; font-size:20px; font-weight:700; color:#111827;">Ticket received</h1>
    <p style="margin:0 0 20px; font-size:15px; color:#4b5563;">
        Hi {{ $ticket->user->name }}, thanks for contacting us. Our team will reply as soon as possible.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin:0 0 16px;">
        <tr>
            <td style="padding:10px 0; border-bottom:1px solid #eef1f6; font-size:13px; color:#6b7280;">Ticket ID</td>
            <td style="padding:10px 0; border-bottom:1px solid #eef1f6; font-size:14px; color:#111827; font-weight:600; text-align:right;">#{{ substr($ticket->id, 0, 8) }}</td>
        </tr>
        <tr>
            <td style="padding:10px 0; border-bottom:1px solid #eef1f6; font-size:13px; color:#6b7280;">Subject</td>
            <td style="padding:10px 0; border-bottom:1px solid #eef1f6; font-size:14px; color:#111827; font-weight:600; text-align:right;">{{ $ticket->subject }}</td>
        </tr>
        <tr>
            <td style="padding:10px 0; font-size:13px; color:#6b7280;">Status</td>
            <td style="padding:10px 0; text-align:right;">
                <span style="display:inline-block; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:700; color:#ffffff; background-color:#f59e0b;">Open</span>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 6px; font-size:13px; color:#6b7280;">Your message</p>
    <div style="margin:0 0 8px; font-size:14px; color:#111827; background-color:#f9fafb; padding:12px 14px; border-radius:8px; line-height:1.6;">
        {!! nl2br(e($ticketMessage->message)) !!}
    </div>

    @include('emails.components.button', ['url' => route('support.index', ['ticket' => $ticket->id]), 'label' => 'View Ticket'])
@endsection