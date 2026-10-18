@php
    // ── SEO values. Every page can override these with @section(...) ──
    $siteName     = 'ProxWorld';
    $defaultTitle = 'ProxWorld — Premium Residential, Datacenter, ISP & Mobile Proxies';
    $defaultDesc  = 'Buy fast, reliable proxies with ProxWorld. Residential, datacenter, ISP, and mobile proxies across 190+ countries — built for scraping, automation, and anonymity.';

    $seoTitle  = trim($__env->yieldContent('title'))       ?: e($defaultTitle);
    $seoDesc   = trim($__env->yieldContent('description')) ?: e($defaultDesc);
    $seoUrl    = trim($__env->yieldContent('canonical'))   ?: url()->current();
    $seoImage  = trim($__env->yieldContent('og_image'))    ?: asset('assets/images/LOGO.png');
    $seoType   = trim($__env->yieldContent('og_type'))     ?: 'website';
    $seoRobots = trim($__env->yieldContent('robots'))      ?: 'index, follow, max-image-preview:large';

    $orgSchema = [
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => $siteName,
        'url'      => route('welcome'),
        'logo'     => asset('assets/images/LOGO.png'),
        'parentOrganization' => ['@type' => 'Organization', 'name' => 'AfricGEM International Company Limited'],
        'sameAs'   => [
            'https://t.me/proxworldhq',
            'https://www.tiktok.com/@ProxWorld3928',
            'https://www.x.com/prox_world',
            'https://www.instagram.com/proxworldhq',
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>{!! $seoTitle !!}</title>
<meta name="description" content="{!! $seoDesc !!}">
@hasSection('keywords')
<meta name="keywords" content="@yield('keywords')">
@endif
<meta name="robots" content="{{ $seoRobots }}">
<link rel="canonical" href="{{ $seoUrl }}">
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/LOGO.png') }}">

<!-- Open Graph: Facebook, WhatsApp, Telegram, LinkedIn -->
<meta property="og:type" content="{{ $seoType }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:url" content="{{ $seoUrl }}">
<meta property="og:title" content="{!! $seoTitle !!}">
<meta property="og:description" content="{!! $seoDesc !!}">
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
@stack('meta')

<!-- Twitter / X -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@prox_world">
<meta name="twitter:title" content="{!! $seoTitle !!}">
<meta name="twitter:description" content="{!! $seoDesc !!}">
<meta name="twitter:image" content="{{ $seoImage }}">

<script type="application/ld+json">{!! json_encode($orgSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@stack('jsonld')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Syne:wght@400;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/site.css') }}?v={{ @filemtime(public_path('css/site.css')) }}">
@stack('styles')

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-55FF9H7J');</script>
<!-- End Google Tag Manager -->

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-2T8TWXC01E"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'G-2T8TWXC01E');
</script>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-55FF9H7J" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<a class="skip-link" href="#main">Skip to content</a>
<div class="cursor" id="cursor"></div>
<div class="cursor-ring" id="cursorRing"></div>

@include('partials.header')

<main id="main">
    @yield('content')
</main>

@include('partials.footer')

<script src="{{ asset('js/site.js') }}?v={{ @filemtime(public_path('js/site.js')) }}" defer></script>
@stack('scripts')
</body>
</html>
