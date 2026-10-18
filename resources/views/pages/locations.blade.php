@extends('layouts.app')

@section('title', 'Proxy locations in 190+ countries — ProxWorld')
@section('description', 'Choose proxies from 190+ countries. Browse top locations and every region across Africa, Europe, the Americas, Asia and Oceania.')

@php
    // Top locations: 'Country name' => 'iso code' (flags come from flagcdn.com).
    $top = [
        'United States' => 'us', 'Spain' => 'es', 'United Kingdom' => 'gb', 'Germany' => 'de',
        'France' => 'fr', 'Italy' => 'it', 'India' => 'in', 'Poland' => 'pl',
        'Brazil' => 'br', 'Philippines' => 'ph', 'Japan' => 'jp', 'Vietnam' => 'vn',
    ];

    // All locations by region: 'Country name' => 'iso code'.
    // A null code (no standard flag, e.g. Somaliland) shows a plain placeholder instead of a flag.
    $regions = [
        'Africa' => [
            'Algeria' => 'dz', 'Angola' => 'ao', 'Benin' => 'bj', 'Botswana' => 'bw', 'Burkina Faso' => 'bf',
            'Burundi' => 'bi', 'Cameroon' => 'cm', 'Central African Republic' => 'cf', 'Chad' => 'td',
            'Democratic Republic of the Congo' => 'cd', 'Djibouti' => 'dj', 'Egypt' => 'eg',
            'Equatorial Guinea' => 'gq', 'Eritrea' => 'er', 'Ethiopia' => 'et', 'Gabon' => 'ga', 'Gambia' => 'gm',
            'Ghana' => 'gh', 'Guinea' => 'gn', 'Guinea Bissau' => 'gw', 'Ivory Coast' => 'ci', 'Kenya' => 'ke',
            'Lesotho' => 'ls', 'Liberia' => 'lr', 'Libya' => 'ly', 'Madagascar' => 'mg', 'Malawi' => 'mw',
            'Mali' => 'ml', 'Mauritania' => 'mr', 'Morocco' => 'ma', 'Mozambique' => 'mz', 'Namibia' => 'na',
            'Niger' => 'ne', 'Nigeria' => 'ng', 'Republic of the Congo' => 'cg', 'Rwanda' => 'rw', 'Senegal' => 'sn',
            'Sierra Leone' => 'sl', 'Somalia' => 'so', 'Somaliland' => null, 'South Africa' => 'za',
            'South Sudan' => 'ss', 'Sudan' => 'sd', 'Swaziland' => 'sz', 'Tanzania' => 'tz', 'Togo' => 'tg',
            'Tunisia' => 'tn', 'Uganda' => 'ug', 'Zambia' => 'zm', 'Zimbabwe' => 'zw',
        ],
        'Asia' => [
            'Armenia' => 'am', 'Azerbaijan' => 'az', 'Bangladesh' => 'bd', 'Bhutan' => 'bt', 'Brunei' => 'bn',
            'Cambodia' => 'kh', 'China' => 'cn', 'East Timor' => 'tl', 'Hong Kong' => 'hk', 'India' => 'in',
            'Indonesia' => 'id', 'Iraq' => 'iq', 'Israel' => 'il', 'Japan' => 'jp', 'Jordan' => 'jo',
            'Kazakhstan' => 'kz', 'Kuwait' => 'kw', 'Kyrgyzstan' => 'kg', 'Laos' => 'la', 'Lebanon' => 'lb',
            'Malaysia' => 'my', 'Maldives' => 'mv', 'Mongolia' => 'mn', 'Myanmar' => 'mm', 'Nepal' => 'np',
            'Oman' => 'om', 'Pakistan' => 'pk', 'Palestine' => 'ps', 'Philippines' => 'ph', 'Qatar' => 'qa',
            'Saudi Arabia' => 'sa', 'Singapore' => 'sg', 'South Korea' => 'kr', 'Sri Lanka' => 'lk', 'Taiwan' => 'tw',
            'Tajikistan' => 'tj', 'Thailand' => 'th', 'Turkey' => 'tr', 'Turkmenistan' => 'tm',
            'United Arab Emirates' => 'ae', 'Uzbekistan' => 'uz', 'Vietnam' => 'vn', 'Yemen' => 'ye',
        ],
        'Europe' => [
            'Albania' => 'al', 'Andorra' => 'ad', 'Austria' => 'at', 'Belgium' => 'be',
            'Bosnia and Herzegovina' => 'ba', 'Bulgaria' => 'bg', 'Croatia' => 'hr', 'Cyprus' => 'cy',
            'Czech Republic' => 'cz', 'Denmark' => 'dk', 'Estonia' => 'ee', 'Finland' => 'fi', 'France' => 'fr',
            'Germany' => 'de', 'Gibraltar' => 'gi', 'Greece' => 'gr', 'Hungary' => 'hu', 'Iceland' => 'is',
            'Ireland' => 'ie', 'Italy' => 'it', 'Latvia' => 'lv', 'Liechtenstein' => 'li', 'Lithuania' => 'lt',
            'Luxembourg' => 'lu', 'North Macedonia' => 'mk', 'Malta' => 'mt', 'Moldova' => 'md', 'Monaco' => 'mc',
            'Montenegro' => 'me', 'Netherlands' => 'nl', 'Northern Cyprus' => null, 'Norway' => 'no',
            'Poland' => 'pl', 'Portugal' => 'pt', 'Romania' => 'ro', 'San Marino' => 'sm', 'Serbia' => 'rs',
            'Slovakia' => 'sk', 'Slovenia' => 'si', 'Spain' => 'es', 'Sweden' => 'se', 'Switzerland' => 'ch',
            'Ukraine' => 'ua', 'United Kingdom' => 'gb',
        ],
        'North America' => [
            'Bahamas' => 'bs', 'Belize' => 'bz', 'Bermuda' => 'bm', 'Canada' => 'ca', 'Costa Rica' => 'cr',
            'Dominican Republic' => 'do', 'El Salvador' => 'sv', 'Greenland' => 'gl', 'Guatemala' => 'gt',
            'Haiti' => 'ht', 'Honduras' => 'hn', 'Jamaica' => 'jm', 'Mexico' => 'mx', 'Nicaragua' => 'ni',
            'Panama' => 'pa', 'Puerto Rico' => 'pr', 'Trinidad and Tobago' => 'tt', 'United States' => 'us',
        ],
        'Oceania' => [
            'Australia' => 'au', 'Fiji' => 'fj', 'New Caledonia' => 'nc', 'New Zealand' => 'nz',
            'Papua New Guinea' => 'pg', 'Solomon Islands' => 'sb', 'Vanuatu' => 'vu',
        ],
        'South America' => [
            'Argentina' => 'ar', 'Bolivia' => 'bo', 'Brazil' => 'br', 'Chile' => 'cl', 'Colombia' => 'co',
            'Ecuador' => 'ec', 'Falkland Islands' => 'fk', 'French Guiana' => 'gf', 'Guyana' => 'gy',
            'Paraguay' => 'py', 'Peru' => 'pe', 'Suriname' => 'sr', 'Uruguay' => 'uy', 'Venezuela' => 've',
        ],
    ];
