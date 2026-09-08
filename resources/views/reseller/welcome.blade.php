<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reseller->panel_name }} — Premium Proxies</title>
    <meta name="description" content="Buy fast, reliable proxies with {{ $reseller->panel_name }}. Residential, datacenter, ISP, and mobile proxies." />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Syne:wght@400;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --accent:    {{ $reseller->primary_color ?? '#2563EB' }};
            --electric:  {{ $reseller->primary_color ?? '#3B82F6' }};
            --bg:        #F5F7FF;
            --bg-2:      #EEF1FA;
            --bg-3:      #E4E9F5;
            --surface:   #FFFFFF;
            --gold:      #D97706;
            --navy:      #0F172A;
            --navy-2:    #1E293B;
            --muted:     #64748B;
            --soft:      #94A3B8;
            --border:    rgba(0,0,0,0.08);
            --border-2:  rgba(0,0,0,0.1);
            --shadow:    0 2px 20px rgba(15, 23, 42, 0.07);
            --shadow-lg: 0 8px 40px rgba(15, 23, 42, 0.12);
            --white:     #FFFFFF;
            --accent-rgb: {{ implode(',', sscanf($reseller->primary_color ?? '#2563EB', '#%02x%02x%02x')) }};
        }

        html { scroll-behavior: smooth; }
        body {
            font-family: 'Syne', sans-serif;
            background: var(--bg);
            color: var(--navy);
            overflow-x: hidden;
            cursor: none;
        }
        body::before {
            content: '';
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 512 512' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E");
            opacity: 0.018; pointer-events: none; z-index: 9999;
        }

        /* CURSOR */
        .cursor { width:10px;height:10px;background:var(--accent);border-radius:50%;position:fixed;pointer-events:none;z-index:99999;transition:transform 0.1s;mix-blend-mode:multiply; }
        .cursor-ring { width:36px;height:36px;border:1.5px solid var(--accent);border-radius:50%;position:fixed;pointer-events:none;z-index:99998;transition:all 0.15s ease;opacity:0.35; }

        /* NAV */
        nav { position:fixed;top:0;left:0;width:100%;z-index:1000;padding:1.4rem 3rem;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid transparent;transition:all 0.4s; }
        nav.scrolled { background:rgba(245,247,255,0.94);backdrop-filter:blur(16px);border-color:var(--border);box-shadow:var(--shadow); }
        .logo { font-family:'Bebas Neue',sans-serif;font-size:1.9rem;letter-spacing:3px;color:var(--navy);text-decoration:none; }
        .logo span { color:var(--accent); }
        .nav-links { display:flex;gap:2.5rem;list-style:none; }
        .nav-links a { color:var(--muted);text-decoration:none;font-size:0.85rem;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;transition:color 0.3s; }
        .nav-links a:hover { color:var(--navy); }
        .nav-btns { display:flex;gap:1rem;align-items:center; }
        .btn { font-family:'Syne',sans-serif;font-weight:700;font-size:0.8rem;letter-spacing:1.5px;text-transform:uppercase;padding:0.7rem 1.8rem;border-radius:4px;text-decoration:none;cursor:none;border:none;display:inline-block;transition:all 0.3s; }
        .btn-ghost { color:var(--muted);background:transparent;border:1px solid rgba(100,116,139,0.3); }
        .btn-ghost:hover { color:var(--navy);border-color:var(--navy); }
        .btn-solid { background:var(--accent);color:#fff;box-shadow:0 4px 18px rgba(var(--accent-rgb),0.28); }
        .btn-solid:hover { filter:brightness(1.1);transform:translateY(-2px);box-shadow:0 6px 28px rgba(var(--accent-rgb),0.38); }
        .btn-large { padding:1rem 2.5rem;font-size:0.9rem; }

        /* HERO */
        .hero { min-height:100vh;display:grid;grid-template-columns:1fr 1fr;align-items:center;padding:9rem 3rem 5rem;position:relative;overflow:hidden;gap:4rem; }
        .hero::after { content:'';position:absolute;inset:0;background-image:radial-gradient(circle,rgba(var(--accent-rgb),0.12) 1px,transparent 1px);background-size:36px 36px;pointer-events:none;mask-image:radial-gradient(ellipse 80% 80% at 50% 50%,black 40%,transparent 100%); }
        .hero-glow { position:absolute;width:800px;height:800px;background:radial-gradient(circle,rgba(var(--accent-rgb),0.09) 0%,transparent 70%);top:-200px;right:-200px;pointer-events:none; }
        .hero-glow-2 { position:absolute;width:600px;height:600px;background:radial-gradient(circle,rgba(245,158,11,0.05) 0%,transparent 70%);bottom:0;left:10%;pointer-events:none; }
        .hero-left { display:flex;flex-direction:column;justify-content:center; }
        .hero-eyebrow { font-family:'DM Mono',monospace;font-size:0.75rem;color:var(--accent);letter-spacing:3px;text-transform:uppercase;margin-bottom:1.5rem;display:flex;align-items:center;gap:1rem;opacity:0;animation:rise 0.8s ease forwards 0.3s; }
        .hero-eyebrow::before { content:'';display:inline-block;width:40px;height:1.5px;background:var(--accent); }
        .hero h1 { font-family:'Bebas Neue',sans-serif;font-size:clamp(5rem,8vw,9rem);line-height:0.92;letter-spacing:-1px;color:var(--navy);opacity:0;animation:rise 1s ease forwards 0.5s; }
        .hero h1 .outline { -webkit-text-stroke:2px rgba(15,23,42,0.2);color:transparent; }
        .hero h1 .blue { color:var(--accent); }
        .hero-desc { color:var(--muted);font-size:1.05rem;line-height:1.75;font-weight:400;margin-top:1.8rem;max-width:420px;opacity:0;animation:rise 1s ease forwards 0.7s; }
        .hero-actions { display:flex;gap:1rem;align-items:center;margin-top:2.5rem;opacity:0;animation:rise 1s ease forwards 0.9s; }

        /* HERO RIGHT */
        .hero-right { position:relative;display:flex;align-items:center;justify-content:center;opacity:0;animation:rise 1s ease forwards 0.6s; }
        .social-panel { position:relative;width:100%;max-width:420px; }
        .social-cards-scroll { max-height:340px;overflow:hidden;position:relative;border-radius:14px; }
        .social-cards-track { display:flex;flex-direction:column;gap:0.75rem;animation:scrollCards 22s linear infinite; }
        .social-cards-track:hover { animation-play-state:paused; }
        @keyframes scrollCards { 0%{transform:translateY(0)} 100%{transform:translateY(-50%)} }
        .soc-card { background:var(--white);border:1px solid var(--border-2);border-radius:14px;padding:0.9rem 1.1rem;display:flex;align-items:center;gap:0.9rem;box-shadow:var(--shadow);flex-shrink:0;transition:border-color 0.3s,box-shadow 0.3s; }
        .soc-card:hover { border-color:var(--electric);box-shadow:var(--shadow-lg); }
        .soc-card-icon { width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0; }
        .ig-bg{background:linear-gradient(135deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888)}.tt-bg{background:#111}.yt-bg{background:#FF0000}.tw-bg{background:#1DA1F2}.fb-bg{background:#1877F2}.sp-bg{background:#1DB954}.tg-bg{background:#0088cc}.res-bg{background:#2563EB}.dc-bg{background:#7C3AED}.isp-bg{background:#0D9488}.mob-bg{background:#EA580C}
        .soc-card-info { flex:1;min-width:0; }
        .soc-card-name { font-size:0.82rem;font-weight:700;color:var(--navy);margin-bottom:0.1rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
        .soc-card-handle { font-family:'DM Mono',monospace;font-size:0.62rem;color:var(--soft);letter-spacing:0.5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
        .soc-card-right { text-align:right;flex-shrink:0;display:flex;flex-direction:column;align-items:flex-end;gap:0.2rem; }
        .soc-card-count { font-family:'Bebas Neue',sans-serif;font-size:1.4rem;line-height:1;color:var(--navy); }
        .soc-card-count.green{color:#059669}.soc-card-count.gold{color:var(--gold)}
        .soc-card-metric { font-family:'DM Mono',monospace;font-size:0.58rem;color:var(--soft);text-transform:uppercase;letter-spacing:1px; }
        .soc-card-country { font-family:'DM Mono',monospace;font-size:0.55rem;color:#059669;letter-spacing:0.5px;display:flex;align-items:center;gap:0.3rem; }
        .soc-card-country::before { content:'▲';font-size:0.45rem; }
        .social-cards-scroll::before,.social-cards-scroll::after { content:'';position:absolute;left:0;right:0;height:50px;z-index:2;pointer-events:none; }
        .social-cards-scroll::before{top:0;background:linear-gradient(to bottom,var(--bg),transparent)}
        .social-cards-scroll::after{bottom:0;background:linear-gradient(to top,var(--bg),transparent)}

        .live-badge { position:absolute;top:-14px;right:0px;background:linear-gradient(135deg,#059669,#047857);color:white;font-family:'DM Mono',monospace;font-size:0.6rem;letter-spacing:1.5px;text-transform:uppercase;padding:0.3rem 0.8rem;border-radius:20px;display:flex;align-items:center;gap:0.4rem;box-shadow:0 4px 14px rgba(5,150,105,0.35);z-index:3; }
        .live-dot { width:6px;height:6px;background:white;border-radius:50%;animation:blink 1.2s ease-in-out infinite; }
        @keyframes blink { 0%,100%{opacity:1}50%{opacity:0.3} }

        .activity-feed { margin-top:1rem;background:var(--white);border:1px solid var(--border-2);border-radius:12px;overflow:hidden;box-shadow:var(--shadow); }
        .activity-feed-header { padding:0.6rem 1rem;border-bottom:1px solid rgba(0,0,0,0.06);display:flex;align-items:center;justify-content:space-between;background:var(--bg-2); }
        .activity-feed-header span { font-family:'DM Mono',monospace;font-size:0.6rem;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted); }
        .activity-feed-header .green-dot { width:6px;height:6px;background:#059669;border-radius:50%;box-shadow:0 0 6px #059669;animation:blink 1.5s ease-in-out infinite; }
        .activity-items { max-height:115px;overflow:hidden;position:relative; }
        .activity-track { display:flex;flex-direction:column; }
        .activity-item { padding:0.55rem 1rem;display:flex;align-items:center;gap:0.6rem;border-bottom:1px solid rgba(0,0,0,0.04);animation:slideInActivity 0.4s ease; }
        @keyframes slideInActivity { from{opacity:0;transform:translateX(-10px)}to{opacity:1;transform:translateX(0)} }
        .activity-icon { width:26px;height:26px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:0.7rem;flex-shrink:0; }
        .activity-text { flex:1;font-size:0.7rem;color:var(--muted);line-height:1.3; }
        .activity-text strong{color:var(--navy);font-weight:700}.activity-text .hl{color:#059669;font-weight:700}
        .activity-time { font-family:'DM Mono',monospace;font-size:0.55rem;color:var(--soft);flex-shrink:0; }

        .counter-bar { margin-top:0.75rem;background:var(--white);border:1px solid var(--border-2);border-radius:10px;padding:0.8rem 1.1rem;display:flex;align-items:center;justify-content:space-between;box-shadow:var(--shadow); }

        .scroll-hint { position:absolute;bottom:2.5rem;left:50%;transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:0.5rem;opacity:0;animation:rise 1s ease forwards 1.2s; }
        .scroll-hint span { font-family:'DM Mono',monospace;font-size:0.65rem;letter-spacing:2px;color:var(--soft);text-transform:uppercase; }
        .scroll-line { width:1px;height:50px;background:linear-gradient(to bottom,var(--accent),transparent);animation:pulse-line 2s ease-in-out infinite; }
        @keyframes pulse-line { 0%,100%{opacity:0.3}50%{opacity:1} }

        /* TICKER */
        .ticker-wrap { border-top:1px solid var(--border-2);border-bottom:1px solid var(--border-2);background:var(--white);overflow:hidden;padding:0.9rem 0; }
        .ticker { display:flex;width:max-content;animation:ticker 30s linear infinite; }
        .ticker-item { display:flex;align-items:center;gap:1rem;padding:0 2rem;font-family:'DM Mono',monospace;font-size:0.75rem;letter-spacing:1px;color:var(--soft);text-transform:uppercase;white-space:nowrap; }
        .ticker-item .dot { color:var(--accent);font-size:0.5rem; }
        @keyframes ticker { from{transform:translateX(0)}to{transform:translateX(-50%)} }

        /* STATS */
        .stats-section { padding:6rem 3rem;max-width:1400px;margin:0 auto;display:grid;grid-template-columns:repeat(3,1fr);gap:0; }
        .stat-item { padding:3rem;border-right:1px solid var(--border-2);position:relative; }
        .stat-item:last-child{border-right:none}
        .stat-num { font-family:'Bebas Neue',sans-serif;font-size:5rem;line-height:1;color:var(--navy);margin-bottom:0.5rem; }
        .stat-num span { color:var(--accent); }
        .stat-label { font-size:0.8rem;letter-spacing:2px;text-transform:uppercase;color:var(--muted);font-weight:600; }
        .stat-item::before { content:'';position:absolute;top:0;left:3rem;right:3rem;height:1px;background:var(--border-2); }

        /* FEATURES */
        .features { padding:6rem 3rem;background:var(--white);border-top:1px solid var(--border-2);border-bottom:1px solid var(--border-2); }
        .features-inner { max-width:1400px;margin:0 auto; }
        .section-tag { font-family:'DM Mono',monospace;font-size:0.7rem;letter-spacing:3px;text-transform:uppercase;color:var(--accent);margin-bottom:3rem;display:flex;align-items:center;gap:1rem; }
        .section-tag::before { content:'';display:inline-block;width:30px;height:1.5px;background:var(--accent); }
        .features-layout { display:grid;grid-template-columns:1fr 1fr;gap:1px;background:var(--border-2);border:1px solid var(--border-2);border-radius:8px;overflow:hidden; }
        .feat-card { background:var(--white);padding:3rem;transition:background 0.3s;position:relative;overflow:hidden; }
        .feat-card:hover{background:var(--bg)}
        .feat-card::before { content:'';position:absolute;top:0;left:0;width:3px;height:0;background:var(--accent);transition:height 0.4s; }
        .feat-card:hover::before{height:100%}
        .feat-num { font-family:'DM Mono',monospace;font-size:0.7rem;color:var(--soft);letter-spacing:2px;margin-bottom:1.5rem; }
        .feat-icon { font-size:1.5rem;color:var(--accent);margin-bottom:1rem; }
        .feat-card h3 { font-size:1.2rem;font-weight:800;margin-bottom:0.8rem;color:var(--navy); }
        .feat-card p { color:var(--muted);font-size:0.92rem;line-height:1.65;font-weight:400; }

        /* PLATFORMS */
        .platforms-section { padding:6rem 3rem;max-width:1400px;margin:0 auto; }
        .platforms-header { display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:4rem;border-bottom:1px solid var(--border-2);padding-bottom:2rem; }
        .platforms-header h2 { font-family:'Bebas Neue',sans-serif;font-size:clamp(3rem,6vw,5rem);line-height:1;color:var(--navy); }
        .platforms-header p { color:var(--muted);font-size:0.9rem;max-width:300px;text-align:right;line-height:1.6; }
        .platforms-grid { display:grid;grid-template-columns:repeat(8,1fr);gap:1px;background:var(--border-2);border:1px solid var(--border-2);border-radius:8px;overflow:hidden; }
        .platform-cell { background:var(--white);padding:2rem 1rem;display:flex;flex-direction:column;align-items:center;gap:0.6rem;transition:all 0.3s;text-decoration:none; }
        .platform-cell:hover{background:var(--bg-2)}
        .platform-cell i { font-size:1.6rem;color:var(--soft);transition:color 0.3s; }
        .platform-cell:hover i{color:var(--accent)}
        .platform-cell span { font-size:0.65rem;letter-spacing:1px;text-transform:uppercase;color:var(--soft);font-weight:600;font-family:'DM Mono',monospace; }

        /* CTA */
        .cta-section { padding:8rem 3rem;text-align:center;position:relative;overflow:hidden;background:var(--navy); }
        .cta-section::before { content:'';position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:900px;height:600px;background:radial-gradient(ellipse,rgba(var(--accent-rgb),0.18) 0%,transparent 70%);pointer-events:none; }
        .cta-section h2 { font-family:'Bebas Neue',sans-serif;font-size:clamp(4rem,10vw,9rem);line-height:0.9;letter-spacing:-1px;margin-bottom:2rem;color:#fff; }
        .cta-section h2 .outline { -webkit-text-stroke:1px rgba(255,255,255,0.25);color:transparent; }
        .cta-section p { color:#94A3B8;font-size:1rem;max-width:450px;margin:0 auto 3rem;line-height:1.7; }

        /* FOOTER */
        footer { background:var(--navy-2);border-top:1px solid rgba(255,255,255,0.07);padding:4rem 3rem 2rem; }
        .footer-top { max-width:1400px;margin:0 auto;display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:4rem;padding-bottom:3rem;border-bottom:1px solid rgba(255,255,255,0.07); }
        .footer-brand .logo-text { font-family:'Bebas Neue',sans-serif;font-size:2rem;letter-spacing:3px;margin-bottom:1rem;display:block;color:#fff; }
        .footer-brand .logo-text span{color:var(--electric)}
        .footer-brand p { color:#64748B;font-size:0.88rem;line-height:1.65;max-width:280px;margin-bottom:1.5rem; }
        .footer-socials{display:flex;gap:0.6rem}
        .soc-btn { width:38px;height:38px;border:1px solid rgba(255,255,255,0.1);border-radius:4px;display:flex;align-items:center;justify-content:center;color:#64748B;text-decoration:none;font-size:0.9rem;transition:all 0.3s; }
        .soc-btn:hover{border-color:var(--electric);color:var(--electric)}
        .footer-col h4 { font-size:0.7rem;letter-spacing:2.5px;text-transform:uppercase;color:#fff;margin-bottom:1.5rem;font-weight:700; }
        .footer-col ul{list-style:none}
        .footer-col li{margin-bottom:0.8rem}
        .footer-col a { color:#64748B;text-decoration:none;font-size:0.88rem;transition:color 0.3s; }
        .footer-col a:hover{color:#fff}
        .footer-col .contact-item { color:#64748B;font-size:0.85rem;display:flex;align-items:flex-start;gap:0.6rem;margin-bottom:0.8rem; }
        .footer-col .contact-item i{color:var(--electric);margin-top:2px;flex-shrink:0}
        .footer-bottom { max-width:1400px;margin:2rem auto 0;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem; }
        .footer-bottom p { color:#475569;font-size:0.78rem;font-family:'DM Mono',monospace; }

        @keyframes rise { from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)} }
        .reveal { opacity:0;transform:translateY(24px);transition:opacity 0.7s ease,transform 0.7s ease; }
        .reveal.visible{opacity:1;transform:translateY(0)}

        @media(max-width:900px){
            nav{padding:1.2rem 1.5rem}
            .nav-links{display:none}
            .hero{grid-template-columns:1fr;padding:7rem 1.5rem 4rem;gap:3rem}
            .hero h1{font-size:clamp(3.5rem,14vw,7rem)}
            .hero-right{display:none}
            .stats-section{grid-template-columns:1fr;padding:3rem 1.5rem}
            .stat-item{border-right:none;border-bottom:1px solid var(--border-2);padding:2rem 1.5rem}
            .features{padding:4rem 1.5rem}
            .features-layout{grid-template-columns:1fr}
            .platforms-section{padding:4rem 1.5rem}
            .platforms-header{flex-direction:column;align-items:flex-start;gap:1rem}
            .platforms-header p{text-align:left}
            .platforms-grid{grid-template-columns:repeat(4,1fr)}
            .cta-section{padding:5rem 1.5rem}
            footer{padding:3rem 1.5rem 2rem}
            .footer-top{grid-template-columns:1fr 1fr;gap:2rem}
            .footer-bottom{flex-direction:column;align-items:flex-start}
        }
        @media(max-width:500px){
            .platforms-grid{grid-template-columns:repeat(3,1fr)}
            .footer-top{grid-template-columns:1fr}
        }
    </style>
</head>
<body>

<div class="cursor" id="cursor"></div>
<div class="cursor-ring" id="cursorRing"></div>

<nav id="nav">
    @if($reseller->logo_path)
        <a href="/" class="logo">
            <img src="{{ asset($reseller->logo_path) }}" alt="{{ $reseller->panel_name }}" style="height:36px;width:auto;" />
        </a>
    @else
        @php
            $words = explode(' ', $reseller->panel_name);
            $first = $words[0] ?? '';
            $rest  = implode(' ', array_slice($words, 1));
        @endphp
        <a href="/" class="logo">{{ $first }}<span>{{ $rest ? ' '.$rest : '' }}</span></a>
    @endif

    <ul class="nav-links">
        <li><a href="#features">Features</a></li>
        <li><a href="#platforms">Platforms</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>
    <div class="nav-btns">
        <a href="{{ route('storefront.login') }}" class="btn btn-ghost">Login</a>
        <a href="{{ route('storefront.register') }}" class="btn btn-solid">Get Started</a>
    </div>
</nav>

<section class="hero">
    <div class="hero-glow"></div>
    <div class="hero-glow-2"></div>

    <div class="hero-left">
        <div class="hero-eyebrow">Premium Proxy Network</div>
        <h1>
            Power<br>
            <span class="outline">Your</span><br>
            <span class="blue">Connections</span>
        </h1>
        <p class="hero-desc">
            Residential, datacenter, ISP and mobile proxies from
            <strong>{{ $reseller->panel_name }}</strong>.
            Fast, reliable, and built for scraping, automation, and anonymity.
        </p>
        <div class="hero-actions">
            <a href="{{ route('storefront.register') }}" class="btn btn-solid btn-large">Get Proxies</a>
            <a href="#features" class="btn btn-ghost btn-large">See How</a>
        </div>
    </div>

    <div class="hero-right">
        <div class="social-panel">
            <div class="live-badge">
                <div class="live-dot"></div>
                Live Boosting
            </div>
            <div class="social-cards-scroll">
                <div class="social-cards-track" id="cardsTrack"></div>
            </div>
            <div class="activity-feed">
                <div class="activity-feed-header">
                    <span>Live Activity</span>
                    <div class="green-dot"></div>
                </div>
                <div class="activity-items">
                    <div class="activity-track" id="activityTrack"></div>
                </div>
            </div>
            <div class="counter-bar">
                <div style="display:flex;align-items:center;gap:0.6rem;">
                    <div style="width:8px;height:8px;background:#059669;border-radius:50%;box-shadow:0 0 8px rgba(5,150,105,0.5);"></div>
                    <span style="font-family:'DM Mono',monospace;font-size:0.68rem;letter-spacing:1px;color:var(--muted);text-transform:uppercase;">Orders completed today</span>
                </div>
                <span id="order-counter" style="font-family:'Bebas Neue',sans-serif;font-size:1.4rem;color:#059669;">2,247</span>
            </div>
        </div>
    </div>

    <div class="scroll-hint">
        <span>Scroll</span>
        <div class="scroll-line"></div>
    </div>
</section>

<div class="ticker-wrap">
    <div class="ticker">
        <div class="ticker-item"><span class="dot">●</span> Residential Proxies</div>
        <div class="ticker-item"><span class="dot">●</span> Datacenter Proxies</div>
        <div class="ticker-item"><span class="dot">●</span> ISP Proxies</div>
        <div class="ticker-item"><span class="dot">●</span> Mobile Proxies</div>
        <div class="ticker-item"><span class="dot">●</span> 190+ Countries</div>
        <div class="ticker-item"><span class="dot">●</span> HTTP &amp; SOCKS5</div>
        <div class="ticker-item"><span class="dot">●</span> Rotating Sessions</div>
        <div class="ticker-item"><span class="dot">●</span> 99.9% Uptime</div>
        <div class="ticker-item"><span class="dot">●</span> Residential Proxies</div>
        <div class="ticker-item"><span class="dot">●</span> Datacenter Proxies</div>
        <div class="ticker-item"><span class="dot">●</span> ISP Proxies</div>
        <div class="ticker-item"><span class="dot">●</span> Mobile Proxies</div>
        <div class="ticker-item"><span class="dot">●</span> 190+ Countries</div>
        <div class="ticker-item"><span class="dot">●</span> HTTP &amp; SOCKS5</div>
        <div class="ticker-item"><span class="dot">●</span> Rotating Sessions</div>
        <div class="ticker-item"><span class="dot">●</span> 99.9% Uptime</div>
    </div>
</div>

<div class="stats-section reveal">
    <div class="stat-item">
        <div class="stat-num">50K<span>+</span></div>
        <div class="stat-label">Happy Customers</div>
    </div>
    <div class="stat-item">
        <div class="stat-num">10M<span>+</span></div>
        <div class="stat-label">Orders Delivered</div>
    </div>
    <div class="stat-item">
        <div class="stat-num">99.9<span>%</span></div>
        <div class="stat-label">Success Rate</div>
    </div>
</div>

<section id="features" class="features">
    <div class="features-inner">
        <div class="section-tag reveal">Why We're Different</div>
        <div class="features-layout">
            <div class="feat-card reveal">
                <div class="feat-num">01</div>
                <div class="feat-icon"><i class="fas fa-bolt"></i></div>
                <h3>Instant Provisioning</h3>
                <p>Orders are provisioned within minutes. Our automated pipeline delivers your proxy credentials without you waiting around.</p>
            </div>
            <div class="feat-card reveal">
                <div class="feat-num">02</div>
                <div class="feat-icon"><i class="fas fa-shield-alt"></i></div>
                <h3>Clean &amp; Unshared IPs</h3>
                <p>No blacklisted ranges, no overused IPs. Every proxy pool is sourced from vetted, ethically-obtained networks.</p>
            </div>
            <div class="feat-card reveal">
                <div class="feat-num">03</div>
                <div class="feat-icon"><i class="fas fa-gauge-high"></i></div>
                <h3>High Uptime, Low Latency</h3>
                <p>99.9% uptime across the network, with fast response times whatever you're using proxies for.</p>
            </div>
            <div class="feat-card reveal">
                <div class="feat-num">04</div>
                <div class="feat-icon"><i class="fas fa-headset"></i></div>
                <h3>Support, Always On</h3>
                <p>Got a question at 2am? Our team responds fast — day, night, weekends. No ticket queue runaround.</p>
            </div>
            <div class="feat-card reveal">
                <div class="feat-num">05</div>
                <div class="feat-icon"><i class="fas fa-tag"></i></div>
                <h3>Prices That Make Sense</h3>
                <p>Competitive rates with bulk discounts. Growing your presence shouldn't drain your budget.</p>
            </div>
            <div class="feat-card reveal">
                <div class="feat-num">06</div>
                <div class="feat-icon"><i class="fas fa-undo"></i></div>
                <h3>Refund Guarantee</h3>
                <p>Something goes wrong? We make it right. Straightforward refund policy, no hoops.</p>
            </div>
        </div>
    </div>
</section>

<section id="platforms" class="platforms-section">
    <div class="platforms-header reveal">
        <h2>190+ Countries.<br>One Dashboard.</h2>
        <p>Every proxy type, sourced from top-tier suppliers, covering nearly every country on earth.</p>
    </div>
    <div class="platforms-grid reveal">
        <div class="platform-cell"><i class="fas fa-house-user"></i><span>Residential</span></div>
        <div class="platform-cell"><i class="fas fa-server"></i><span>Datacenter</span></div>
        <div class="platform-cell"><i class="fas fa-network-wired"></i><span>ISP</span></div>
        <div class="platform-cell"><i class="fas fa-mobile-screen"></i><span>Mobile</span></div>
        <div class="platform-cell"><span style="font-size:1.6rem;">🇺🇸</span><span>USA</span></div>
        <div class="platform-cell"><span style="font-size:1.6rem;">🇬🇧</span><span>UK</span></div>
        <div class="platform-cell"><span style="font-size:1.6rem;">🇩🇪</span><span>Germany</span></div>
        <div class="platform-cell"><span style="font-size:1.6rem;">🇳🇬</span><span>Nigeria</span></div>
        <div class="platform-cell"><span style="font-size:1.6rem;">🇮🇳</span><span>India</span></div>
        <div class="platform-cell"><span style="font-size:1.6rem;">🇧🇷</span><span>Brazil</span></div>
        <div class="platform-cell"><span style="font-size:1.6rem;">🇿🇦</span><span>South Africa</span></div>
        <div class="platform-cell"><span style="font-size:1.6rem;">🇦🇪</span><span>UAE</span></div>
        <div class="platform-cell"><span style="font-size:1.6rem;">🇨🇦</span><span>Canada</span></div>
        <div class="platform-cell"><i class="fas fa-earth-americas"></i><span>+ many more</span></div>
    </div>
</section>

<section class="cta-section">
    <h2 class="reveal">
        Go<br>
        <span class="outline">Anonymous</span><br>
        Today
    </h2>
    <p class="reveal">Join thousands of developers, marketers, and businesses running on {{ $reseller->panel_name }}.</p>
    <a href="{{ route('storefront.register') }}" class="btn btn-solid btn-large reveal">Create Free Account</a>
</section>

<footer id="contact">
    <div class="footer-top">
        <div class="footer-brand">
            @if($reseller->logo_path)
                <img src="{{ asset($reseller->logo_path) }}" alt="{{ $reseller->panel_name }}" style="height:40px;width:auto;margin-bottom:1rem;display:block;" />
            @else
                <span class="logo-text">{{ $first }}<span>{{ $rest ? ' '.$rest : '' }}</span></span>
            @endif
            <p>Your trusted proxy provider. Fast, reliable, and built for results.</p>
        </div>
        <div class="footer-col">
            <h4>Navigate</h4>
            <ul>
                <li><a href="{{ route('storefront.login') }}">Login</a></li>
                <li><a href="{{ route('storefront.register') }}">Sign Up</a></li>
                <li><a href="#features">Features</a></li>
                <li><a href="#platforms">Platforms</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Legal</h4>
            <ul>
                <li><a href="#">FAQ</a></li>
                <li><a href="#">Terms of Use</a></li>
                <li><a href="#">Refund Policy</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Contact</h4>
            @if($reseller->support_email)
                <div class="contact-item">
                    <i class="fas fa-envelope"></i> {{ $reseller->support_email }}
                </div>
            @endif
        </div>
    </div>
    <div class="footer-bottom">
        <p>© {{ date('Y') }} {{ $reseller->panel_name }}. All rights reserved.</p>
        <p>Powered by ProxWorld.com</p>
    </div>
</footer>

<script>
const socialCards = [
    { icon:'fas fa-house-user',bg:'res-bg',name:'Tolani Adewale',handle:'Residential Proxy',metric:'Bandwidth',count:'50 GB',countColor:'green',gained:'+50GB',from:'🇳🇬 Nigeria'},
    { icon:'fas fa-server',bg:'dc-bg',name:'Marcus Chen',handle:'Datacenter Proxy',metric:'IPs',count:'100',countColor:'green',gained:'+100 IPs',from:'🇺🇸 USA'},
    { icon:'fas fa-network-wired',bg:'isp-bg',name:'Amira Hassan',handle:'ISP Proxy',metric:'IPs',count:'25',countColor:'green',gained:'+25 IPs',from:'🇦🇪 UAE'},
    { icon:'fas fa-mobile-screen',bg:'mob-bg',name:'Jake Morrison',handle:'Mobile Proxy',metric:'Bandwidth',count:'10 GB',countColor:'green',gained:'+10GB',from:'🇬🇧 UK'},
    { icon:'fas fa-house-user',bg:'res-bg',name:'Chidinma Obi',handle:'Residential Proxy',metric:'Bandwidth',count:'200 GB',countColor:'gold',gained:'+200GB',from:'🇳🇬 Nigeria'},
    { icon:'fas fa-server',bg:'dc-bg',name:'Sofia Reyes',handle:'Datacenter Proxy',metric:'IPs',count:'500',countColor:'green',gained:'+500 IPs',from:'🇲🇽 Mexico'},
    { icon:'fas fa-network-wired',bg:'isp-bg',name:'David Okonkwo',handle:'ISP Proxy',metric:'IPs',count:'50',countColor:'green',gained:'+50 IPs',from:'🇬🇭 Ghana'},
    { icon:'fas fa-mobile-screen',bg:'mob-bg',name:'Luca Ferri',handle:'Mobile Proxy',metric:'Bandwidth',count:'20 GB',countColor:'green',gained:'+20GB',from:'🇮🇹 Italy'},
    { icon:'fas fa-house-user',bg:'res-bg',name:'Priya Kapoor',handle:'Residential Proxy',metric:'Bandwidth',count:'75 GB',countColor:'green',gained:'+75GB',from:'🇮🇳 India'},
    { icon:'fas fa-server',bg:'dc-bg',name:'Kwame Asante',handle:'Datacenter Proxy',metric:'IPs',count:'1,000',countColor:'gold',gained:'+1K IPs',from:'🇬🇭 Ghana'},
];

function shuffle(arr){const a=[...arr];for(let i=a.length-1;i>0;i--){const j=Math.floor(Math.random()*(i+1));[a[i],a[j]]=[a[j],a[i]]}return a}
function buildCard(c){return`<div class="soc-card"><div class="soc-card-icon ${c.bg}"><i class="${c.icon}" style="color:#fff"></i></div><div class="soc-card-info"><div class="soc-card-name">${c.name}</div><div class="soc-card-handle">${c.handle}</div></div><div class="soc-card-right"><div class="soc-card-count ${c.countColor}">${c.count}</div><div class="soc-card-metric">${c.metric}</div><div class="soc-card-country">${c.gained} added</div></div></div>`}

const track = document.getElementById('cardsTrack');
const doubled = [...shuffle(socialCards),...shuffle(socialCards)];
track.innerHTML = doubled.map(buildCard).join('');

const activityEvents = [
    { icon:'fas fa-house-user',bg:'#2563EB',text:'<strong>Tolani A.</strong> just provisioned <span class="hl">+50GB residential</span> from 🇳🇬 Nigeria'},
    { icon:'fas fa-server',bg:'#7C3AED',text:'<strong>Sofia R.</strong> got <span class="hl">+500 datacenter IPs</span> from 🇲🇽 Mexico'},
    { icon:'fas fa-network-wired',bg:'#0D9488',text:'<strong>KwameBuilds</strong> got <span class="hl">+50 ISP IPs</span> from 🇬🇧 UK'},
    { icon:'fas fa-mobile-screen',bg:'#EA580C',text:'<strong>Ryan B.</strong> activated <span class="hl">+10GB mobile</span> from 🇺🇸 USA'},
    { icon:'fas fa-house-user',bg:'#2563EB',text:'<strong>Chidinma O.</strong> received <span class="hl">+200GB residential</span> instantly'},
    { icon:'fas fa-network-wired',bg:'#0D9488',text:'<strong>Fatima A.</strong> added <span class="hl">+25 ISP IPs</span>'},
    { icon:'fas fa-server',bg:'#7C3AED',text:'<strong>Luca F.</strong> got <span class="hl">+1K datacenter IPs</span> from 🇮🇹 Italy'},
];

let actIdx=0;
const actTrack=document.getElementById('activityTrack');
function timeAgo(){return['just now','1m ago','2m ago','3m ago','5m ago'][Math.floor(Math.random()*5)]}
function addActivity(){
    const e=activityEvents[actIdx%activityEvents.length];actIdx++;
    const item=document.createElement('div');item.className='activity-item';
    item.innerHTML=`<div class="activity-icon" style="background:${e.bg}18;border:1px solid ${e.bg}30"><i class="${e.icon}" style="color:${e.bg}"></i></div><div class="activity-text">${e.text}</div><div class="activity-time">${timeAgo()}</div>`;
    actTrack.insertBefore(item,actTrack.firstChild);
    while(actTrack.children.length>4)actTrack.removeChild(actTrack.lastChild);
}
addActivity();addActivity();addActivity();
setInterval(addActivity,2600);

const counterEl=document.getElementById('order-counter');
let count=2100+Math.floor(Math.random()*400);
counterEl.textContent=count.toLocaleString();
setInterval(()=>{if(Math.random()>0.35){count+=Math.floor(Math.random()*4)+1;counterEl.textContent=count.toLocaleString()}},2800);

const cursor=document.getElementById('cursor'),ring=document.getElementById('cursorRing');
let mx=0,my=0,rx=0,ry=0;
document.addEventListener('mousemove',e=>{mx=e.clientX;my=e.clientY;cursor.style.left=mx-5+'px';cursor.style.top=my-5+'px'});
function animRing(){rx+=(mx-rx)*0.12;ry+=(my-ry)*0.12;ring.style.left=rx-18+'px';ring.style.top=ry-18+'px';requestAnimationFrame(animRing)}
animRing();
document.querySelectorAll('a,button').forEach(el=>{
    el.addEventListener('mouseenter',()=>{cursor.style.transform='scale(2)';ring.style.transform='scale(1.4)';ring.style.opacity='0.6'});
    el.addEventListener('mouseleave',()=>{cursor.style.transform='scale(1)';ring.style.transform='scale(1)';ring.style.opacity='0.35'});
});

const nav=document.getElementById('nav');
window.addEventListener('scroll',()=>nav.classList.toggle('scrolled',window.scrollY>60));

const reveals=document.querySelectorAll('.reveal');
const observer=new IntersectionObserver((entries)=>{
    entries.forEach((entry,i)=>{if(entry.isIntersecting){setTimeout(()=>entry.target.classList.add('visible'),i*80);observer.unobserve(entry.target)}});
},{threshold:0.1});
reveals.forEach(el=>observer.observe(el));

document.querySelectorAll('a[href^="#"]').forEach(a=>{
    a.addEventListener('click',e=>{const t=document.querySelector(a.getAttribute('href'));if(t){e.preventDefault();t.scrollIntoView({behavior:'smooth',block:'start'})}});
});
</script>
</body>
</html>