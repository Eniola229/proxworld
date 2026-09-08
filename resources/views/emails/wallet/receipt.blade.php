@extends('emails.layout')
@section('content')
    <h2>Wallet Funded 💰</h2>
    <p>Your wallet has been credited successfully.</p>
    <table>
        <tr><td>Reference</td><td>{{ $transaction->reference }}</td></tr>
        <tr><td>Amount</td><td>{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</td></tr>
        <tr><td>New Balance</td><td>{{ $transaction->currency }} {{ number_format($transaction->balance_after, 2) }}</td></tr>
    </table>
    <a class="btn" href="{{ url('/wallet') }}">View Wallet</a>
@endsection
