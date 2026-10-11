@extends('emails.layout')

@section('title', 'New reply on your ticket')
@section('preheader', 'Our support team has replied to your ticket.')

@section('content')
    <h1 style="margin:0 0 8px; font-size:20px; font-weight:700; color:#111827;">New reply on your ticket</h1>
    <p style="margin:0 0 20px; font-size:15px; color:#4b5563;">
        Hi {{ $ticket->user->name }}, our support team replied to <strong>{{ $ticket->subject }}</strong> (#{{ substr($ticket->id, 0, 8) }}).
    </p>

    <p style="margin:0 0 6px; font-size:13px; color:#6b7280;">Support team</p>
    <div style="margin:0 0 8px; font-size:14px; color:#111827; background-color:#f9fafb; padding:12px 14px; border-radius:8px; line-height:1.6; border-left:3px solid #0d6efd;">
        {!! nl2br(e($ticketMessage->message)) !!}
    </div>

    @include('emails.components.button', ['url' => route('support.index', ['ticket' => $ticket->id]), 'label' => 'Reply to Ticket'])
@endsection