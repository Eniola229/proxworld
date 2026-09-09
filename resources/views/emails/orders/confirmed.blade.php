@extends('emails.layout')

@section('title', 'Order confirmed')
@section('preheader', 'Your order has been fulfilled successfully.')

@section('content')
    <h1 style="margin:0 0 8px; font-size:20px; font-weight:700; color:#111827;">Order confirmed</h1>
    <p style="margin:0 0 20px; font-size:15px; color:#4b5563;">
        Hi {{ $order->user->name }}, your order has been fulfilled successfully.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin:0 0 8px;">
        <tr>
            <td style="padding:10px 0; border-bottom:1px solid #eef1f6; font-size:13px; color:#6b7280;">Order ID</td>
            <td style="padding:10px 0; border-bottom:1px solid #eef1f6; font-size:14px; color:#111827; font-weight:600; text-align:right;">{{ Str::substr($order->id, 0, 8) }}</td>
        </tr>
        <tr>
            <td style="padding:10px 0; border-bottom:1px solid #eef1f6; font-size:13px; color:#6b7280;">Service</td>
            <td style="padding:10px 0; border-bottom:1px solid #eef1f6; font-size:14px; color:#111827; font-weight:600; text-align:right;">{{ $order->service_name }}</td>
        </tr>
        <tr>
            <td style="padding:10px 0; border-bottom:1px solid #eef1f6; font-size:13px; color:#6b7280;">Quantity</td>
            <td style="padding:10px 0; border-bottom:1px solid #eef1f6; font-size:14px; color:#111827; font-weight:600; text-align:right;">{{ $order->quantity }}</td>
        </tr>
        <tr>
            <td style="padding:10px 0; border-bottom:1px solid #eef1f6; font-size:13px; color:#6b7280;">Amount</td>
            <td style="padding:10px 0; border-bottom:1px solid #eef1f6; font-size:14px; color:#111827; font-weight:600; text-align:right;">{{ $order->currency }} {{ number_format($order->charge, 2) }}</td>
        </tr>
        <tr>
            <td style="padding:10px 0; font-size:13px; color:#6b7280;">Status</td>
            <td style="padding:10px 0; text-align:right;">
                <span style="display:inline-block; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:700; color:#ffffff; background-color:#16a34a;">
                    {{ ucfirst($order->status) }}
                </span>
            </td>
        </tr>
    </table>

    @include('emails.components.button', ['url' => url('/orders/'.$order->id), 'label' => 'View Order'])
@endsection
