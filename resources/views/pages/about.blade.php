@extends('layouts.app')

@section('title', 'About ProxWorld')
@section('description', 'ProxWorld sells residential, datacenter, ISP and mobile proxies. Learn who we are and how we work.')

@section('content')

<section class="page-hero">
    <div class="glow"></div>
    <div class="container">
        <div class="crumbs"><a href="{{ url('/') }}">Home</a> / About</div>
        <h1>We sell proxies. We try to do it well.</h1>
        <p class="lede">ProxWorld gives developers, agencies and resellers a straightforward way to buy proxies, pay in their own currency, and get on with their work.</p>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width:820px">
        <div class="section-head" style="max-width:none">
            <h2>Why ProxWorld exists</h2>
        </div>
        <p style="color:var(--muted);line-height:1.85;margin-bottom:1.2rem">
            Buying proxies from abroad can be a hassle: foreign payment cards, unclear pricing and support that answers days later. We built ProxWorld so you can fund a wallet, pick the proxies you need and start working.
        </p>
        <p style="color:var(--muted);line-height:1.85;margin-bottom:1.2rem">
            We offer four proxy types, an API for people who automate their orders, and a reseller program for anyone who wants to sell proxies under their own brand.
        </p>
    </div>
</section>

<section class="section alt">
    <div class="container">
        <div class="section-head">
            <h2>How we work</h2>
        </div>
        <div class="tiles c3">
            <div class="tile">
                <div class="ico"><i class="fas fa-eye" aria-hidden="true"></i></div>
                <h3>Clear pricing</h3>
                <p>You see what a proxy costs before you pay. No surprise charges.</p>
            </div>
            <div class="tile">
                <div class="ico"><i class="fas fa-shield-halved" aria-hidden="true"></i></div>
                <h3>Responsible use</h3>
                <p>We verify accounts and enforce our acceptable use rules to keep the network clean for everyone.</p>
            </div>
            <div class="tile">
                <div class="ico"><i class="fas fa-headset" aria-hidden="true"></i></div>
                <h3>Real people</h3>
                <p>Write to us and a person answers. We read every message.</p>
            </div>
        </div>
    </div>
</section>

<section class="cta-band">
    <h2>Try it on a real task</h2>
    <p>Create an account and see if ProxWorld fits your workflow.</p>
    <a href="{{ route('register') }}" class="btn btn-light btn-large">Get started</a>
    <a href="{{ route('contact') }}" class="btn btn-outline-light btn-large">Contact us</a>
</section>

@endsection
