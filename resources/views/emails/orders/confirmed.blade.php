@extends('emails.layout')
@section('content')
    <h2>Order Confirmed ✅</h2>
    <p>Hi {{ $order->user->name }}, your order has been fulfilled successfully.</p>
    <table>
        <tr><td>Order ID</td><td>{{ Str::substr($order->id, 0, 8) }}</td></tr>
        <tr><td>Service</td><td>{{ $order->service_name }}</td></tr>
        <tr><td>Quantity</td><td>{{ $order->quantity }}</td></tr>
        <tr><td>Amount</td><td>{{ $order->currency }} {{ number_format($order->charge, 2) }}</td></tr>
        <tr><td>Status</td><td>{{ ucfirst($order->status) }}</td></tr>
    </table>
    <a class="btn" href="{{ url('/orders/'.$order->id) }}">View Order</a>
@endsection
