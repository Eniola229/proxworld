@extends('emails.layout')

@section('title', "You're verified")
@section('preheader', 'Your identity verification was approved — you can place orders now.')

@section('content')
    <h1 style="margin:0 0 8px; font-size:20px; font-weight:700; color:#111827;">You're verified</h1>
    <p style="margin:0 0 20px; font-size:15px; color:#4b5563;">
        Hi {{ $user->name }}, your ID check went through. Your ProxWorld account is verified, so you can place orders now.
    </p>

    @include('emails.components.button', ['url' => route('order.create'), 'label' => 'Place an order'])
@endsection