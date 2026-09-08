<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="{{ $reseller->panel_name }} — Premium residential, datacenter, ISP &amp; mobile proxies." />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $reseller->panel_name }} || @yield('title', 'Premium Proxy Network')</title>

    {{-- Open Graph uses panel name only --}}
    <meta property="og:title" content="{{ $reseller->panel_name }} – Premium Proxies" />
    <meta property="og:description" content="Fast, reliable residential, datacenter, ISP, and mobile proxies." />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:site_name" content="{{ $reseller->panel_name }}" />

    {{-- Favicon: use reseller logo if set, else default --}}
    @if($reseller->logo_path)
        <link rel="shortcut icon" type="image/x-icon" href="{{ $reseller->logo_path }}" />
    @else
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/B.png') }}" />
    @endif

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/daterangepicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/theme.min.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Inject reseller primary colour so the whole NXL theme picks it up --}}
    <style>
        :root {
            --reseller-primary: {{ $reseller->primary_color ?? '#6366f1' }};
        }
        .btn-primary                          { background-color: var(--reseller-primary) !important; border-color: var(--reseller-primary) !important; }
        .btn-outline-primary                  { color: var(--reseller-primary) !important; border-color: var(--reseller-primary) !important; }
        .btn-outline-primary:hover            { background-color: var(--reseller-primary) !important; color: #fff !important; }
        .text-primary                         { color: var(--reseller-primary) !important; }
        .bg-primary                           { background-color: var(--reseller-primary) !important; }
        .border-primary                       { border-color: var(--reseller-primary) !important; }
        .progress-bar.bg-primary              { background-color: var(--reseller-primary) !important; }
        .nxl-link.active .nxl-micon,
        .nxl-link:hover  .nxl-micon          { color: var(--reseller-primary) !important; }
        .nxl-navbar .nxl-item.active > .nxl-link { border-left-color: var(--reseller-primary) !important; }
        .support-float-btn                    { background: var(--reseller-primary) !important; }
        a                                     { --bs-link-color: var(--reseller-primary); }
    </style>

    @stack('styles')
</head>
<body>