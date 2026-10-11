@extends('emails.layout')

@section('title', 'New support ticket')
@section('preheader', 'A customer opened a new support ticket.')

@section('content')
    <h1 style="margin:0 0 8px; font-size:20px; font-weight:700; color:#111827;">New support ticket</h1>
    <p style="margin:0 0 20px; font-size:15px; color:#4b5563;">
        {{ $ticket->user->name }} ({{ $ticket->user->email }}) opened a ticket.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin:0 0 16px;">
        <tr>
            <td style="padding:10px 0; border-bottom:1px solid #eef1f6; font-size:13px; color:#6b7280;">Ticket ID</td>
            <td style="padding:10px 0; border-bottom:1px solid #eef1f6; font-size:14px; color:#111827; font-weight:600; text-align:right;">#{{ substr($ticket->id, 0, 8) }}</td>
        </tr>
        <tr>
            <td style="padding:10px 0; font-size:13px; color:#6b7280;">Subject</td>
            <td style="padding:10px 0; font-size:14px; color:#111827; font-weight:600; text-align:right;">{{ $ticket->subject }}</td>
        </tr>
    </table>

    <div style="margin:0 0 8px; font-size:14px; color:#111827; background-color:#f9fafb; padding:12px 14px; border-radius:8px; line-height:1.6;">
        {!! nl2br(e($ticketMessage->message)) !!}
    </div>

    @include('emails.components.button', ['url' => route('admin.support.show', $ticket->id), 'label' => 'Open in Admin'])
@endsection