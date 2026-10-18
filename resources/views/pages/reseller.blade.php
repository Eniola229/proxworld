@extends('layouts.app')

@section('title', 'Reseller program — ProxWorld')
@section('description', 'Sell proxies under your own brand. Get your own storefront and a reseller panel to manage customers, powered by ProxWorld.')

@section('content')

<section class="page-hero">
    <div class="glow"></div>
    <div class="container">
        <div class="crumbs"><a href="{{ url('/') }}">Home</a> / Reseller program</div>
        <h1>Sell proxies under your own brand</h1>
        <p class="lede">Get a storefront with your name on it, manage your customers from one panel, and let ProxWorld handle the proxies behind it.</p>
        <div class="hero-cta">
            <a href="{{ route('register') }}" class="btn btn-solid btn-large">Become a reseller</a>
            <a href="{{ route('contact') }}" class="btn btn-ghost btn-large">Ask a question</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <h2>What you get</h2>
        </div>
        <div class="tiles c3">
            <div class="tile">
                <div class="ico"><i class="fas fa-store" aria-hidden="true"></i></div>
                <h3>Your own storefront</h3>
                <p>A shop your customers use to buy proxies from you, not from us.</p>
            </div>
            <div class="tile">
                <div class="ico"><i class="fas fa-users-gear" aria-hidden="true"></i></div>
                <h3>A reseller panel</h3>
                <p>See your customers and their orders, and manage everything in one place.</p>
            </div>
            <div class="tile">
                <div class="ico"><i class="fas fa-code" aria-hidden="true"></i></div>
                <h3>API access</h3>
                <p>Plug ProxWorld into your own site or tools if you want a fully custom setup.</p>
            </div>
        </div>
    </div>
</section>

<section class="section alt">
    <div class="container">
        <div class="section-head">
            <h2>How it works</h2>
        </div>
        <ol class="steps">
            <li>
                <h3>Create your account</h3>
                <p>Sign up and tell us you want to resell.</p>
            </li>
            <li>
                <h3>Set up your storefront</h3>
                <p>Add your branding and choose the proxy types you want to sell.</p>
            </li>
            <li>
                <h3>Start selling</h3>
                <p>Share your store link. Your customers order, and the proxies are delivered for you.</p>
            </li>
        </ol>
    </div>
</section>

<section class="cta-band">
    <h2>Questions about reselling?</h2>
    <p>Tell us how many customers you expect and what you plan to sell.</p>
    <a href="{{ route('contact') }}" class="btn btn-light btn-large">Contact us</a>
    <a href="{{ route('register') }}" class="btn btn-outline-light btn-large">Create account</a>
</section>

@endsection
