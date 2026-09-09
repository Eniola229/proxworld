<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reseller->panel_name }} — Proxy Access</title>
    <meta name="description" content="Residential, datacenter, ISP and mobile proxies from {{ $reseller->panel_name }}.">

    {{-- Favicon: reseller logo if set, else default --}}
    @if($reseller->logo_path)
        <link rel="shortcut icon" type="image/x-icon" href="{{ $reseller->logo_path }}" />
    @else
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/B.png') }}" />
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --accent: {{ $reseller->primary_color ?? '#2563EB' }};
            --ink:    #171512;
            --slate:  #5B564E;
            --line:   #E7E2D8;
            --bg:     #FAFAF7;
            --card:   #FFFFFF;
            --accent-rgb: {{ implode(',', sscanf($reseller->primary_color ?? '#2563EB', '#%02x%02x%02x')) }};
        }

        html { -webkit-font-smoothing: antialiased; scroll-behavior: smooth; }
        html, body { overflow-x: hidden; width: 100%; max-width: 100%; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--ink); line-height: 1.55; }
        a { color: inherit; }
        img { display: block; max-width: 100%; }

        .wrap { max-width: 1100px; margin: 0 auto; padding: 0 1.75rem; width: 100%; }

        h1, h2, h3 { font-family: 'Fraunces', serif; font-weight: 600; letter-spacing: -0.01em; }

        /* HEADER */
        header { border-bottom: 1px solid var(--line); position: sticky; top: 0; background: rgba(250,250,247,0.92); backdrop-filter: blur(8px); z-index: 10; }
        .header-inner { display: flex; align-items: center; justify-content: space-between; padding: 1.1rem 0; gap: 1rem; flex-wrap: wrap; }
        .brand { display: flex; align-items: center; gap: 0.6rem; text-decoration: none; }
        .brand img { height: 30px; width: auto; }
        .brand-name { font-family: 'Fraunces', serif; font-weight: 600; font-size: 1.15rem; }
        nav.main-nav { display: flex; gap: 2rem; }
        nav.main-nav a { font-size: 0.92rem; color: var(--slate); text-decoration: none; }
        nav.main-nav a:hover { color: var(--ink); }
        .header-actions { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }
        .link-muted { font-size: 0.92rem; color: var(--slate); text-decoration: none; }
        .link-muted:hover { color: var(--ink); }
        .btn { font-weight: 600; font-size: 0.9rem; padding: 0.65rem 1.3rem; border-radius: 7px; text-decoration: none; display: inline-block; background: var(--accent); color: #fff; transition: transform 0.15s, filter 0.15s; white-space: nowrap; }
        .btn:hover { filter: brightness(1.08); transform: translateY(-1px); }
        .btn-large { padding: 0.85rem 1.7rem; font-size: 1rem; }
        .btn-outline { background: transparent; border: 1.5px solid var(--line); color: var(--ink); }
        .btn-outline:hover { border-color: var(--ink); filter: none; }

        /* HERO */
        .hero { padding: 5rem 0 4rem; }
        .status-line { display: flex; align-items: center; gap: 0.55rem; font-size: 0.88rem; color: var(--slate); margin-bottom: 1.5rem; }
        .dot { width: 7px; height: 7px; border-radius: 50%; background: var(--accent); animation: pulse 2.4s ease-out infinite; }
        @keyframes pulse {
            0%   { box-shadow: 0 0 0 0 rgba(var(--accent-rgb), 0.45); }
            70%  { box-shadow: 0 0 0 8px rgba(var(--accent-rgb), 0); }
            100% { box-shadow: 0 0 0 0 rgba(var(--accent-rgb), 0); }
        }
        .hero-grid { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 3rem; align-items: center; }
        h1.hero-title { font-size: clamp(2.3rem, 4.2vw, 3.3rem); line-height: 1.12; max-width: 18ch; }
        .hero p.lead { margin-top: 1.2rem; color: var(--slate); font-size: 1.08rem; max-width: 42ch; }
        .hero-actions { margin-top: 2.1rem; display: flex; gap: 1rem; flex-wrap: wrap; }

        .proxy-card { background: var(--card); border: 1px solid var(--line); border-radius: 12px; padding: 1.6rem; }
        .proxy-card-row { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-top: 1px solid var(--line); }
        .proxy-card-row:first-child { border-top: none; padding-top: 0; }
        .proxy-card-row .name { display: flex; align-items: center; gap: 0.65rem; font-weight: 600; font-size: 0.94rem; }
        .proxy-card-row .name i { color: var(--accent); width: 18px; text-align: center; }
        .proxy-card-row .price { font-size: 0.86rem; color: var(--slate); }
        .proxy-card-foot { margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--line); font-size: 0.82rem; color: var(--slate); }

        /* STATS */
        .stats-bar { border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); background: var(--card); }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); }
        .stat { padding: 2.2rem 1.75rem; text-align: center; border-left: 1px solid var(--line); }
        .stat:first-child { border-left: none; }
        .stat-num { font-family: 'Fraunces', serif; font-size: 2.1rem; font-weight: 600; }
        .stat-label { font-size: 0.85rem; color: var(--slate); margin-top: 0.25rem; }

        /* FEATURES */
        .section { padding: 4.5rem 0; }
        .section-head { max-width: 46ch; margin-bottom: 2.5rem; }
        .section-head h2 { font-size: 1.8rem; }
        .section-head p { color: var(--slate); margin-top: 0.6rem; }
        .feature-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
        .feature-card { background: var(--card); border: 1px solid var(--line); border-radius: 12px; padding: 1.75rem; transition: transform 0.15s, box-shadow 0.15s; }
        .feature-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.05); }
        .feature-card i { color: var(--accent); font-size: 1.3rem; }
        .feature-card h3 { font-size: 1.05rem; margin-top: 0.9rem; }
        .feature-card p { color: var(--slate); font-size: 0.92rem; margin-top: 0.5rem; }

        /* PLATFORMS */
        .platform-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 1px; background: var(--line); border: 1px solid var(--line); border-radius: 10px; overflow: hidden; }
        .platform-cell { background: var(--card); padding: 1.5rem 0.75rem; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; }
        .platform-cell i, .platform-cell span.flag { font-size: 1.35rem; color: var(--slate); }
        .platform-cell .label { font-size: 0.72rem; color: var(--slate); text-align: center; }

        /* SUPPORT */
        .support-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; }
        .support-card { background: var(--card); border: 1px solid var(--line); border-radius: 12px; padding: 1.5rem; text-decoration: none; display: flex; flex-direction: column; gap: 0.7rem; transition: border-color 0.15s, transform 0.15s; }
        .support-card:hover { border-color: var(--accent); transform: translateY(-2px); }
        .support-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.05rem; color: #fff; }
        .support-card h3 { font-family: 'Inter', sans-serif; font-weight: 600; font-size: 0.98rem; color: var(--ink); }
        .support-card p { font-size: 0.86rem; color: var(--slate); }

        /* CTA */
        .cta-band { background: var(--ink); color: #fff; padding: 4rem 0; }
        .cta-inner { display: flex; align-items: center; justify-content: space-between; gap: 2rem; flex-wrap: wrap; }
        .cta-inner h2 { color: #fff; font-size: 1.9rem; max-width: 22ch; }
        .cta-inner p { color: #B9B4A9; margin-top: 0.5rem; }

        /* FOOTER */
        footer { padding: 3rem 0 2rem; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 2.5rem; padding-bottom: 2.2rem; border-bottom: 1px solid var(--line); }
        .footer-brand p { color: var(--slate); font-size: 0.9rem; max-width: 32ch; margin-top: 0.7rem; }
        .footer-col h4 { font-size: 0.82rem; font-weight: 600; margin-bottom: 1rem; }
        .footer-col ul { list-style: none; }
        .footer-col li { margin-bottom: 0.6rem; }
        .footer-col a { font-size: 0.88rem; color: var(--slate); text-decoration: none; }
        .footer-col a:hover { color: var(--ink); }
        .footer-bottom { display: flex; justify-content: space-between; padding-top: 1.5rem; flex-wrap: wrap; gap: 0.6rem; }
        .footer-bottom p { font-size: 0.8rem; color: var(--slate); }

        @media (max-width: 860px) {
            nav.main-nav { display: none; }
            .hero-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr; }
            .stat { border-left: none; border-top: 1px solid var(--line); }
            .stat:first-child { border-top: none; }
            .feature-grid { grid-template-columns: 1fr; }
            .platform-grid { grid-template-columns: repeat(3, 1fr); }
            .footer-grid { grid-template-columns: 1fr; gap: 1.75rem; }
        }

        @media (max-width: 600px) {
            .wrap { padding-left: 1.25rem; padding-right: 1.25rem; }
            .hero { padding: 3rem 0 2.5rem; }
            .header-inner { padding: 0.85rem 0; }
            .section { padding: 3rem 0; }
            h1.hero-title { max-width: 100%; }
            .stat { padding: 1.75rem 1.25rem; }
            .platform-grid { grid-template-columns: repeat(2, 1fr); }
        }

        /* ===== FORCE CONSISTENT MOBILE SPACING — PASTE LAST ===== */
@media (max-width: 600px) {
    .wrap,
    header .wrap,
    .hero,
    .stats-bar .wrap,
    #features.wrap,
    #proxies.wrap,
    #support.wrap,
    .cta-band .wrap,
    footer .wrap {
        padding-left: 1.25rem !important;
        padding-right: 1.25rem !important;
    }

    .hero {
        padding-top: 3rem !important;
        padding-bottom: 2.5rem !important;
    }

    .section {
        padding-top: 3rem !important;
        padding-bottom: 3rem !important;
    }

    .header-inner {
        padding-top: 0.85rem !important;
        padding-bottom: 0.85rem !important;
    }

    .cta-band {
        padding-top: 3rem !important;
        padding-bottom: 3rem !important;
    }

    footer {
        padding-top: 3rem !important;
        padding-bottom: 2rem !important;
    }

    h1.hero-title { max-width: 100% !important; }
    .stat { padding: 1.75rem 1.25rem !important; }
    .platform-grid { grid-template-columns: repeat(2, 1fr) !important; }
}
    </style>
</head>
<body>

<header>
    <div class="wrap header-inner">
        @if($reseller->logo_path)
            <a href="/" class="brand">
                <img src="{{ $reseller->logo_path }}" alt="{{ $reseller->panel_name }}">
            </a>
        @else
            <a href="/" class="brand"><span class="brand-name">{{ $reseller->panel_name }}</span></a>
        @endif

        <nav class="main-nav">
            <a href="#proxies">Proxy Types</a>
            <a href="#features">Why Us</a>
            <a href="#support">Support</a>
        </nav>

        <div class="header-actions">
            <a href="{{ route('storefront.login') }}" class="link-muted">Log in</a>
            <a href="{{ route('storefront.register') }}" class="btn">Sign up</a>
        </div>
    </div>
</header>

<section class="wrap hero">
    <div class="hero-grid">
        <div>
            <div class="status-line"><span class="dot"></span> Network online</div>
            <h1 class="hero-title">Proxy access, provisioned in minutes.</h1>
            <p class="lead">{{ $reseller->panel_name }} gives you residential, datacenter, ISP and mobile proxies — order, pay, and get your credentials without waiting around.</p>
            <div class="hero-actions">
                <a href="{{ route('storefront.register') }}" class="btn btn-large">Create an account</a>
                <a href="{{ route('storefront.login') }}" class="btn btn-outline btn-large">Log in</a>
            </div>
        </div>
        <div class="proxy-card">
            <div class="proxy-card-row">
                <div class="name"><i class="fas fa-house-user"></i> Residential</div>
                <div class="price">Per GB</div>
            </div>
            <div class="proxy-card-row">
                <div class="name"><i class="fas fa-server"></i> Datacenter</div>
                <div class="price">Per IP</div>
            </div>
            <div class="proxy-card-row">
                <div class="name"><i class="fas fa-network-wired"></i> ISP</div>
                <div class="price">Per IP</div>
            </div>
            <div class="proxy-card-row">
                <div class="name"><i class="fas fa-mobile-screen"></i> Mobile</div>
                <div class="price">Per GB</div>
            </div>
            <div class="proxy-card-foot">Pricing is set per order at checkout — no separate plans to compare.</div>
        </div>
    </div>
</section>

<div class="stats-bar">
    <div class="wrap stats-grid">
        <div class="stat">
            <div class="stat-num">4</div>
            <div class="stat-label">Proxy Types</div>
        </div>
        <div class="stat">
            <div class="stat-num">190+</div>
            <div class="stat-label">Countries Covered</div>
        </div>
        <div class="stat">
            <div class="stat-num">99.9%</div>
            <div class="stat-label">Network Uptime</div>
        </div>
    </div>
</div>

<section id="features" class="wrap section">
    <div class="section-head">
        <h2>Built to just work</h2>
        <p>No unnecessary steps between paying and getting a working proxy.</p>
    </div>
    <div class="feature-grid">
        <div class="feature-card">
            <i class="fas fa-bolt"></i>
            <h3>Instant delivery</h3>
            <p>Credentials land in your dashboard the moment payment clears — no waiting on manual approval.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-shield-alt"></i>
            <h3>Clean IP pools</h3>
            <p>Every proxy pool is sourced from vetted networks, not overused or blacklisted ranges.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-gauge-high"></i>
            <h3>Low latency</h3>
            <p>Fast response times whether you're scraping, automating, or browsing anonymously.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-tag"></i>
            <h3>Fair pricing</h3>
            <p>Pay for what you use, with bulk discounts as your volume grows.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-rotate"></i>
            <h3>Rotating sessions</h3>
            <p>Switch IPs automatically or hold a session as long as you need it.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-undo"></i>
            <h3>Straightforward refunds</h3>
            <p>If something's wrong with an order, we make it right — no hoops to jump through.</p>
        </div>
    </div>
</section>

<section id="proxies" class="wrap section">
    <div class="section-head">
        <h2>Coverage across 190+ countries</h2>
        <p>Pick a proxy type and a country, and you're set.</p>
    </div>
    <div class="platform-grid">
        <div class="platform-cell"><span class="flag">🇺🇸</span><span class="label">USA</span></div>
        <div class="platform-cell"><span class="flag">🇬🇧</span><span class="label">UK</span></div>
        <div class="platform-cell"><span class="flag">🇩🇪</span><span class="label">Germany</span></div>
        <div class="platform-cell"><span class="flag">🇳🇬</span><span class="label">Nigeria</span></div>
        <div class="platform-cell"><span class="flag">🇮🇳</span><span class="label">India</span></div>
        <div class="platform-cell"><span class="flag">🇧🇷</span><span class="label">Brazil</span></div>
        <div class="platform-cell"><span class="flag">🇿🇦</span><span class="label">South Africa</span></div>
        <div class="platform-cell"><span class="flag">🇦🇪</span><span class="label">UAE</span></div>
        <div class="platform-cell"><span class="flag">🇨🇦</span><span class="label">Canada</span></div>
        <div class="platform-cell"><i class="fas fa-house-user"></i><span class="label">Residential</span></div>
        <div class="platform-cell"><i class="fas fa-server"></i><span class="label">Datacenter</span></div>
        <div class="platform-cell"><i class="fas fa-earth-americas"></i><span class="label">+ more</span></div>
    </div>
</section>

@if($reseller->support_email || $reseller->telegram_link || $reseller->whatsapp_link)
<section id="support" class="wrap section">
    <div class="section-head">
        <h2>Talk to us</h2>
        <p>Reach {{ $reseller->panel_name }} directly — pick whichever's easiest for you.</p>
    </div>
    <div class="support-grid">
        @if($reseller->telegram_link)
        <a href="{{ $reseller->telegram_link }}" target="_blank" class="support-card">
            <div class="support-icon" style="background:#0088cc;"><i class="fab fa-telegram"></i></div>
            <h3>Telegram</h3>
            <p>Fastest way to reach us — usually replies within minutes.</p>
        </a>
        @endif
        @if($reseller->whatsapp_link)
        <a href="{{ $reseller->whatsapp_link }}" target="_blank" class="support-card">
            <div class="support-icon" style="background:#25D366;"><i class="fab fa-whatsapp"></i></div>
            <h3>WhatsApp</h3>
            <p>Message us directly if you'd rather not use Telegram.</p>
        </a>
        @endif
        @if($reseller->support_email)
        <a href="mailto:{{ $reseller->support_email }}" class="support-card">
            <div class="support-icon" style="background:var(--accent);"><i class="fas fa-envelope"></i></div>
            <h3>Email</h3>
            <p>{{ $reseller->support_email }}</p>
        </a>
        @endif
    </div>
</section>
@endif

<div class="cta-band">
    <div class="wrap cta-inner">
        <div>
            <h2>Ready to get your first proxy?</h2>
            <p>Create an account and check out in under two minutes.</p>
        </div>
        <a href="{{ route('storefront.register') }}" class="btn btn-large">Create an account</a>
    </div>
</div>

<footer>
    <div class="wrap footer-grid">
        <div class="footer-brand">
            @if($reseller->logo_path)
                <img src="{{ $reseller->logo_path }}" alt="{{ $reseller->panel_name }}" style="height:32px;">
            @else
                <span class="brand-name">{{ $reseller->panel_name }}</span>
            @endif
            <p>Your proxy provider — residential, datacenter, ISP and mobile, delivered fast.</p>
        </div>
        <div class="footer-col">
            <h4>Navigate</h4>
            <ul>
                <li><a href="{{ route('storefront.login') }}">Log in</a></li>
                <li><a href="{{ route('storefront.register') }}">Sign up</a></li>
                <li><a href="#proxies">Proxy Types</a></li>
                <li><a href="#features">Why Us</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Contact</h4>
            <ul>
                @if($reseller->telegram_link)<li><a href="{{ $reseller->telegram_link }}" target="_blank">Telegram</a></li>@endif
                @if($reseller->whatsapp_link)<li><a href="{{ $reseller->whatsapp_link }}" target="_blank">WhatsApp</a></li>@endif
                @if($reseller->support_email)<li><a href="mailto:{{ $reseller->support_email }}">Email</a></li>@endif
            </ul>
        </div>
    </div>
    <div class="wrap footer-bottom">
        <p>© {{ date('Y') }} {{ $reseller->panel_name }}. All rights reserved.</p>
        <p><a href="proxworld.shop"> Powered by ProxWorld</a></p> | <a href="/terms-of-use"> Terms and Condition</a>
    </div>
</footer>

</body>
</html>