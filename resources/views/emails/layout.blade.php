<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; background:#f4f6f5; margin:0; padding:0; color:#1f2937; }
        .wrapper { max-width: 560px; margin: 0 auto; padding: 32px 16px; }
        .card { background:#ffffff; border-radius:12px; padding:32px; box-shadow:0 1px 3px rgba(0,0,0,0.06); }
        .brand { color:#16a34a; font-weight:700; font-size:20px; margin-bottom:24px; }
        .btn { display:inline-block; background:#16a34a; color:#ffffff !important; text-decoration:none; padding:12px 24px; border-radius:8px; font-weight:600; margin-top:16px; }
        .footer { text-align:center; color:#9ca3af; font-size:12px; margin-top:24px; }
        table { width:100%; border-collapse: collapse; margin-top:16px; }
        td { padding:8px 0; border-bottom:1px solid #f0f0f0; font-size:14px; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <div class="brand">{{ config('app.name') }}</div>
        @yield('content')
    </div>
    <div class="footer">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</div>
</div>
</body>
</html>
