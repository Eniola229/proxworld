@extends('layouts.app')

@section('title', $p['name'] . ' — ProxWorld')
@section('description', $p['intro'])

@section('content')

<section class="page-hero">
    <div class="glow"></div>
    <div class="container split">
        <div>
            <div class="crumbs"><a href="{{ url('/') }}">Home</a> / Products / {{ $p['short'] }}</div>
            <h1>{{ $p['name'] }}</h1>
            <p class="lede">{{ $p['intro'] }}</p>
            <div class="hero-cta">
                <a href="{{ route('register') }}" class="btn btn-solid btn-large">Buy {{ strtolower($p['short']) }} proxies</a>
                <a href="{{ route('pricing') }}" class="btn btn-ghost btn-large">See pricing</a>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <span class="pill-ico {{ $p['color'] }}"><i class="fas {{ $p['icon'] }}" aria-hidden="true"></i></span>
                <div><strong>{{ $p['short'] }} at a glance</strong><small>{{ $p['model'] }}</small></div>
            </div>
            @foreach ($p['specs'] as $label => $value)
                <div class="spec-row"><span>{{ $label }}</span><span>{{ $value }}</span></div>
            @endforeach
            <div class="panel-foot">
                <a href="{{ route('register') }}" class="btn btn-solid btn-block">
                    {{ $p['from'] ? 'From ' . $p['from'] . ' / ' . $p['unit'] : 'Get started' }}
                </a>
            </div>
        </div>
    </div>
</section>

<section class="section alt">
    <div class="container">
        <div class="section-head">
            <h2>What you get</h2>
        </div>
        <div class="tiles c4">
            @foreach ($p['features'] as $f)
                <div class="tile">
                    <div class="ico"><i class="fas {{ $f[0] }}" aria-hidden="true"></i></div>
                    <h3>{{ $f[1] }}</h3>
                    <p>{{ $f[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <h2>Good for</h2>
            <p>These are the jobs people most often use {{ strtolower($p['short']) }} proxies for.</p>
        </div>
        <div class="chips">
            @foreach ($p['best_for'] as $item)
                <span class="chip">{{ $item }}</span>
            @endforeach
        </div>
    </div>
</section>

<section class="section alt">
    <div class="container">
        <div class="section-head">
            <h2>Up and running in three steps</h2>
        </div>
        <ol class="steps">
            <li>
                <h3>Create an account</h3>
                <p>Sign up and verify your details so your account can place orders.</p>
            </li>
            <li>
                <h3>Fund your wallet and order</h3>
                <p>Add money to your wallet, choose {{ strtolower($p['short']) }} proxies and your location, and confirm.</p>
            </li>
            <li>
                <h3>Copy your credentials</h3>
                <p>Your proxy details appear in the dashboard. Paste them into your tool, or order through the <a href="{{ route('api.docs') }}">API</a>.</p>
            </li>
        </ol>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <h2>Other proxy types</h2>
            <p>Not sure this is the right one? Compare the others.</p>
        </div>
        <div class="tiles c3">
            @foreach (config('products') as $key => $other)
                @continue($key === $slug)
                <div class="tile">
                    <div class="ico"><i class="fas {{ $other['icon'] }}" aria-hidden="true"></i></div>
                    <h3>{{ $other['name'] }}</h3>
                    <p>{{ $other['tagline'] }}.</p>
                    <a class="more" href="{{ route('products.show', $key) }}">Learn more</a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="cta-band">
    <h2>Ready to send your first request?</h2>
    <p>Create an account, fund your wallet and get your proxies in minutes.</p>
    <a href="{{ route('register') }}" class="btn btn-light btn-large">Get started</a>
    <a href="{{ route('contact') }}" class="btn btn-outline-light btn-large">Talk to us</a>
</section>

@endsection
