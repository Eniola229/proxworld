@extends('emails.layout')

@section('title', 'Reset your password')
@section('preheader', 'Use this link to reset your password.')

@section('content')
    <h1 style="margin:0 0 8px; font-size:20px; font-weight:700; color:#111827;">Reset your password</h1>
    <p style="margin:0 0 20px; font-size:14px; font-weight:600; color:#6b7280;">You requested a password reset</p>

    <p style="margin:0 0 16px; font-size:15px; color:#4b5563;">Hello,</p>
    <p style="margin:0 0 4px; font-size:15px; color:#4b5563;">
        You are receiving this email because we received a password reset request for your account.
    </p>

    @include('emails.components.button', ['url' => $url, 'label' => 'Reset Password'])

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:24px 0; background-color:#eff6ff; border-radius:8px;">
        <tr>
            <td style="padding:16px; font-size:13px; color:#1e3a8a;">
                <strong>This password reset link will expire in {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} minutes.</strong>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 4px; font-size:13px; color:#9ca3af;">
        If you did not request a password reset, no further action is required.
    </p>
    <p style="margin:12px 0 0; font-size:12px; color:#9ca3af;">
        If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your browser:
    </p>
    <p style="margin:4px 0 0; font-size:12px; word-break:break-all;">
        <a href="{{ $url }}" style="color:#2563eb;">{{ $url }}</a>
    </p>
@endsection