@endphp

@push('styles')
<style>
    .chip.chip-flag { display: inline-flex; align-items: center; gap: .6rem; padding: .5rem 1rem .5rem .7rem; font-size: .78rem; color: var(--navy); transition: border-color .2s, transform .2s; }
    .chip.chip-flag:hover { border-color: var(--accent); transform: translateY(-1px); }
    .chip-flag .flag { width: 24px; height: 18px; object-fit: cover; border-radius: 2px; flex-shrink: 0; box-shadow: 0 0 0 1px rgba(15, 23, 42, .12); background: var(--bg-2); }
    .chip-flag .flag-empty { display: inline-block; width: 24px; height: 18px; border-radius: 2px; flex-shrink: 0; background: var(--bg-2); box-shadow: 0 0 0 1px rgba(15, 23, 42, .12); }

    .loc-top { margin-bottom: 2.5rem; }
    .loc-top h3 { margin-bottom: 1rem; }
    .top-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: .75rem; }
    .top-card { display: flex; align-items: center; gap: .85rem; padding: .9rem 1rem; background: #fff; border: 1px solid rgba(15, 23, 42, .12); border-radius: 12px; color: var(--navy); font-weight: 600; font-size: .9rem; transition: border-color .2s, transform .2s; }
    .top-card:hover { border-color: var(--accent); transform: translateY(-1px); }
    .top-card .flag { width: 36px; height: 27px; object-fit: cover; border-radius: 3px; flex-shrink: 0; box-shadow: 0 0 0 1px rgba(15, 23, 42, .12); background: var(--bg-2); }
</style>
@endpush

@section('content')

<section class="page-hero">
    <div class="glow"></div>
    <div class="container">
        <div class="crumbs"><a href="{{ route('welcome') }}">Home</a> / Locations</div>
        <h1>Proxies in 190+ countries</h1>
        <p class="lede">Pick the country your target sees you from. Start with our top locations, search for a country, or browse every region below.</p>
    </div>
</section>

<section class="section">
    <div class="container">

        <div class="loc-top" id="locTop">
            <h3>Top locations</h3>
            <div class="top-grid">
                @foreach ($top as $country => $code)
                    <div class="top-card">
                        <img class="flag"
                             src="https://flagcdn.com/w40/{{ $code }}.png"
                             srcset="https://flagcdn.com/w80/{{ $code }}.png 2x"
                             width="36" height="27" loading="lazy" alt="">
                        {{ $country }}
                    </div>
                @endforeach
            </div>
        </div>

        <div class="field loc-search">
            <label for="locSearch">Search a country</label>
            <input type="search" id="locSearch" placeholder="Type a country name" autocomplete="off">
        </div>

        @foreach ($regions as $region => $countries)
            <div class="loc-region">
                <h3>{{ $region }}</h3>
                <div class="chips">
                    @foreach ($countries as $country => $code)
                        <span class="chip chip-flag" data-name="{{ strtolower($country) }}">
                            @if ($code)
                                <img class="flag"
                                     src="https://flagcdn.com/w40/{{ $code }}.png"
                                     srcset="https://flagcdn.com/w80/{{ $code }}.png 2x"
                                     width="24" height="18" loading="lazy" alt="">
                            @else
                                <span class="flag-empty" aria-hidden="true"></span>
                            @endif
                            {{ $country }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endforeach

        <p class="loc-none" id="locNone">No match in the list. <a href="{{ route('contact') }}">Ask us</a> if you need a specific country or city.</p>
    </div>
</section>

<section class="cta-band">
    <h2>Need a country that is not listed?</h2>
    <p>Coverage changes by proxy type. Tell us where you need to appear from and we will check.</p>
    <a href="{{ route('contact') }}" class="btn btn-light btn-large">Contact us</a>
    <a href="{{ route('register') }}" class="btn btn-outline-light btn-large">Create account</a>
</section>

<script>
    // Hide "Top locations" while searching so it doesn't sit above filtered results.
    (function () {
        const input = document.getElementById('locSearch');
        const top = document.getElementById('locTop');
        if (!input || !top) return;
        input.addEventListener('input', () => { top.hidden = input.value.trim() !== ''; });
    })();
</script>

@endsection