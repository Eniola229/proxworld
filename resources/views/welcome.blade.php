@extends('layouts.app')

@section('title', 'ProxWorld — Premium Residential, Datacenter, ISP & Mobile Proxies')
@section('description', 'Buy fast, reliable proxies with ProxWorld. Residential, datacenter, ISP, and mobile proxies across 190+ countries — built for scraping, automation, and anonymity.')
@section('keywords', 'buy proxies, residential proxies, datacenter proxies, ISP proxies, mobile proxies, proxy provider Nigeria, buy proxies online, rotating proxies, SOCKS5 proxies')
@section('canonical', route('welcome'))

@push('styles')
<style>
    /* FLAG IMAGES */
    .flag { width: 20px; height: 15px; object-fit: cover; border-radius: 2px; vertical-align: middle; box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.12); }
    .flag-lg { width: 32px; height: 24px; border-radius: 3px; display: block; }

    /* HERO */
    .hero { padding: 6.5rem 1.5rem 1.5rem; }
    .hero-card {
        position: relative; max-width: 1400px; margin: 0 auto; min-height: calc(100vh - 8rem);
        border-radius: 28px; overflow: hidden;
        background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 55%, #60A5FA 100%);
        box-shadow: var(--shadow-lg);
        display: flex; align-items: center; justify-content: center; text-align: center; padding: 5rem 2rem;
    }
    .hero-card::after {
        content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background-image: radial-gradient(circle, rgba(255, 255, 255, 0.14) 1px, transparent 1px);
        background-size: 36px 36px; pointer-events: none;
        mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 30%, transparent 100%);
        -webkit-mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 30%, transparent 100%);
    }
    .hero-glow {
        position: absolute; width: 700px; height: 700px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.16) 0%, transparent 70%);
        top: -280px; left: 50%; transform: translateX(-50%); pointer-events: none;
    }
    .hero-content { position: relative; z-index: 2; max-width: 900px; display: flex; flex-direction: column; align-items: center; }
    .hero-pill {
        display: inline-flex; align-items: center; gap: 0.7rem;
        font-family: 'DM Mono', monospace; font-size: 0.7rem; letter-spacing: 1.5px; text-transform: uppercase; color: #fff;
        background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.28);
        -webkit-backdrop-filter: blur(8px); backdrop-filter: blur(8px);
        padding: 0.6rem 1.3rem; border-radius: 40px; margin-bottom: 2rem;
        opacity: 0; animation: rise 0.8s ease forwards 0.2s;
    }
    .hero-pill .dot { width: 7px; height: 7px; border-radius: 50%; background: #4ADE80; box-shadow: 0 0 8px rgba(74, 222, 128, 0.8); animation: blink 1.4s ease-in-out infinite; }
    .hero-pill .sep { opacity: 0.4; }
    .hero h1 {
        font-family: 'Bebas Neue', sans-serif; font-weight: 400; font-size: clamp(3.8rem, 9vw, 8.5rem);
        line-height: 0.95; letter-spacing: -1px; color: #fff; opacity: 0; animation: rise 1s ease forwards 0.4s;
    }
    .hero h1 .outline { -webkit-text-stroke: 2px rgba(255, 255, 255, 0.55); color: transparent; }
    .hero-desc { color: rgba(255, 255, 255, 0.82); font-size: 1.1rem; line-height: 1.75; font-weight: 400; margin-top: 1.8rem; max-width: 560px; opacity: 0; animation: rise 1s ease forwards 0.6s; }
    .hero-actions { display: flex; gap: 1rem; align-items: center; justify-content: center; flex-wrap: wrap; margin-top: 2.5rem; opacity: 0; animation: rise 1s ease forwards 0.8s; }
    .btn-white { background: #fff; color: var(--accent); box-shadow: 0 6px 24px rgba(15, 23, 42, 0.22); }
    .btn-white:hover { background: #F1F5FF; transform: translateY(-2px); box-shadow: 0 10px 32px rgba(15, 23, 42, 0.3); }
    .btn-outline-white { background: rgba(255, 255, 255, 0.08); color: #fff; border: 1px solid rgba(255, 255, 255, 0.45); }
    .btn-outline-white:hover { background: rgba(255, 255, 255, 0.18); border-color: #fff; }
    .hero-note { margin-top: 1.6rem; font-family: 'DM Mono', monospace; font-size: 0.7rem; letter-spacing: 1px; color: rgba(255, 255, 255, 0.65); opacity: 0; animation: rise 1s ease forwards 1s; }

    .hero-tile {
        position: absolute; z-index: 1; width: 76px; height: 76px; border-radius: 18px;
        background: rgba(255, 255, 255, 0.14); border: 1px solid rgba(255, 255, 255, 0.28);
        -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px);
        display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.6rem;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.18);
    }
    .hero-tile:nth-of-type(1) { top: 20%; left: 5%;  animation: orbit1 8s ease-in-out infinite; }
    .hero-tile:nth-of-type(2) { top: 50%; left: 14%; animation: orbit2 9s ease-in-out infinite; }
    .hero-tile:nth-of-type(3) { bottom: 8%; left: 4%; animation: orbit3 7s ease-in-out infinite; }
    .hero-tile:nth-of-type(4) { top: 20%; right: 5%; animation: orbit2 8s ease-in-out infinite 0.5s; }
    .hero-tile:nth-of-type(5) { top: 50%; right: 14%; animation: orbit1 9s ease-in-out infinite 1s; }
    .hero-tile:nth-of-type(6) { bottom: 8%; right: 4%; animation: orbit3 10s ease-in-out infinite 1.5s; }

    .hero-lines { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none; }
    .hero-lines path { fill: none; stroke: rgba(255, 255, 255, 0.35); stroke-width: 1.5; stroke-dasharray: 6 8; vector-effect: non-scaling-stroke; }

    @keyframes rise { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes orbit1 { 0%,100% { transform: translate(0,0) rotate(0deg); } 33% { transform: translate(8px,-10px) rotate(5deg); } 66% { transform: translate(-5px,8px) rotate(-3deg); } }
    @keyframes orbit2 { 0%,100% { transform: translate(0,0); } 50% { transform: translate(-10px,-8px); } }
    @keyframes orbit3 { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(6px,10px) scale(1.05); } }
    @keyframes blink { 0%,100% { opacity: 1; } 50% { opacity: 0.3; } }

    /* PROXY TYPE CARDS */
    .types-head { margin-top: 5rem; }
    .type-top { display: flex; justify-content: space-between; align-items: center; gap: 0.8rem; flex-wrap: wrap; margin-bottom: 1.5rem; }
    .type-price { font-family: 'DM Mono', monospace; font-size: 0.78rem; font-weight: 500; padding: 0.45rem 1rem; border-radius: 30px; display: inline-flex; align-items: center; gap: 0.6rem; }
    .type-price i { font-size: 0.9rem; }
    .type-unit { font-family: 'DM Mono', monospace; font-size: 0.65rem; letter-spacing: 1px; text-transform: uppercase; color: var(--muted); background: var(--bg-2); padding: 0.35rem 0.8rem; border-radius: 30px; }
    .tp-res { background: #DBEAFE; color: #1D4ED8; }
    .tp-dc  { background: #EDE9FE; color: #6D28D9; }
    .tp-isp { background: #CCFBF1; color: #0F766E; }
    .tp-mob { background: #FFEDD5; color: #C2410C; }

    /* TICKER */
    .ticker-wrap { border-top: 1px solid var(--border-2); border-bottom: 1px solid var(--border-2); background: var(--white); overflow: hidden; padding: 0.9rem 0; }
    .ticker { display: flex; width: max-content; animation: ticker 30s linear infinite; }
    .ticker-item { display: flex; align-items: center; gap: 1rem; padding: 0 2rem; font-family: 'DM Mono', monospace; font-size: 0.75rem; letter-spacing: 1px; color: var(--soft); text-transform: uppercase; white-space: nowrap; }
    .ticker-item .dot { color: var(--accent); font-size: 0.5rem; }
    @keyframes ticker { from { transform: translateX(0); } to { transform: translateX(-50%); } }

    /* STATS */
    .stats-section { padding: 6rem 3rem; max-width: 1400px; margin: 0 auto; display: grid; grid-template-columns: repeat(3, 1fr); gap: 0; }
    .stat-item { padding: 3rem; border-right: 1px solid var(--border-2); position: relative; }
    .stat-item:last-child { border-right: none; }
    .stat-num { font-family: 'Bebas Neue', sans-serif; font-size: 5rem; line-height: 1; color: var(--navy); margin-bottom: 0.5rem; }
    .stat-num span { color: var(--accent); }
    .stat-label { font-size: 0.8rem; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); font-weight: 600; }
    .stat-item::before { content: ''; position: absolute; top: 0; left: 3rem; right: 3rem; height: 1px; background: var(--border-2); }

    /* FEATURES */
    .features { padding: 6rem 3rem; background: var(--white); border-top: 1px solid var(--border-2); border-bottom: 1px solid var(--border-2); }
    .features-inner { max-width: 1400px; margin: 0 auto; }
    .section-tag { font-family: 'DM Mono', monospace; font-size: 0.7rem; letter-spacing: 3px; text-transform: uppercase; color: var(--accent); margin-bottom: 3rem; display: flex; align-items: center; gap: 1rem; }
    .section-tag::before { content: ''; display: inline-block; width: 30px; height: 1.5px; background: var(--accent); }
    .features-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: var(--border-2); border: 1px solid var(--border-2); border-radius: 8px; overflow: hidden; }
    .feat-card { background: var(--white); padding: 3rem; transition: background 0.3s; position: relative; overflow: hidden; }
    .feat-card:hover { background: var(--bg); }
    .feat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 3px; height: 0; background: var(--accent); transition: height 0.4s; }
    .feat-card:hover::before { height: 100%; }
    .feat-num { font-family: 'DM Mono', monospace; font-size: 0.7rem; color: var(--soft); letter-spacing: 2px; margin-bottom: 1.5rem; }
    .feat-icon { font-size: 1.5rem; color: var(--accent); margin-bottom: 1rem; }
    .feat-card h3 { font-size: 1.2rem; font-weight: 800; margin-bottom: 0.8rem; color: var(--navy); }
    .feat-card p { color: var(--muted); font-size: 0.92rem; line-height: 1.65; font-weight: 400; }

    /* PLATFORMS */
    .platforms-section { padding: 6rem 3rem; max-width: 1400px; margin: 0 auto; }
    .platforms-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 4rem; border-bottom: 1px solid var(--border-2); padding-bottom: 2rem; }
    .platforms-header h2 { font-family: 'Bebas Neue', sans-serif; font-weight: 400; font-size: clamp(3rem, 6vw, 5rem); line-height: 1; color: var(--navy); }
    .platforms-header p { color: var(--muted); font-size: 0.9rem; max-width: 300px; text-align: right; line-height: 1.6; }
    .platforms-grid { display: grid; grid-template-columns: repeat(8, 1fr); gap: 1px; background: var(--border-2); border: 1px solid var(--border-2); border-radius: 8px; overflow: hidden; }
    .platform-cell { background: var(--white); padding: 2rem 1rem; display: flex; flex-direction: column; align-items: center; gap: 0.6rem; transition: all 0.3s; text-decoration: none; }
    .platform-cell:hover { background: var(--bg-2); }
    .platform-cell i { font-size: 1.6rem; color: var(--soft); transition: color 0.3s; }
    .platform-cell:hover i { color: var(--accent); }
    .platform-cell span { font-size: 0.65rem; letter-spacing: 1px; text-transform: uppercase; color: var(--soft); font-weight: 600; font-family: 'DM Mono', monospace; }

    /* BLOG (home page) */
    .blog-section { padding: 6rem 3rem; background: var(--bg-2); border-top: 1px solid var(--border-2); }
    .blog-inner { max-width: 1400px; margin: 0 auto; }
    .blog-head { display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 2rem; margin-bottom: 3rem; padding-bottom: 2rem; border-bottom: 1px solid var(--border-2); }
    .blog-head h2 { font-family: 'Bebas Neue', sans-serif; font-weight: 400; font-size: clamp(3rem, 6vw, 5rem); line-height: 1; color: var(--navy); }
    .blog-head h2 .outline { -webkit-text-stroke: 2px rgba(15, 23, 42, 0.2); color: transparent; }
    .blog-head p { color: var(--muted); font-size: 0.92rem; max-width: 340px; text-align: right; line-height: 1.6; }
    .hb-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 1.5rem; }
    .hb-card { grid-column: span 2; background: var(--white); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: var(--shadow); text-decoration: none; color: inherit; display: flex; flex-direction: column; transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s; }
    .hb-card:nth-child(-n+2) { grid-column: span 3; }
    .hb-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); border-color: var(--electric); }
    .hb-img { width: 100%; aspect-ratio: 16/10; background: var(--bg-3) center/cover no-repeat; display: flex; align-items: center; justify-content: center; color: var(--soft); font-size: 2rem; }
    .hb-card:nth-child(-n+2) .hb-img { aspect-ratio: 16/8; }
    .hb-body { padding: 1.5rem; display: flex; flex-direction: column; gap: 0.6rem; flex: 1; }
    .hb-date { font-family: 'DM Mono', monospace; font-size: 0.68rem; color: var(--soft); letter-spacing: 1px; text-transform: uppercase; }
    .hb-title { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 1.1rem; line-height: 1.35; color: var(--navy); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .hb-card:nth-child(-n+2) .hb-title { font-size: 1.3rem; }
    .hb-excerpt { color: var(--muted); font-size: 0.88rem; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    .hb-more { margin-top: auto; padding-top: 0.5rem; font-family: 'DM Mono', monospace; font-size: 0.72rem; color: var(--accent); letter-spacing: 1px; text-transform: uppercase; }
    .blog-cta { text-align: center; margin-top: 3rem; }

    /* CTA */
    .cta-section { padding: 8rem 3rem; text-align: center; position: relative; overflow: hidden; background: var(--navy); }
    .cta-section::before { content: ''; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 900px; height: 600px; background: radial-gradient(ellipse, rgba(37,99,235,0.18) 0%, transparent 70%); pointer-events: none; }
    .cta-section h2 { font-family: 'Bebas Neue', sans-serif; font-weight: 400; font-size: clamp(4rem, 10vw, 9rem); line-height: 0.9; letter-spacing: -1px; margin-bottom: 2rem; color: #fff; position: relative; }
    .cta-section h2 .outline { -webkit-text-stroke: 1px rgba(255,255,255,0.25); color: transparent; }
    .cta-section p { color: #94A3B8; font-size: 1rem; max-width: 450px; margin: 0 auto 3rem; line-height: 1.7; position: relative; }
    .cta-section .btn { position: relative; }

    /* ─── RESPONSIVE ─── */
    @media (max-width: 1100px) {
        .hero-tile:nth-of-type(2), .hero-tile:nth-of-type(5) { display: none; }
    }
    @media (max-width: 900px) {
        .hero { padding: 5.5rem 1rem 1rem; }
        .hero-card { min-height: auto; border-radius: 20px; padding: 4rem 1.5rem; }
        .hero h1 { font-size: clamp(3.4rem, 15vw, 6rem); }
        .hero-tile, .hero-lines { display: none; }
        .stats-section { grid-template-columns: 1fr; padding: 3rem 1.5rem; }
        .stat-item { border-right: none; border-bottom: 1px solid var(--border-2); padding: 2rem 1.5rem; }
        .features { padding: 4rem 1.5rem; }
        .features-layout { grid-template-columns: 1fr; }
        .platforms-section { padding: 4rem 1.5rem; }
        .platforms-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
        .platforms-header p { text-align: left; }
        .platforms-grid { grid-template-columns: repeat(4, 1fr); }
        .blog-section { padding: 4rem 1.5rem; }
        .blog-head p { text-align: left; }
        .hb-grid { grid-template-columns: repeat(2, 1fr); }
        .hb-card, .hb-card:nth-child(-n+2) { grid-column: span 1; }
        .hb-card:last-child:nth-child(odd) { grid-column: span 2; }
        .hb-card:nth-child(-n+2) .hb-img { aspect-ratio: 16/10; }
        .hb-card:nth-child(-n+2) .hb-title { font-size: 1.1rem; }
        .cta-section { padding: 5rem 1.5rem; }
    }
    @media (max-width: 600px) {
        .hb-grid { grid-template-columns: 1fr; }
        .hb-card:last-child:nth-child(odd) { grid-column: span 1; }
        .hero-actions .btn-large { width: 100%; text-align: center; }
        .reseller-hdr-p { text-align: left !important; max-width: 100% !important; }
        .reseller-section { padding: 4rem 1.2rem !important; }
    }
    @media (max-width: 500px) {
        .platforms-grid { grid-template-columns: repeat(3, 1fr); }
    }

    /* ============ PROXWORLD ONBOARDING MODAL — SCOPED STYLES ============ */
    .prox-onb-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 99999; background: rgba(15,23,42,0.62); -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; padding: 1.2rem; opacity: 0; visibility: hidden; transition: opacity .35s ease, visibility .35s ease; font-family: 'Syne', sans-serif; }
    .prox-onb-overlay.is-open { opacity: 1; visibility: visible; }
    .prox-onb-modal { background: #FFFFFF; width: 100%; max-width: 560px; border-radius: 18px; overflow: hidden; position: relative; box-shadow: 0 30px 80px rgba(15,23,42,0.35); transform: translateY(28px) scale(0.97); opacity: 0; transition: transform .45s cubic-bezier(.2,.9,.25,1), opacity .4s ease; max-height: 88vh; max-height: 88dvh; display: flex; flex-direction: column; }
    .prox-onb-overlay.is-open .prox-onb-modal { transform: translateY(0) scale(1); opacity: 1; }
    .prox-onb-close { position: absolute; top: 16px; right: 16px; width: 34px; height: 34px; border-radius: 50%; border: 1px solid rgba(15,23,42,0.12); background: #F5F7FF; color: #64748B; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: .9rem; z-index: 5; transition: all .25s; }
    .prox-onb-close:hover { background: #0F172A; color: #fff; transform: rotate(90deg); }
    .prox-onb-header { padding: 1.6rem 2rem 0; display: flex; flex-direction: column; gap: .9rem; }
    .prox-onb-eyebrow { font-family: 'DM Mono', monospace; font-size: .68rem; letter-spacing: 2.5px; text-transform: uppercase; color: #2563EB; display: flex; align-items: center; gap: .6rem; }
    .prox-onb-eyebrow::before { content: ''; width: 22px; height: 1.5px; background: #2563EB; }
    .prox-onb-dots { display: flex; gap: .4rem; }
    .prox-onb-dot { width: 26px; height: 4px; border-radius: 3px; background: #E4E9F5; transition: background .3s, width .3s; }
    .prox-onb-dot.active { background: #2563EB; width: 38px; }
    .prox-onb-dot.done { background: #93C5FD; }
    .prox-onb-body { padding: 1.6rem 2rem 2rem; overflow-y: auto; -webkit-overflow-scrolling: touch; flex: 1; }
    .prox-onb-step { animation: proxStepIn .45s cubic-bezier(.2,.9,.25,1); }
    @keyframes proxStepIn { from { opacity: 0; transform: translateX(18px); } to { opacity: 1; transform: translateX(0); } }
    .prox-onb-icon { width: 60px; height: 60px; border-radius: 16px; background: linear-gradient(135deg,#2563EB,#3B82F6); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #fff; margin-bottom: 1.2rem; box-shadow: 0 10px 26px rgba(37,99,235,0.3); animation: proxIconPop .5s cubic-bezier(.34,1.56,.64,1); }
    @keyframes proxIconPop { 0% { transform: scale(0.4) rotate(-10deg); opacity: 0; } 100% { transform: scale(1) rotate(0deg); opacity: 1; } }
    .prox-onb-step h2 { font-family: 'Bebas Neue', sans-serif; font-weight: 400; font-size: 2.1rem; letter-spacing: .5px; line-height: 1.05; color: #0F172A; margin-bottom: .8rem; }
    .prox-onb-step h2 span { color: #2563EB; }
    .prox-onb-step p { color: #64748B; font-size: .95rem; line-height: 1.65; margin-bottom: 1rem; }
    .prox-onb-step p:last-child { margin-bottom: 0; }
    .prox-onb-step strong { color: #0F172A; }
    .prox-onb-list { list-style: none; display: flex; flex-direction: column; gap: .7rem; margin-top: 1rem; }
    .prox-onb-list li { display: flex; align-items: flex-start; gap: .7rem; font-size: .9rem; color: #334155; line-height: 1.55; opacity: 0; animation: proxLiIn .4s ease forwards; }
    .prox-onb-list li:nth-child(1) { animation-delay: .1s; }
    .prox-onb-list li:nth-child(2) { animation-delay: .2s; }
    .prox-onb-list li:nth-child(3) { animation-delay: .3s; }
    .prox-onb-list li:nth-child(4) { animation-delay: .4s; }
    .prox-onb-list li:nth-child(5) { animation-delay: .5s; }
    @keyframes proxLiIn { from { opacity: 0; transform: translateX(-8px); } to { opacity: 1; transform: translateX(0); } }
    .prox-onb-list i { width: 22px; height: 22px; flex-shrink: 0; border-radius: 6px; background: #EFF6FF; color: #2563EB; font-size: .7rem; display: flex; align-items: center; justify-content: center; margin-top: 2px; }
    .prox-onb-list.prox-bad i { background: #FEF2F2; color: #DC2626; }
    .prox-onb-list.prox-good i { background: #ECFDF5; color: #059669; }
    .prox-onb-types { display: grid; grid-template-columns: 1fr 1fr; gap: .7rem; margin-top: 1.1rem; }
    .prox-onb-type-card { border: 1px solid #E4E9F5; border-radius: 12px; padding: 1rem; background: #F5F7FF; opacity: 0; animation: proxLiIn .4s ease forwards; }
    .prox-onb-type-card:nth-child(1) { animation-delay: .05s; }
    .prox-onb-type-card:nth-child(2) { animation-delay: .15s; }
    .prox-onb-type-card:nth-child(3) { animation-delay: .25s; }
    .prox-onb-type-card:nth-child(4) { animation-delay: .35s; }
    .prox-onb-type-icon { width: 34px; height: 34px; border-radius: 8px; color: #fff; display: flex; align-items: center; justify-content: center; font-size: .85rem; margin-bottom: .6rem; }
    .prox-onb-type-card h4 { font-size: .9rem; color: #0F172A; margin-bottom: .3rem; }
    .prox-onb-type-card p { font-size: .76rem; color: #64748B; line-height: 1.5; margin: 0; }
    .prox-onb-type-card .tag { display: inline-block; margin-top: .5rem; font-family: 'DM Mono', monospace; font-size: .6rem; letter-spacing: .5px; text-transform: uppercase; color: #2563EB; background: #EFF6FF; padding: .2rem .5rem; border-radius: 4px; }
    .prox-onb-flow { display: flex; flex-direction: column; gap: .8rem; margin-top: 1.1rem; }
    .prox-onb-flow-item { display: flex; align-items: center; gap: .9rem; border: 1px solid #E4E9F5; border-radius: 10px; padding: .8rem .9rem; background: #FAFBFF; }
    .prox-onb-flow-num { width: 28px; height: 28px; border-radius: 50%; background: #0F172A; color: #fff; font-family: 'Bebas Neue', sans-serif; font-size: 1rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .prox-onb-flow-item div p { margin: 0; font-size: .85rem; color: #334155; }
    .prox-onb-flow-item div strong { display: block; font-size: .88rem; color: #0F172A; margin-bottom: .1rem; }
    .prox-onb-callout { margin-top: 1.2rem; padding: .85rem 1rem; border-radius: 10px; background: #FFFBEB; border: 1px solid #FDE68A; display: flex; gap: .7rem; align-items: flex-start; }
    .prox-onb-callout i { color: #D97706; margin-top: 2px; }
    .prox-onb-callout p { margin: 0; font-size: .82rem; color: #78350F; line-height: 1.5; }
    .prox-onb-footer { padding: 1rem 2rem 1.4rem; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #EEF1FA; }
    .prox-onb-footer-right { display: flex; gap: .6rem; }
    .prox-onb-btn { font-family: 'Syne', sans-serif; font-weight: 700; font-size: .78rem; letter-spacing: 1px; text-transform: uppercase; padding: .7rem 1.3rem; border-radius: 7px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: .5rem; transition: all .25s; -webkit-appearance: none; appearance: none; }
    .prox-onb-ghost { background: transparent; color: #64748B; border: 1px solid rgba(100,116,139,0.28); }
    .prox-onb-ghost:hover { color: #0F172A; border-color: #0F172A; }
    .prox-onb-solid { background: #2563EB; color: #fff; box-shadow: 0 6px 18px rgba(37,99,235,.3); }
    .prox-onb-solid:hover { background: #3B82F6; transform: translateY(-1px); }
    #proxOnbBack[disabled] { opacity: 0; pointer-events: none; width: 0; padding: 0; overflow: hidden; }
    .prox-onb-progress-track { height: 3px; background: #EEF1FA; width: 100%; }
    .prox-onb-progress-fill { height: 100%; background: linear-gradient(90deg,#2563EB,#3B82F6); width: 14%; transition: width .4s cubic-bezier(.2,.9,.25,1); }
    @media (max-width: 560px) {
        .prox-onb-types { grid-template-columns: 1fr 1fr; }
        .prox-onb-step h2 { font-size: 1.7rem; }
        .prox-onb-header, .prox-onb-body, .prox-onb-footer { padding-left: 1.3rem; padding-right: 1.3rem; }
    }
</style>
@endpush

@section('content')

<section class="hero">
    <div class="hero-card">
        <div class="hero-glow"></div>

        <!-- Curved connector lines -->
        <svg class="hero-lines" viewBox="0 0 1000 600" preserveAspectRatio="none" aria-hidden="true">
            <path d="M 90 190 C 90 330, 70 450, 0 590"></path>
            <path d="M 150 370 C 150 450, 140 520, 110 600"></path>
            <path d="M 910 190 C 910 330, 930 450, 1000 590"></path>
            <path d="M 850 370 C 850 450, 860 520, 890 600"></path>
        </svg>

        <!-- Floating tiles -->
        <div class="hero-tile"><i class="fas fa-house-user"></i></div>
        <div class="hero-tile"><i class="fas fa-server"></i></div>
        <div class="hero-tile"><i class="fas fa-globe"></i></div>
        <div class="hero-tile"><i class="fas fa-network-wired"></i></div>
        <div class="hero-tile"><i class="fas fa-mobile-screen"></i></div>
        <div class="hero-tile"><i class="fas fa-shield-halved"></i></div>

        <div class="hero-content">
            <div class="hero-pill">
                <span class="dot"></span>
                Premium Proxy Network
                <span class="sep">|</span>
                190+ Countries
            </div>

            <h1>
                Power <span class="outline">Your</span><br>
                Connections
            </h1>

            <p class="hero-desc">
                Residential, datacenter, ISP and mobile proxies across 190+ countries.
                Fast, reliable, and built for scraping, automation, and anonymity at scale.
            </p>

            <div class="hero-actions">
                <a href="{{ route('register') }}" class="btn btn-white btn-large">Get Proxies</a>
                <a href="#features" class="btn btn-outline-white btn-large">See How</a>
            </div>

            <div class="hero-note">Instant delivery · 99.9% uptime · 24/7 support</div>
        </div>
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
        <div class="ticker-item"><span class="dot">●</span> Unlimited Bandwidth Plans</div>
        <div class="ticker-item"><span class="dot">●</span> Sticky &amp; Rotating Sessions</div>
        <div class="ticker-item"><span class="dot">●</span> 99.9% Uptime</div>
        <div class="ticker-item"><span class="dot">●</span> 24/7 Support</div>
        <div class="ticker-item"><span class="dot">●</span> Residential Proxies</div>
        <div class="ticker-item"><span class="dot">●</span> Datacenter Proxies</div>
        <div class="ticker-item"><span class="dot">●</span> ISP Proxies</div>
        <div class="ticker-item"><span class="dot">●</span> Mobile Proxies</div>
        <div class="ticker-item"><span class="dot">●</span> 190+ Countries</div>
        <div class="ticker-item"><span class="dot">●</span> HTTP &amp; SOCKS5</div>
        <div class="ticker-item"><span class="dot">●</span> Unlimited Bandwidth Plans</div>
        <div class="ticker-item"><span class="dot">●</span> Sticky &amp; Rotating Sessions</div>
        <div class="ticker-item"><span class="dot">●</span> 99.9% Uptime</div>
        <div class="ticker-item"><span class="dot">●</span> 24/7 Support</div>
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
                <p>Orders are provisioned within minutes. Our automated pipeline connects to the provider and delivers your credentials without you waiting around.</p>
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
                <p>99.9% uptime across our network, with fast response times whether you're scraping, automating, or browsing.</p>
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
                <p>Competitive rates with bulk discounts on bandwidth and IP plans. Scaling up shouldn't drain your budget.</p>
            </div>
            <div class="feat-card reveal">
                <div class="feat-num">06</div>
                <div class="feat-icon"><i class="fas fa-undo"></i></div>
                <h3>Refund Guarantee</h3>
                <p>Something goes wrong? We make it right. Straightforward refund policy, no hoops.</p>
            </div>
        </div>

        <!-- ===== PROXY TYPES ===== -->
        <div class="section-tag types-head reveal">Solutions For Every Use Case</div>
        <div class="features-layout">

            <div class="feat-card reveal">
                <div class="type-top">
                    <span class="type-price tp-res"><i class="fas fa-house-user"></i> from {{ $homePrices['residential'] }}/GB</span>
                    <span class="type-unit">Per GB</span>
                </div>
                <h3><a href="{{ route('products.show', 'residential') }}" style="color:inherit;text-decoration:none">Residential</a></h3>
                <p>Ideal for anonymous high-volume web scraping, SEO monitoring, and geo-targeted research at scale.</p>
            </div>

            <div class="feat-card reveal">
                <div class="type-top">
                    <span class="type-price tp-isp"><i class="fas fa-network-wired"></i> from {{ $homePrices['isp'] }}/proxy</span>
                    <span class="type-unit">Per proxy</span>
                </div>
                <h3><a href="{{ route('products.show', 'isp') }}" style="color:inherit;text-decoration:none">ISP</a></h3>
                <p>Great for ecommerce automation, ad verification, and long-session reliability. Datacenter speed with residential-level trust.</p>
            </div>

            <div class="feat-card reveal">
                <div class="type-top">
                    <span class="type-price tp-dc"><i class="fas fa-server"></i> from {{ $homePrices['datacenter'] }}/proxy</span>
                    <span class="type-unit">Per proxy</span>
                </div>
                <h3><a href="{{ route('products.show', 'datacenter') }}" style="color:inherit;text-decoration:none">Datacenter</a></h3>
                <p>Best for speed-critical work like software testing, scraping, and infrastructure monitoring.</p>
            </div>

            <div class="feat-card reveal">
                <div class="type-top">
                    <span class="type-price tp-mob"><i class="fas fa-mobile-screen"></i> from {{ $homePrices['mobile'] }}/day</span>
                    <span class="type-unit">Per day</span>
                </div>
                <h3><a href="{{ route('products.show', 'mobile') }}" style="color:inherit;text-decoration:none">Mobile</a></h3>
                <p>Perfect for social platforms, mobile app QA, and regional ad testing on real carrier IPs.</p>
            </div>

        </div>
        <!-- ===== END PROXY TYPES ===== -->

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
        <div class="platform-cell"><img class="flag flag-lg" src="https://flagcdn.com/w80/us.png" alt="USA"><span>USA</span></div>
        <div class="platform-cell"><img class="flag flag-lg" src="https://flagcdn.com/w80/gb.png" alt="UK"><span>UK</span></div>
        <div class="platform-cell"><img class="flag flag-lg" src="https://flagcdn.com/w80/de.png" alt="Germany"><span>Germany</span></div>
        <div class="platform-cell"><img class="flag flag-lg" src="https://flagcdn.com/w80/ng.png" alt="Nigeria"><span>Nigeria</span></div>
        <div class="platform-cell"><img class="flag flag-lg" src="https://flagcdn.com/w80/in.png" alt="India"><span>India</span></div>
        <div class="platform-cell"><img class="flag flag-lg" src="https://flagcdn.com/w80/br.png" alt="Brazil"><span>Brazil</span></div>
        <div class="platform-cell"><img class="flag flag-lg" src="https://flagcdn.com/w80/za.png" alt="South Africa"><span>South Africa</span></div>
        <div class="platform-cell"><img class="flag flag-lg" src="https://flagcdn.com/w80/ae.png" alt="UAE"><span>UAE</span></div>
        <div class="platform-cell"><img class="flag flag-lg" src="https://flagcdn.com/w80/ca.png" alt="Canada"><span>Canada</span></div>
        <div class="platform-cell"><img class="flag flag-lg" src="https://flagcdn.com/w80/fr.png" alt="France"><span>France</span></div>
        <div class="platform-cell"><img class="flag flag-lg" src="https://flagcdn.com/w80/jp.png" alt="Japan"><span>Japan</span></div>
        <a href="{{ route('locations') }}" class="platform-cell"><i class="fas fa-earth-americas"></i><span>+ many more</span></a>
    </div>
</section>

<!-- RESELLER / API SECTION -->
<section style="padding: 6rem 3rem; background: var(--navy); position: relative; overflow: hidden;" class="reseller-section">

    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:900px;height:500px;background:radial-gradient(ellipse,rgba(37,99,235,0.15) 0%,transparent 70%);pointer-events:none;"></div>
    <div style="position:absolute;top:0;left:0;right:0;bottom:0;background-image:radial-gradient(circle,rgba(255,255,255,0.04) 1px,transparent 1px);background-size:36px 36px;pointer-events:none;"></div>

    <div style="max-width:1400px;margin:0 auto;position:relative;z-index:1;">

        <div style="margin-bottom:4rem;">
            <div class="section-tag reveal" style="color:#60A5FA;">
                <span style="background:#60A5FA;"></span>
                For Resellers &amp; Developers
            </div>
            <div style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:2rem;border-bottom:1px solid rgba(255,255,255,0.08);padding-bottom:2rem;">
                <h2 class="reveal" style="font-family:'Bebas Neue',sans-serif;font-weight:400;font-size:clamp(2.5rem,6vw,5.5rem);line-height:1;color:#fff;">
                    Build On<br>
                    <span style="-webkit-text-stroke:1px rgba(255,255,255,0.2);color:transparent;">ProxWorld</span>
                </h2>
                <p class="reveal reseller-hdr-p" style="color:#94A3B8;font-size:0.95rem;max-width:380px;line-height:1.7;text-align:right;">
                    White-label our platform or connect your own panel via API.
                    Thousands of services, one integration.
                </p>
            </div>
        </div>

        <!-- Cards grid -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(min(100%, 400px), 1fr));gap:1px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.08);border-radius:12px;overflow:hidden;">

            <!-- Reseller Card -->
            <div class="reveal" style="background:#0F172A;padding:clamp(1.5rem, 4vw, 3rem);position:relative;overflow:hidden;transition:background 0.3s;"
                 onmouseenter="this.style.background='#1E293B'"
                 onmouseleave="this.style.background='#0F172A'">

                <div style="position:absolute;top:0;left:0;width:3px;height:100%;background:linear-gradient(to bottom,#3B82F6,transparent);"></div>

                <div style="width:52px;height:52px;background:rgba(59,130,246,0.12);border:1px solid rgba(59,130,246,0.25);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:1.5rem;">
                    <i class="fas fa-store" style="color:#60A5FA;font-size:1.3rem;"></i>
                </div>

                <div style="font-family:'DM Mono',monospace;font-size:0.65rem;letter-spacing:2px;color:#475569;text-transform:uppercase;margin-bottom:0.6rem;">01 — Reseller Panel</div>
                <h3 style="font-size:clamp(1.1rem, 2.5vw, 1.4rem);font-weight:800;color:#fff;margin-bottom:1rem;">Your Own Proxy Reseller Panel</h3>
                <p style="color:#94A3B8;font-size:clamp(0.82rem, 1.5vw, 0.9rem);line-height:1.7;margin-bottom:2rem;">
                    Sell our services under your own brand. Set your own prices, manage your own customers,
                    and keep 100% of your margin. No setup fees — just top up and go.
                </p>

                <div style="display:flex;flex-direction:column;gap:0.7rem;margin-bottom:2.5rem;">
                    <div style="display:flex;align-items:center;gap:0.8rem;">
                        <div style="width:20px;height:20px;min-width:20px;background:rgba(5,150,105,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-check" style="color:#10B981;font-size:0.55rem;"></i>
                        </div>
                        <span style="color:#94A3B8;font-size:clamp(0.8rem, 1.5vw, 0.85rem);">Custom pricing on all services</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:0.8rem;">
                        <div style="width:20px;height:20px;min-width:20px;background:rgba(5,150,105,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-check" style="color:#10B981;font-size:0.55rem;"></i>
                        </div>
                        <span style="color:#94A3B8;font-size:clamp(0.8rem, 1.5vw, 0.85rem);">Manage customers &amp; transactions</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:0.8rem;">
                        <div style="width:20px;height:20px;min-width:20px;background:rgba(5,150,105,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-check" style="color:#10B981;font-size:0.55rem;"></i>
                        </div>
                        <span style="color:#94A3B8;font-size:clamp(0.8rem, 1.5vw, 0.85rem);">Instant order fulfillment</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:0.8rem;">
                        <div style="width:20px;height:20px;min-width:20px;background:rgba(5,150,105,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-check" style="color:#10B981;font-size:0.55rem;"></i>
                        </div>
                        <span style="color:#94A3B8;font-size:clamp(0.8rem, 1.5vw, 0.85rem);">500+ services across all platforms</span>
                    </div>
                </div>

                <a href="{{ route('register') }}" class="btn btn-solid" style="display:inline-flex;align-items:center;gap:0.6rem;width:100%;justify-content:center;max-width:260px;">
                    Start Reselling &nbsp;<i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- API Card -->
            <div class="reveal" style="background:#0F172A;padding:clamp(1.5rem, 4vw, 3rem);position:relative;overflow:hidden;transition:background 0.3s;"
                 onmouseenter="this.style.background='#1E293B'"
                 onmouseleave="this.style.background='#0F172A'">

                <div style="position:absolute;top:0;left:0;width:3px;height:100%;background:linear-gradient(to bottom,#8B5CF6,transparent);"></div>

                <div style="width:52px;height:52px;background:rgba(139,92,246,0.12);border:1px solid rgba(139,92,246,0.25);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:1.5rem;">
                    <i class="fas fa-code" style="color:#A78BFA;font-size:1.3rem;"></i>
                </div>

                <div style="font-family:'DM Mono',monospace;font-size:0.65rem;letter-spacing:2px;color:#475569;text-transform:uppercase;margin-bottom:0.6rem;">02 — Developer API</div>
                <h3 style="font-size:clamp(1.1rem, 2.5vw, 1.4rem);font-weight:800;color:#fff;margin-bottom:1rem;">Integrate via REST API</h3>
                <p style="color:#94A3B8;font-size:clamp(0.82rem, 1.5vw, 0.9rem);line-height:1.7;margin-bottom:2rem;">
                    Connect ProxWorld directly to your own app, bot, or panel.
                    Simple POST requests, instant responses. Get your API key and start building in minutes.
                </p>

                <div style="background:#020617;border:1px solid rgba(139,92,246,0.2);border-radius:8px;padding:1rem 1.2rem;margin-bottom:2rem;font-family:'DM Mono',monospace;font-size:clamp(0.65rem, 1.5vw, 0.72rem);line-height:1.8;overflow-x:auto;">
                    <div style="color:#475569;margin-bottom:0.3rem;">// Get all services</div>
                    <div><span style="color:#8B5CF6;">POST</span> <span style="color:#60A5FA;">{{ url('/api/v1') }}</span></div>
                    <div style="margin-top:0.5rem;"><span style="color:#94A3B8;">action</span> <span style="color:#475569;">=</span> <span style="color:#10B981;">"services"</span></div>
                    <div><span style="color:#94A3B8;">key</span> <span style="color:#475569;">=</span> <span style="color:#10B981;">"pxw_xxxxxxxxxxxx"</span></div>
                </div>

                <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                    <a href="{{ route('api.docs') }}" class="btn"
                       style="background:rgba(139,92,246,0.15);color:#A78BFA;border:1px solid rgba(139,92,246,0.3);display:inline-flex;align-items:center;gap:0.6rem;flex:1;min-width:120px;justify-content:center;">
                        <i class="fas fa-book"></i> API Docs
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-solid" style="display:inline-flex;align-items:center;gap:0.6rem;flex:1;min-width:120px;justify-content:center;">
                        Get API Key &nbsp;<i class="fas fa-key"></i>
                    </a>
                </div>
            </div>

        </div>

        <!-- Bottom stat bar -->
        <div class="reveal" style="background:#0F172A;border:1px solid rgba(255,255,255,0.06);border-top:none;border-radius:0 0 12px 12px;padding:1.2rem clamp(1rem, 3vw, 2rem);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <div style="display:flex;gap:clamp(1.5rem, 4vw, 3rem);flex-wrap:wrap;">
                <div>
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:clamp(1.4rem, 3vw, 1.8rem);color:#fff;line-height:1;">500<span style="color:#3B82F6;">+</span></div>
                    <div style="font-family:'DM Mono',monospace;font-size:0.6rem;letter-spacing:1.5px;color:#475569;text-transform:uppercase;margin-top:2px;">Services</div>
                </div>
                <div>
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:clamp(1.4rem, 3vw, 1.8rem);color:#fff;line-height:1;">99.9<span style="color:#3B82F6;">%</span></div>
                    <div style="font-family:'DM Mono',monospace;font-size:0.6rem;letter-spacing:1.5px;color:#475569;text-transform:uppercase;margin-top:2px;">API Uptime</div>
                </div>
                <div>
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:clamp(1.4rem, 3vw, 1.8rem);color:#fff;line-height:1;">&lt;200<span style="color:#3B82F6;">ms</span></div>
                    <div style="font-family:'DM Mono',monospace;font-size:0.6rem;letter-spacing:1.5px;color:#475569;text-transform:uppercase;margin-top:2px;">Avg Response</div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:0.6rem;">
                <div style="width:7px;height:7px;background:#10B981;border-radius:50%;box-shadow:0 0 8px rgba(16,185,129,0.6);animation:blink 1.5s ease-in-out infinite;"></div>
                <span style="font-family:'DM Mono',monospace;font-size:0.65rem;letter-spacing:1.5px;color:#475569;text-transform:uppercase;">API Operational</span>
            </div>
        </div>

    </div>
</section>

{{-- ===== BLOG (latest 5 posts) ===== --}}
@php
    $latestPosts = \App\Models\Newsletter::publishedOnBlog()->latest('sent_at')->limit(5)->get();
@endphp

@if($latestPosts->isNotEmpty())
<section id="blog" class="blog-section">
    <div class="blog-inner">
        <div class="section-tag reveal">From The Blog</div>

        <div class="blog-head reveal">
            <h2>Proxy Guides<br><span class="outline">&amp; Insights</span></h2>
            <p>Tips, tutorials, and news on residential, datacenter, ISP, and mobile proxies — straight from the team building them.</p>
        </div>

        <div class="hb-grid reveal">
            @foreach($latestPosts as $post)
                <a href="{{ route('blog.show', $post->slug) }}" class="hb-card">
                    <div class="hb-img" @if($post->featured_image_url) style="background-image:url('{{ $post->featured_image_url }}');" @endif>
                        @unless($post->featured_image_url)
                            <i class="fa-regular fa-image"></i>
                        @endunless
                    </div>
                    <div class="hb-body">
                        <span class="hb-date">{{ $post->sent_at->format('M d, Y') }}</span>
                        <h3 class="hb-title">{{ $post->subject }}</h3>
                        @if($post->excerpt)
                            <p class="hb-excerpt">{{ $post->excerpt }}</p>
                        @endif
                        <span class="hb-more">Read Article →</span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="blog-cta reveal">
            <a href="{{ route('blog.index') }}" class="btn btn-solid btn-large">
                View All Blogs &nbsp;<i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
@endif

<section class="cta-section">
    <h2 class="reveal">
        Go<br>
        <span class="outline">Anonymous</span><br>
        Today
    </h2>
    <p class="reveal">Join 50,000+ developers, marketers, and businesses running on ProxWorld's network.</p>
    <a href="{{ route('register') }}" class="btn btn-solid btn-large reveal">Create Free Account</a>
</section>

<!-- ONBOARDING MODAL -->
<div id="proxOnboardOverlay" class="prox-onb-overlay" role="dialog" aria-modal="true" aria-label="ProxWorld quick start">
  <div class="prox-onb-modal" id="proxOnboardModal">

    <button type="button" class="prox-onb-close" id="proxOnbCancel" title="Cancel" aria-label="Cancel">
      <i class="fas fa-xmark"></i>
    </button>

    <div class="prox-onb-header">
      <div class="prox-onb-eyebrow">ProxWorld Quick Start</div>
      <div class="prox-onb-dots" id="proxOnbDots"></div>
    </div>

    <div class="prox-onb-body" id="proxOnbBody">
      <!-- injected per step -->
    </div>

    <div class="prox-onb-footer">
      <div class="prox-onb-footer-left">
        <button type="button" class="prox-onb-btn prox-onb-ghost" id="proxOnbSkip">Skip</button>
      </div>
      <div class="prox-onb-footer-right">
        <button type="button" class="prox-onb-btn prox-onb-ghost" id="proxOnbBack">
          <i class="fas fa-arrow-left"></i> Back
        </button>
        <button type="button" class="prox-onb-btn prox-onb-solid" id="proxOnbContinue">
          Continue <i class="fas fa-arrow-right"></i>
        </button>
      </div>
    </div>

    <div class="prox-onb-progress-track">
      <div class="prox-onb-progress-fill" id="proxOnbProgressFill"></div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
(function(){
  var steps = [
    {
      icon: '<i class="fas fa-circle-question"></i>',
      html: `
        <h2>What Is a <span>Proxy?</span></h2>
        <p>A proxy is a middleman server that sits between your device and the internet. Instead of websites seeing <strong>your</strong> real IP address and location, they see the proxy's.</p>
        <p>Every request you make — loading a page, submitting a form, running a bot — is routed through that proxy first. This lets you appear to browse from a different city, country, or device network entirely.</p>
        <ul class="prox-onb-list prox-good">
          <li><i class="fas fa-check"></i> Hide your real IP and location</li>
          <li><i class="fas fa-check"></i> Access geo-restricted content and prices</li>
          <li><i class="fas fa-check"></i> Run multiple accounts without linking them</li>
          <li><i class="fas fa-check"></i> Automate, scrape, and test at scale safely</li>
        </ul>
      `
    },
    {
      icon: '<i class="fas fa-shield-halved"></i>',
      html: `
        <h2>Proxy vs <span>VPN</span></h2>
        <p>They both mask your IP, but they're built for very different jobs — and using the wrong one can cost you accounts.</p>
        <ul class="prox-onb-list prox-bad">
          <li><i class="fas fa-triangle-exclamation"></i> VPN IPs are shared by thousands of users at once, so platforms flag them constantly — many accounts get <strong>banned or locked</strong> just for logging in through a popular VPN.</li>
          <li><i class="fas fa-triangle-exclamation"></i> A large share of VPN IP ranges are already tagged as high‑risk by fraud‑detection systems because of how heavily they're reused for scams and abuse.</li>
          <li><i class="fas fa-triangle-exclamation"></i> One IP, one location — no control over exactly where you appear from, and most VPNs throttle bandwidth during heavy use.</li>
        </ul>
        <ul class="prox-onb-list prox-good">
          <li><i class="fas fa-check"></i> Proxies (especially residential, ISP &amp; mobile) carry a <strong>very low scam/fraud score</strong> — they look like real, everyday users.</li>
          <li><i class="fas fa-check"></i> Pick the exact country, city, or carrier you need, down to the individual connection.</li>
          <li><i class="fas fa-check"></i> Built for scale — rotate thousands of IPs, run automation, and manage many accounts side‑by‑side without tripping bans.</li>
          <li><i class="fas fa-check"></i> No shared bandwidth ceiling — proxies are sized and priced for real workloads.</li>
        </ul>
      `
    },
    {
      icon: '<i class="fas fa-diagram-project"></i>',
      html: `
        <h2>The 4 Types of <span>Proxies</span></h2>
        <p>ProxWorld offers every proxy type — each suited to a different use case.</p>
        <div class="prox-onb-types">
          <div class="prox-onb-type-card">
            <div class="prox-onb-type-icon" style="background:#2563EB"><i class="fas fa-house-user"></i></div>
            <h4>Residential</h4>
            <p>Real IPs assigned by ISPs to actual home users. Extremely low detection risk.</p>
            <span class="tag">Highest trust</span>
          </div>
          <div class="prox-onb-type-card">
            <div class="prox-onb-type-icon" style="background:#7C3AED"><i class="fas fa-server"></i></div>
            <h4>Datacenter</h4>
            <p>Hosted in data centers. Extremely fast and affordable for high-volume tasks.</p>
            <span class="tag">Fastest &amp; cheapest</span>
          </div>
          <div class="prox-onb-type-card">
            <div class="prox-onb-type-icon" style="background:#0D9488"><i class="fas fa-network-wired"></i></div>
            <h4>ISP</h4>
            <p>Datacenter speed with residential legitimacy. Static &amp; dedicated to you.</p>
            <span class="tag">Best for account mgmt</span>
          </div>
          <div class="prox-onb-type-card">
            <div class="prox-onb-type-icon" style="background:#EA580C"><i class="fas fa-mobile-screen"></i></div>
            <h4>Mobile</h4>
            <p>Routed through real carrier networks. Hardest of all to detect or block.</p>
            <span class="tag">Ultimate stealth</span>
          </div>
        </div>
      `
    },
    {
      icon: '<i class="fas fa-user-plus"></i>',
      html: `
        <h2>Step 1 — <span>Register or Login</span></h2>
        <p>Everything starts with an account. Create one in seconds, or log back in if you already have one.</p>
        <div class="prox-onb-flow">
          <div class="prox-onb-flow-item">
            <div class="prox-onb-flow-num">1</div>
            <div><strong>Sign up</strong><p>Click "Get Started" and fill in your details — takes under a minute.</p></div>
          </div>
          <div class="prox-onb-flow-item">
            <div class="prox-onb-flow-num">2</div>
            <div><strong>Verify &amp; log in</strong><p>Confirm your account and you're straight into your dashboard.</p></div>
          </div>
        </div>
      `
    },
    {
      icon: '<i class="fas fa-wallet"></i>',
      html: `
        <h2>Step 2 — <span>Top Up Your Balance</span></h2>
        <p>Before you can place an order, you'll need funds in your wallet.</p>
        <div class="prox-onb-flow">
          <div class="prox-onb-flow-item">
            <div class="prox-onb-flow-num">1</div>
            <div><strong>Go to Wallet / Top Up</strong><p>Found right on your dashboard.</p></div>
          </div>
          <div class="prox-onb-flow-item">
            <div class="prox-onb-flow-num">2</div>
            <div><strong>Choose an amount &amp; pay</strong><p>Your balance updates instantly once payment confirms.</p></div>
          </div>
        </div>
        <div class="prox-onb-callout">
          <i class="fas fa-circle-info"></i>
          <p>You must have a sufficient balance before you can create an order — orders are deducted directly from your wallet.</p>
        </div>
      `
    },
    {
      icon: '<i class="fas fa-cart-shopping"></i>',
      html: `
        <h2>Step 3 — <span>Create New Order</span></h2>
        <p>Once you're funded, click <strong>"Create New Order"</strong> from your dashboard and configure your proxy:</p>
        <div class="prox-onb-flow">
          <div class="prox-onb-flow-item">
            <div class="prox-onb-flow-num">1</div>
            <div><strong>Pick a proxy type</strong><p>Residential, Datacenter, ISP, or Mobile.</p></div>
          </div>
          <div class="prox-onb-flow-item">
            <div class="prox-onb-flow-num">2</div>
            <div><strong>Choose a country</strong><p>Where available for that proxy type.</p></div>
          </div>
          <div class="prox-onb-flow-item">
            <div class="prox-onb-flow-num">3</div>
            <div><strong>Set your quantity</strong><p>Then confirm to complete your purchase.</p></div>
          </div>
        </div>
      `
    },
    {
      icon: '<i class="fas fa-circle-check"></i>',
      html: `
        <h2>Step 4 — <span>Your Order Details</span></h2>
        <p>After purchase, you're taken straight to the Order Details page with everything you need:</p>
        <ul class="prox-onb-list prox-good">
          <li><i class="fas fa-check"></i> Proxy IP &amp; port</li>
          <li><i class="fas fa-check"></i> Username &amp; password / login info</li>
          <li><i class="fas fa-check"></i> Location, expiry, and usage details</li>
        </ul>
        <div class="prox-onb-callout">
          <i class="fas fa-rocket"></i>
          <p>That's it — you're ready to go! You can revisit this guide anytime from this home page. Thank you for trusting ProxWorld</p>
        </div>
      `
    }
  ];

  var current = 0;
  var overlay = document.getElementById('proxOnboardOverlay');
  var body = document.getElementById('proxOnbBody');
  var dotsWrap = document.getElementById('proxOnbDots');
  var fill = document.getElementById('proxOnbProgressFill');
  var backBtn = document.getElementById('proxOnbBack');
  var continueBtn = document.getElementById('proxOnbContinue');

  function buildDots(){
    dotsWrap.innerHTML = '';
    steps.forEach(function(_, i){
      var d = document.createElement('div');
      d.className = 'prox-onb-dot';
      dotsWrap.appendChild(d);
    });
  }

  function render(){
    var step = steps[current];
    body.innerHTML = '<div class="prox-onb-step"><div class="prox-onb-icon">' + step.icon + '</div>' + step.html + '</div>';

    var dots = dotsWrap.querySelectorAll('.prox-onb-dot');
    dots.forEach(function(d, i){
      d.classList.remove('active','done');
      if(i < current) d.classList.add('done');
      if(i === current) d.classList.add('active');
    });

    fill.style.width = (((current + 1) / steps.length) * 100) + '%';
    backBtn.disabled = current === 0;

    if(current === steps.length - 1){
      continueBtn.innerHTML = 'Get Started <i class="fas fa-rocket"></i>';
    } else {
      continueBtn.innerHTML = 'Continue <i class="fas fa-arrow-right"></i>';
    }
  }

  function open(){
    current = 0;
    buildDots();
    render();
    overlay.classList.add('is-open');
    document.body.style.overflow = 'hidden';
  }

  function close(){
    overlay.classList.remove('is-open');
    document.body.style.overflow = '';
  }

  continueBtn.addEventListener('click', function(){
    if(current === steps.length - 1){
      window.location.href = "{{ route('register') }}";
      return;
    }
    current++;
    render();
  });

  backBtn.addEventListener('click', function(){
    if(current > 0){
      current--;
      render();
    }
  });

  document.getElementById('proxOnbCancel').addEventListener('click', close);
  document.getElementById('proxOnbSkip').addEventListener('click', close);
  overlay.addEventListener('click', function(e){
    if(e.target === overlay) close();
  });
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape') close();
  });

  // Show automatically when the welcome page loads
  window.addEventListener('load', function(){
    setTimeout(open, 500);
  });

  // Expose so you can trigger it manually too, e.g. from a "How it works" link
  window.ProxOnboarding = { open: open, close: close };
})();
</script>
@endpush
