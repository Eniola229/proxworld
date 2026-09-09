@extends('emails.layout')

@section('title', 'Welcome to the '.config('app.name').' admin team')
@section('preheader', 'Set your password to access the admin panel.')

@section('content')
    <h1 style="margin:0 0 8px; font-size:20px; font-weight:700; color:#111827;">Welcome to the team</h1>
    <p style="margin:0 0 20px; font-size:15px; color:#4b5563;">
        Hi {{ $admin->name }}, an account has been created for you with the role of
        <strong style="color:#111827;">{{ ucfirst(str_replace('_', ' ', $admin->role)) }}</strong>.
    </p>
    <p style="margin:0 0 4px; font-size:15px; color:#4b5563;">
        Click below to set your password and access the admin panel. This link expires soon for security.
    </p>

    @include('emails.components.button', ['url' => $url, 'label' => 'Set Your Password'])

    <p style="margin:24px 0 0; font-size:13px; color:#9ca3af;">
        If the button above doesn't work, copy and paste this link into your browser:<br>
        <a href="{{ $url }}" style="color:#2563eb; word-break:break-all;">{{ $url }}</a>
    </p>
@endsection
