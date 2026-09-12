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
            <td style="padding:10px 0; border-bottom:1px solid #eef1f6; font-size:14px; color:#111827; font-weight:600; text-align:right;">{{ number_format($order->quantity) }}</td>
        </tr>
        <tr>
            <td style="padding:10px 0; border-bottom:1px solid #eef1f6; font-size:13px; color:#6b7280;">Amount</td>
            <td style="padding:10px 0; border-bottom:1px solid #eef1f6; font-size:14px; color:#111827; font-weight:600; text-align:right;">{{ $order->currency ?? '₦' }} {{ number_format($order->charge, 2) }}</td>
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

    @if($order->isDataBasedProduct())
        @php($access = $order->provider->config['static_proxy_access'] ?? null)
        @if($access)
            <h2 style="margin:24px 0 8px; font-size:16px; font-weight:700; color:#111827;">Proxy access details</h2>
            <p style="margin:0 0 12px; font-size:13px; color:#6b7280;">
                This is your shared account-wide connection.
            </p>
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin:0 0 8px;">
                <tr>
                    <td style="padding:8px 0; border-bottom:1px solid #eef1f6; font-size:13px; color:#6b7280;">Host</td>
                    <td style="padding:8px 0; border-bottom:1px solid #eef1f6; font-size:13px; color:#111827; font-family:monospace; text-align:right;">{{ $access['host'] }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0; border-bottom:1px solid #eef1f6; font-size:13px; color:#6b7280;">Port</td>
                    <td style="padding:8px 0; border-bottom:1px solid #eef1f6; font-size:13px; color:#111827; font-family:monospace; text-align:right;">{{ $access['port'] }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0; border-bottom:1px solid #eef1f6; font-size:13px; color:#6b7280;">Username</td>
                    <td style="padding:8px 0; border-bottom:1px solid #eef1f6; font-size:13px; color:#111827; font-family:monospace; text-align:right;">{{ $access['username'] }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0; font-size:13px; color:#6b7280;">Password</td>
                    <td style="padding:8px 0; font-size:13px; color:#111827; font-family:monospace; text-align:right;">{{ $access['password'] }}</td>
                </tr>
            </table>

            @if(!empty($access['username_format']))
                <p style="margin:12px 0 0; font-size:12px; color:#6b7280; background-color:#f9fafb; padding:8px 12px; border-radius:6px; font-family:monospace;">
                    <strong>Session format:</strong> {{ $access['username_format'] }}
                </p>
            @endif
        @endif
    @elseif($order->hasProxyCredentials())
        <h2 style="margin:24px 0 8px; font-size:16px; font-weight:700; color:#111827;">Proxy access details</h2>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin:0 0 8px;">
            <thead>
                <tr>
                    <th align="left" style="padding:8px 6px; font-size:11px; text-transform:uppercase; color:#6b7280; font-weight:700; border-bottom:2px solid #eef1f6;">IP</th>
                    <th align="left" style="padding:8px 6px; font-size:11px; text-transform:uppercase; color:#6b7280; font-weight:700; border-bottom:2px solid #eef1f6;">Port</th>
                    <th align="left" style="padding:8px 6px; font-size:11px; text-transform:uppercase; color:#6b7280; font-weight:700; border-bottom:2px solid #eef1f6;">Username</th>
                    <th align="left" style="padding:8px 6px; font-size:11px; text-transform:uppercase; color:#6b7280; font-weight:700; border-bottom:2px solid #eef1f6;">Password</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->proxy_data as $proxy)
                    <tr>
                        <td style="padding:8px 6px; font-size:12px; color:#111827; border-bottom:1px solid #eef1f6; font-family:monospace;">{{ $proxy['ip'] ?? $proxy['host'] ?? $proxy['proxy_ip_address'] ?? '—' }}</td>
                        <td style="padding:8px 6px; font-size:12px; color:#111827; border-bottom:1px solid #eef1f6; font-family:monospace;">{{ $proxy['port'] ?? $proxy['proxy_http_port'] ?? '—' }}</td>
                        <td style="padding:8px 6px; font-size:12px; color:#111827; border-bottom:1px solid #eef1f6; font-family:monospace;">{{ $proxy['username'] ?? $proxy['login'] ?? $proxy['default_proxy_user_username'] ?? '—' }}</td>
                        <td style="padding:8px 6px; font-size:12px; color:#111827; border-bottom:1px solid #eef1f6; font-family:monospace;">{{ $proxy['password'] ?? $proxy['default_proxy_user_password'] ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @include('emails.components.button', ['url' => url('/orders/'.$order->id), 'label' => 'View Order'])
@endsection