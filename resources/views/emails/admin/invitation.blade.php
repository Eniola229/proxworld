@extends('emails.layout')
@section('content')
    <h2>Welcome to the ProxWorld admin team</h2>
    <p>Hi {{ $admin->name }}, an account has been created for you with the role of <strong>{{ ucfirst(str_replace('_', ' ', $admin->role)) }}</strong>.</p>
    <p>Click below to set your password and access the admin panel. This link expires soon for security.</p>
    <a class="btn" href="{{ $url }}">Set Your Password</a>
@endsection
