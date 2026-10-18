@extends('layouts.app')

@section('title', 'Proxy use cases — ProxWorld')
@section('description', 'See what people use proxies for: web scraping, SEO tracking, price monitoring, ad verification, social media, sneaker sites and more — and which proxy type fits.')

@php
    $cases = [
        ['id' => 'web-scraping', 'icon' => 'fa-spider', 'title' => 'Web scraping',
         'text' => 'Collect public data at scale without your IP getting blocked after a few hundred requests. Rotating IPs spread your traffic so each site sees many visitors, not one.',
         'use' => ['residential', 'datacenter']],
        ['id' => 'seo', 'icon' => 'fa-magnifying-glass-chart', 'title' => 'SEO and SERP tracking',
         'text' => 'Check where a page ranks in Google from different countries and cities, without personalised results or captcha walls.',
         'use' => ['residential']],
        ['id' => 'price-monitoring', 'icon' => 'fa-tags', 'title' => 'Price monitoring',
         'text' => 'Track competitor prices and stock across regions. Many shops show different prices by location, so you need an IP from each one.',
         'use' => ['residential', 'isp']],
        ['id' => 'ad-verification', 'icon' => 'fa-bullhorn', 'title' => 'Ad verification',
         'text' => 'Confirm your ads show up where, and to whom, you paid for. View them the way a real person in the target country would.',
         'use' => ['residential', 'mobile']],
        ['id' => 'social-media', 'icon' => 'fa-share-nodes', 'title' => 'Social media management',
         'text' => 'Run several brand or client accounts without them being linked by one IP address. Mobile IPs give the best trust.',
         'use' => ['mobile', 'isp']],
        ['id' => 'ecommerce', 'icon' => 'fa-cart-shopping', 'title' => 'E-commerce automation',
         'text' => 'Keep stable, fast sessions for store management, order tracking and inventory tools that expect the same IP every time.',
         'use' => ['isp']],
        ['id' => 'testing', 'icon' => 'fa-vial', 'title' => 'App and website testing',
         'text' => 'See your site or app as users in other countries do: local content, payment options, language and load times.',
         'use' => ['datacenter', 'mobile']],
        ['id' => 'market-research', 'icon' => 'fa-chart-line', 'title' => 'Market research',
         'text' => 'Gather reviews, listings and public posts from many sources to understand a market before you enter it.',
         'use' => ['residential', 'datacenter']],
    ];
@endphp

@section('content')

<section class="page-hero">
    <div class="glow"></div>
    <div class="container">
        <div class="crumbs"><a href="{{ url('/') }}">Home</a> / Use cases</div>
        <h1>What people use proxies for</h1>
        <p class="lede">Find your job below and see which proxy type fits it best.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="tiles c2">
            @foreach ($cases as $c)
                <div class="tile" id="{{ $c['id'] }}">
                    <div class="ico"><i class="fas {{ $c['icon'] }}" aria-hidden="true"></i></div>
                    <h3>{{ $c['title'] }}</h3>
                    <p>{{ $c['text'] }}</p>
                    <div class="chips" style="margin-top:1.2rem">
                        @foreach ($c['use'] as $slug)
                            <a class="chip" href="{{ route('products.show', $slug) }}" style="text-decoration:none">
                                {{ config('products.' . $slug . '.short') }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="cta-band">
    <h2>Have a different use in mind?</h2>
    <p>Tell us what you are building and we will point you to the right proxy type.</p>
    <a href="{{ route('contact') }}" class="btn btn-light btn-large">Ask us</a>
    <a href="{{ route('register') }}" class="btn btn-outline-light btn-large">Get started</a>
</section>

@endsection
