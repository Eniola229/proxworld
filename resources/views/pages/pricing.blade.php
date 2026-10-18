@extends('layouts.app')

@section('title', 'Pricing — ProxWorld')
@section('description', 'Simple proxy pricing. Pay per GB for residential, per proxy for ISP and datacenter, and per day for mobile. Fund your wallet and only pay for what you use.')

@section('content')

<section class="page-hero">
    <div class="glow"></div>
    <div class="container">
        <div class="crumbs"><a href="{{ url('/') }}">Home</a> / Pricing</div>
        <h1>Pay for what you use</h1>
        <p class="lede">Fund your wallet, pick a proxy type, and you are charged only for what you order.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="price-grid">
            @foreach ($products as $slug => $p)
                <div class="price-card">
                    <span class="pill-ico {{ $p['color'] }}"><i class="fas {{ $p['icon'] }}" aria-hidden="true"></i></span>
                    <h3>{{ $p['name'] }}</h3>
                    <div class="model">{{ $p['model'] }}</div>

                    <div class="amount">
                        @if ($p['from'])
                            {{ $p['from'] }} <small>/ {{ $p['unit'] }}</small>
                        @else
                            Live price
                        @endif
                    </div>
                    <div class="model">{{ $p['from'] ? 'Starting price' : 'Shown in your dashboard' }}</div>

                    <ul>
                        @foreach (array_slice($p['best_for'], 0, 3) as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                        <li>{{ $p['specs']['Protocols'] ?? 'HTTP(S), SOCKS5' }}</li>
                    </ul>

                    <a href="{{ route('order.create', ['type' => $slug]) }}" class="btn btn-solid btn-block">Purchase now</a>
                    <a href="{{ route('products.show', $slug) }}" class="btn btn-ghost btn-block" style="margin-top:.7rem">Details</a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section alt">
    <div class="container">
        <div class="section-head">
            <h2>Which one should I pick?</h2>
        </div>
        <div class="table-wrap">
            <table class="cmp">
                <thead>
                    <tr><th>Type</th><th>Billing</th><th>Speed</th><th>Trust level</th><th>Pick it for</th></tr>
                </thead>
                <tbody>
                    <tr><td>Residential</td><td>Per GB</td><td>Good</td><td>High</td><td>Scraping protected sites, geo-targeting</td></tr>
                    <tr><td>ISP</td><td>Per proxy</td><td>Very fast</td><td>High</td><td>Stable sessions, e-commerce, accounts</td></tr>
                    <tr><td>Datacenter</td><td>Per proxy</td><td>Fastest</td><td>Medium</td><td>Bulk jobs on sites that do not block hosting IPs</td></tr>
                    <tr><td>Mobile</td><td>Per day</td><td>Good</td><td>Highest</td><td>Social platforms, mobile app tests</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <h2>How billing works</h2>
        </div>
        <ol class="steps">
            <li>
                <h3>Fund your wallet</h3>
                <p>Add money to your wallet whenever you like.</p>
            </li>
            <li>
                <h3>Order what you need</h3>
                <p>Choose the proxy type, location and amount. The cost comes out of your wallet.</p>
            </li>
            <li>
                <h3>Top up when you run low</h3>
                <p>Reselling or running a team? <a href="{{ route('reseller') }}">See the reseller program</a> or <a href="{{ route('contact') }}">contact us</a> for large orders.</p>
            </li>
        </ol>
    </div>
</section>

<section class="cta-band">
    <h2>Start with a small order</h2>
    <p>Test the proxies on your own targets before you scale up.</p>
    <a href="{{ route('register') }}" class="btn btn-light btn-large">Create account</a>
</section>

@endsection