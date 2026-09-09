@extends('emails.layout')

@section('title', 'Wallet funded')
@section('preheader', 'Your wallet has been credited successfully.')

@section('content')
    <h1 style="margin:0 0 8px; font-size:20px; font-weight:700; color:#111827;">Wallet funded</h1>
    <p style="margin:0 0 20px; font-size:15px; color:#4b5563;">Your wallet has been credited successfully.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin:0 0 8px;">
        <tr>
            <td style="padding:10px 0; border-bottom:1px solid #eef1f6; font-size:13px; color:#6b7280;">Reference</td>
            <td style="padding:10px 0; border-bottom:1px solid #eef1f6; font-size:14px; color:#111827; font-weight:600; text-align:right;">{{ $transaction->reference }}</td>
        </tr>
        <tr>
            <td style="padding:10px 0; border-bottom:1px solid #eef1f6; font-size:13px; color:#6b7280;">Amount</td>
            <td style="padding:10px 0; border-bottom:1px solid #eef1f6; font-size:14px; color:#16a34a; font-weight:700; text-align:right;">+{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</td>
        </tr>
        <tr>
            <td style="padding:10px 0; font-size:13px; color:#6b7280;">New Balance</td>
            <td style="padding:10px 0; font-size:14px; color:#111827; font-weight:700; text-align:right;">{{ $transaction->currency }} {{ number_format($transaction->balance_after, 2) }}</td>
        </tr>
    </table>

    @include('emails.components.button', ['url' => url('/wallet'), 'label' => 'View Wallet'])
@endsection
