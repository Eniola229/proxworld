<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProxWorld — Premium Residential, Datacenter, ISP &amp; Mobile Proxies</title>
    <meta name="description" content="Buy fast, reliable proxies with ProxWorld. Residential, datacenter, ISP, and mobile proxies across 190+ countries — built for scraping, automation, and anonymity." />
    <meta name="keyword" content="buy proxies, residential proxies, datacenter proxies, ISP proxies, mobile proxies, proxy provider Nigeria, buy proxies online, rotating proxies, SOCKS5 proxies" />
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/LOGO.jpeg') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Syne:wght@400;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg:        #F5F7FF;
            --bg-2:      #EEF1FA;
            --bg-3:      #E4E9F5;
            --surface:   #FFFFFF;
            --accent:    #2563EB;
            --electric:  #3B82F6;
            --gold:      #D97706;
            --navy:      #0F172A;
            --navy-2:    #1E293B;
            --muted:     #64748B;
            --soft:      #94A3B8;
            --border:    rgba(37, 99, 235, 0.14);
            --border-2:  rgba(37, 99, 235, 0.22);
            --shadow:    0 2px 20px rgba(15, 23, 42, 0.07);
            --shadow-lg: 0 8px 40px rgba(15, 23, 42, 0.12);
            --white:     #FFFFFF;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Syne', sans-serif;
            background: var(--bg);
            color: var(--navy);
            overflow-x: hidden;
            cursor: none;
        }

        /* Subtle paper texture overlay */
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 512 512' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E");
            opacity: 0.018;
            pointer-events: none;
            z-index: 9999;
        }

        /* CURSOR */
        .cursor {
            width: 10px; height: 10px;
            background: var(--accent);
            border-radius: 50%;
            position: fixed;
            pointer-events: none;
            z-index: 99999;
            transition: transform 0.1s;
            mix-blend-mode: multiply;
        }
        .cursor-ring {
            width: 36px; height: 36px;
            border: 1.5px solid var(--accent);
            border-radius: 50%;
            position: fixed;
            pointer-events: none;
            z-index: 99998;
            transition: all 0.15s ease;
            opacity: 0.35;
        }

        /* NAV */
        nav {
            position: fixed; top: 0; left: 0; width: 100%;
            z-index: 1000;
            padding: 1.4rem 3rem;
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 1px solid transparent;
            transition: all 0.4s;
        }
        nav.scrolled {
            background: rgba(245, 247, 255, 0.94);
            backdrop-filter: blur(16px);
            border-color: var(--border);
            box-shadow: var(--shadow);
        }

        .logo {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.9rem;
            letter-spacing: 3px;
            color: var(--navy);
            text-decoration: none;
        }
        .logo span { color: var(--accent); }

        .nav-links {
            display: flex; gap: 2.5rem; list-style: none;
        }
        .nav-links a {
            color: var(--muted);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: var(--navy); }

        .nav-btns { display: flex; gap: 1rem; align-items: center; }

        .btn {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 0.7rem 1.8rem;
            border-radius: 4px;
            text-decoration: none;
            cursor: none;
            border: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-ghost {
            color: var(--muted);
            background: transparent;
            border: 1px solid rgba(100, 116, 139, 0.3);
        }
        .btn-ghost:hover { color: var(--navy); border-color: var(--navy); }
        .btn-solid {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 4px 18px rgba(37, 99, 235, 0.28);
        }
        .btn-solid:hover {
            background: var(--electric);
            box-shadow: 0 6px 28px rgba(59, 130, 246, 0.38);
            transform: translateY(-2px);
        }

        /* HERO */
        .hero {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            padding: 9rem 3rem 5rem;
            position: relative;
            overflow: hidden;
            gap: 4rem;
        }

        /* Subtle dot grid */
        .hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(37, 99, 235, 0.12) 1px, transparent 1px);
            background-size: 36px 36px;
            pointer-events: none;
            mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 40%, transparent 100%);
        }

        .hero-glow {
            position: absolute;
            width: 800px; height: 800px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.09) 0%, transparent 70%);
            top: -200px; right: -200px;
            pointer-events: none;
        }
        .hero-glow-2 {
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.05) 0%, transparent 70%);
            bottom: 0; left: 10%;
            pointer-events: none;
        }

        .hero-left {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .hero-eyebrow {
            font-family: 'DM Mono', monospace;
            font-size: 0.75rem;
            color: var(--accent);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            opacity: 0;
            animation: rise 0.8s ease forwards 0.3s;
        }
        .hero-eyebrow::before {
            content: '';
            display: inline-block;
            width: 40px; height: 1.5px;
            background: var(--accent);
        }

        .hero h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(5rem, 8vw, 9rem);
            line-height: 0.92;
            letter-spacing: -1px;
            color: var(--navy);
            opacity: 0;
            animation: rise 1s ease forwards 0.5s;
        }
        .hero h1 .outline {
            -webkit-text-stroke: 2px rgba(15, 23, 42, 0.2);
            color: transparent;
        }
        .hero h1 .blue { color: var(--accent); }

        .hero-desc {
            color: var(--muted);
            font-size: 1.05rem;
            line-height: 1.75;
            font-weight: 400;
            margin-top: 1.8rem;
            max-width: 420px;
            opacity: 0;
            animation: rise 1s ease forwards 0.7s;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-top: 2.5rem;
            opacity: 0;
            animation: rise 1s ease forwards 0.9s;
        }

        .btn-large {
            padding: 1rem 2.5rem;
            font-size: 0.9rem;
        }

        /* HERO RIGHT */
        .hero-right {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            animation: rise 1s ease forwards 0.6s;
        }

        .social-panel {
            position: relative;
            width: 100%;
            max-width: 420px;
        }

        .social-cards-scroll {
            max-height: 340px;
            overflow: hidden;
            position: relative;
            border-radius: 14px;
        }

        .social-cards-track {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            animation: scrollCards 22s linear infinite;
        }
        .social-cards-track:hover { animation-play-state: paused; }

        @keyframes scrollCards {
            0% { transform: translateY(0); }
            100% { transform: translateY(-50%); }
        }

        .soc-card {
            background: var(--white);
            border: 1px solid var(--border-2);
            border-radius: 14px;
            padding: 0.9rem 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.9rem;
            box-shadow: var(--shadow);
            flex-shrink: 0;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .soc-card:hover {
            border-color: var(--electric);
            box-shadow: var(--shadow-lg);
        }

        .soc-card-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .ig-bg { background: linear-gradient(135deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); }
        .tt-bg { background: #111; }
        .yt-bg { background: #FF0000; }
        .tw-bg { background: #1DA1F2; }
        .fb-bg { background: #1877F2; }
        .sp-bg { background: #1DB954; }
        .tg-bg { background: #0088cc; }
        .wa-bg { background: #25D366; }
        .li-bg { background: #0A66C2; }
        .res-bg { background: #2563EB; }
        .dc-bg  { background: #7C3AED; }
        .isp-bg { background: #0D9488; }
        .mob-bg { background: #EA580C; }

        .soc-card-info { flex: 1; min-width: 0; }
        .soc-card-name {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 0.1rem;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .soc-card-handle {
            font-family: 'DM Mono', monospace;
            font-size: 0.62rem;
            color: var(--soft);
            letter-spacing: 0.5px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .soc-card-right {
            text-align: right;
            flex-shrink: 0;
            display: flex; flex-direction: column;
            align-items: flex-end; gap: 0.2rem;
        }
        .soc-card-count {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.4rem; line-height: 1;
            color: var(--navy);
        }
        .soc-card-count.green { color: #059669; }
        .soc-card-count.gold  { color: var(--gold); }
        .soc-card-metric {
            font-family: 'DM Mono', monospace;
            font-size: 0.58rem;
            color: var(--soft);
            text-transform: uppercase; letter-spacing: 1px;
        }
        .soc-card-country {
            font-family: 'DM Mono', monospace;
            font-size: 0.55rem;
            color: #059669; letter-spacing: 0.5px;
            display: flex; align-items: center; gap: 0.3rem;
        }
        .soc-card-country::before { content: '▲'; font-size: 0.45rem; }

        /* Fade edges */
        .social-cards-scroll::before,
        .social-cards-scroll::after {
            content: '';
            position: absolute;
            left: 0; right: 0; height: 50px; z-index: 2; pointer-events: none;
        }
        .social-cards-scroll::before { top: 0; background: linear-gradient(to bottom, var(--bg), transparent); }
        .social-cards-scroll::after  { bottom: 0; background: linear-gradient(to top, var(--bg), transparent); }

        /* Orbit icons */
        .orbit-icons {
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            pointer-events: none; z-index: -1;
        }
        .orbit-icon {
            position: absolute;
            width: 38px; height: 38px;
            border-radius: 10px;
            background: var(--white);
            border: 1px solid var(--border-2);
            box-shadow: var(--shadow);
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            color: var(--muted);
        }
        .orbit-icon:nth-child(1) { top: 2%; left: 40%; animation: orbit1 8s ease-in-out infinite; }
        .orbit-icon:nth-child(2) { top: 20%; right: -8%; animation: orbit2 9s ease-in-out infinite; }
        .orbit-icon:nth-child(3) { top: 55%; right: -10%; animation: orbit3 7s ease-in-out infinite; }
        .orbit-icon:nth-child(4) { bottom: 5%; right: 20%; animation: orbit1 10s ease-in-out infinite 1s; }
        .orbit-icon:nth-child(5) { bottom: 15%; left: -8%; animation: orbit2 8s ease-in-out infinite 2s; }
        .orbit-icon:nth-child(6) { top: 38%; left: -10%; animation: orbit3 9s ease-in-out infinite 0.5s; }

        @keyframes orbit1 {
            0%,100% { transform: translate(0,0) rotate(0deg); }
            33%      { transform: translate(8px,-10px) rotate(5deg); }
            66%      { transform: translate(-5px,8px) rotate(-3deg); }
        }
        @keyframes orbit2 {
            0%,100% { transform: translate(0,0); }
            50%      { transform: translate(-10px,-8px); }
        }
        @keyframes orbit3 {
            0%,100% { transform: translate(0,0) scale(1); }
            50%      { transform: translate(6px,10px) scale(1.05); }
        }

        .live-badge {
            position: absolute;
            top: -14px; right: 0px;
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
            font-family: 'DM Mono', monospace;
            font-size: 0.6rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            display: flex; align-items: center; gap: 0.4rem;
            box-shadow: 0 4px 14px rgba(5,150,105,0.35);
            z-index: 3;
        }
        .live-dot {
            width: 6px; height: 6px;
            background: white; border-radius: 50%;
            animation: blink 1.2s ease-in-out infinite;
        }
        @keyframes blink {
            0%,100% { opacity: 1; }
            50%      { opacity: 0.3; }
        }

        /* ACTIVITY FEED */
        .activity-feed {
            margin-top: 1rem;
            background: var(--white);
            border: 1px solid var(--border-2);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        .activity-feed-header {
            padding: 0.6rem 1rem;
            border-bottom: 1px solid rgba(37,99,235,0.1);
            display: flex; align-items: center; justify-content: space-between;
            background: var(--bg-2);
        }
        .activity-feed-header span {
            font-family: 'DM Mono', monospace;
            font-size: 0.6rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--muted);
        }
        .activity-feed-header .green-dot {
            width: 6px; height: 6px;
            background: #059669; border-radius: 50%;
            box-shadow: 0 0 6px #059669;
            animation: blink 1.5s ease-in-out infinite;
        }
        .activity-items {
            max-height: 115px;
            overflow: hidden;
            position: relative;
        }
        .activity-track { display: flex; flex-direction: column; }
        .activity-item {
            padding: 0.55rem 1rem;
            display: flex; align-items: center; gap: 0.6rem;
            border-bottom: 1px solid rgba(37,99,235,0.06);
            animation: slideInActivity 0.4s ease;
        }
        @keyframes slideInActivity {
            from { opacity: 0; transform: translateX(-10px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .activity-icon {
            width: 26px; height: 26px;
            border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem;
            flex-shrink: 0;
        }
        .activity-text {
            flex: 1;
            font-size: 0.7rem;
            color: var(--muted);
            line-height: 1.3;
        }
        .activity-text strong { color: var(--navy); font-weight: 700; }
        .activity-text .hl   { color: #059669; font-weight: 700; }
        .activity-time {
            font-family: 'DM Mono', monospace;
            font-size: 0.55rem;
            color: var(--soft);
            flex-shrink: 0;
        }

        /* Counter bar */
        .counter-bar {
            margin-top: 0.75rem;
            background: var(--white);
            border: 1px solid var(--border-2);
            border-radius: 10px;
            padding: 0.8rem 1.1rem;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: var(--shadow);
        }

        .scroll-hint {
            position: absolute; bottom: 2.5rem; left: 50%;
            transform: translateX(-50%);
            display: flex; flex-direction: column; align-items: center; gap: 0.5rem;
            opacity: 0;
            animation: rise 1s ease forwards 1.2s;
        }
        .scroll-hint span {
            font-family: 'DM Mono', monospace;
            font-size: 0.65rem;
            letter-spacing: 2px;
            color: var(--soft);
            text-transform: uppercase;
        }
        .scroll-line {
            width: 1px; height: 50px;
            background: linear-gradient(to bottom, var(--accent), transparent);
            animation: pulse-line 2s ease-in-out infinite;
        }
        @keyframes pulse-line {
            0%,100% { opacity: 0.3; }
            50%      { opacity: 1; }
        }

        /* TICKER */
        .ticker-wrap {
            border-top: 1px solid var(--border-2);
            border-bottom: 1px solid var(--border-2);
            background: var(--white);
            overflow: hidden;
            padding: 0.9rem 0;
        }
        .ticker {
            display: flex;
            width: max-content;
            animation: ticker 30s linear infinite;
        }
        .ticker-item {
            display: flex; align-items: center; gap: 1rem;
            padding: 0 2rem;
            font-family: 'DM Mono', monospace;
            font-size: 0.75rem;
            letter-spacing: 1px;
            color: var(--soft);
            text-transform: uppercase;
            white-space: nowrap;
        }
        .ticker-item .dot { color: var(--accent); font-size: 0.5rem; }
        @keyframes ticker {
            from { transform: translateX(0); }
            to   { transform: translateX(-50%); }
        }

        /* STATS */
        .stats-section {
            padding: 6rem 3rem;
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0;
        }
        .stat-item {
            padding: 3rem;
            border-right: 1px solid var(--border-2);
            position: relative;
        }
        .stat-item:last-child { border-right: none; }
        .stat-num {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 5rem; line-height: 1;
            color: var(--navy);
            margin-bottom: 0.5rem;
        }
        .stat-num span { color: var(--accent); }
        .stat-label {
            font-size: 0.8rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 600;
        }
        .stat-item::before {
            content: '';
            position: absolute;
            top: 0; left: 3rem; right: 3rem;
            height: 1px; background: var(--border-2);
        }

        /* FEATURES */
        .features {
            padding: 6rem 3rem;
            background: var(--white);
            border-top: 1px solid var(--border-2);
            border-bottom: 1px solid var(--border-2);
        }
        .features-inner { max-width: 1400px; margin: 0 auto; }

        .section-tag {
            font-family: 'DM Mono', monospace;
            font-size: 0.7rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 3rem;
            display: flex; align-items: center; gap: 1rem;
        }
        .section-tag::before {
            content: '';
            display: inline-block;
            width: 30px; height: 1.5px;
            background: var(--accent);
        }

        .features-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1px;
            background: var(--border-2);
            border: 1px solid var(--border-2);
            border-radius: 8px;
            overflow: hidden;
        }
        .feat-card {
            background: var(--white);
            padding: 3rem;
            transition: background 0.3s;
            position: relative;
            overflow: hidden;
        }
        .feat-card:hover { background: var(--bg); }
        .feat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 3px; height: 0;
            background: var(--accent);
            transition: height 0.4s;
        }
        .feat-card:hover::before { height: 100%; }

        .feat-num {
            font-family: 'DM Mono', monospace;
            font-size: 0.7rem;
            color: var(--soft);
            letter-spacing: 2px;
            margin-bottom: 1.5rem;
        }
        .feat-icon {
            font-size: 1.5rem;
            color: var(--accent);
            margin-bottom: 1rem;
        }
        .feat-card h3 {
            font-size: 1.2rem; font-weight: 800;
            margin-bottom: 0.8rem;
            color: var(--navy);
        }
        .feat-card p {
            color: var(--muted);
            font-size: 0.92rem;
            line-height: 1.65;
            font-weight: 400;
        }

        /* PLATFORMS */
        .platforms-section {
            padding: 6rem 3rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        .platforms-header {
            display: flex; justify-content: space-between; align-items: flex-end;
            margin-bottom: 4rem;
            border-bottom: 1px solid var(--border-2);
            padding-bottom: 2rem;
        }
        .platforms-header h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(3rem, 6vw, 5rem); line-height: 1;
            color: var(--navy);
        }
        .platforms-header p {
            color: var(--muted);
            font-size: 0.9rem; max-width: 300px;
            text-align: right; line-height: 1.6;
        }

        .platforms-grid {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 1px;
            background: var(--border-2);
            border: 1px solid var(--border-2);
            border-radius: 8px;
            overflow: hidden;
        }
        .platform-cell {
            background: var(--white);
            padding: 2rem 1rem;
            display: flex; flex-direction: column;
            align-items: center; gap: 0.6rem;
            transition: all 0.3s;
            text-decoration: none;
        }
        .platform-cell:hover { background: var(--bg-2); }
        .platform-cell i {
            font-size: 1.6rem;
            color: var(--soft);
            transition: color 0.3s;
        }
        .platform-cell:hover i { color: var(--accent); }
        .platform-cell span {
            font-size: 0.65rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--soft);
            font-weight: 600;
            font-family: 'DM Mono', monospace;
        }

        /* PARTNER */
        .partner-section {
            padding: 6rem 3rem;
            background: var(--bg-2);
            border-top: 1px solid var(--border-2);
        }
        .partner-inner {
            max-width: 1400px; margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6rem; align-items: center;
        }
        .partner-left h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(3rem, 5vw, 4.5rem);
            line-height: 1.0; margin: 1rem 0 1.5rem;
            color: var(--navy);
        }
        .partner-left p {
            color: var(--muted);
            line-height: 1.7; font-size: 0.95rem;
            margin-bottom: 2rem;
        }
        .partner-badges {
            display: flex; flex-wrap: wrap; gap: 0.6rem; margin-bottom: 2.5rem;
        }
        .badge {
            font-family: 'DM Mono', monospace;
            font-size: 0.65rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 0.4rem 0.9rem;
            border: 1px solid var(--border-2);
            border-radius: 3px;
            color: var(--muted);
            background: var(--white);
        }
        .partner-right { position: relative; }
        .partner-card {
            background: var(--white);
            border: 1px solid var(--border-2);
            border-radius: 12px;
            padding: 3rem;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }
        .partner-card::before {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(circle at top right, rgba(37,99,235,0.04), transparent 60%);
            pointer-events: none;
        }
        .partner-logo {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 3.5rem; letter-spacing: 4px;
            color: var(--navy); margin-bottom: 0.5rem;
        }
        .partner-logo span { color: var(--accent); }
        .partner-card p {
            color: var(--muted);
            font-size: 0.85rem; line-height: 1.6; margin-bottom: 2rem;
        }
        .partner-card .btn-solid { width: 100%; text-align: center; }

        /* CTA */
        .cta-section {
            padding: 8rem 3rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            background: var(--navy);
        }
        .cta-section::before {
            content: '';
            position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 900px; height: 600px;
            background: radial-gradient(ellipse, rgba(37,99,235,0.18) 0%, transparent 70%);
            pointer-events: none;
        }
        .cta-section h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(4rem, 10vw, 9rem);
            line-height: 0.9; letter-spacing: -1px;
            margin-bottom: 2rem;
            color: #fff;
        }
        .cta-section h2 .outline {
            -webkit-text-stroke: 1px rgba(255,255,255,0.25);
            color: transparent;
        }
        .cta-section p {
            color: #94A3B8;
            font-size: 1rem;
            max-width: 450px; margin: 0 auto 3rem;
            line-height: 1.7;
        }

        /* FOOTER */
        footer {
            background: var(--navy-2);
            border-top: 1px solid rgba(255,255,255,0.07);
            padding: 4rem 3rem 2rem;
        }
        .footer-top {
            max-width: 1400px; margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 4rem;
            padding-bottom: 3rem;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .footer-brand .logo-text {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2rem; letter-spacing: 3px;
            margin-bottom: 1rem; display: block;
            color: #fff;
        }
        .footer-brand .logo-text span { color: var(--electric); }
        .footer-brand p {
            color: #64748B;
            font-size: 0.88rem; line-height: 1.65;
            max-width: 280px; margin-bottom: 1.5rem;
        }
        .footer-socials { display: flex; gap: 0.6rem; }
        .soc-btn {
            width: 38px; height: 38px;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 4px;
            display: flex; align-items: center; justify-content: center;
            color: #64748B;
            text-decoration: none; font-size: 0.9rem;
            transition: all 0.3s;
        }
        .soc-btn:hover { border-color: var(--electric); color: var(--electric); }

        .footer-col h4 {
            font-size: 0.7rem; letter-spacing: 2.5px;
            text-transform: uppercase;
            color: #fff; margin-bottom: 1.5rem; font-weight: 700;
        }
        .footer-col ul { list-style: none; }
        .footer-col li { margin-bottom: 0.8rem; }
        .footer-col a {
            color: #64748B; text-decoration: none;
            font-size: 0.88rem; transition: color 0.3s;
        }
        .footer-col a:hover { color: #fff; }
        .footer-col .contact-item {
            color: #64748B;
            font-size: 0.85rem;
            display: flex; align-items: flex-start;
            gap: 0.6rem; margin-bottom: 0.8rem;
        }
        .footer-col .contact-item i { color: var(--electric); margin-top: 2px; flex-shrink: 0; }

        .footer-bottom {
            max-width: 1400px; margin: 2rem auto 0;
            display: flex; justify-content: space-between;
            align-items: center; flex-wrap: wrap; gap: 1rem;
        }
        .footer-bottom p {
            color: #475569;
            font-size: 0.78rem;
            font-family: 'DM Mono', monospace;
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .reveal {
            opacity: 0; transform: translateY(24px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }
        .reveal.visible { opacity: 1; transform: translateY(0); }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 900px) {
            nav { padding: 1.2rem 1.5rem; }
            .nav-links { display: none; }
            .hero { grid-template-columns: 1fr; padding: 7rem 1.5rem 4rem; gap: 3rem; }
            .hero h1 { font-size: clamp(3.5rem, 14vw, 7rem); }
            .hero-right { display: none; }
            .stats-section { grid-template-columns: 1fr; padding: 3rem 1.5rem; }
            .stat-item { border-right: none; border-bottom: 1px solid var(--border-2); padding: 2rem 1.5rem; }
            .features { padding: 4rem 1.5rem; }
            .features-layout { grid-template-columns: 1fr; }
            .platforms-section { padding: 4rem 1.5rem; }
            .platforms-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
            .platforms-header p { text-align: left; }
            .platforms-grid { grid-template-columns: repeat(4, 1fr); }
            .partner-section { padding: 4rem 1.5rem; }
            .partner-inner { grid-template-columns: 1fr; gap: 3rem; }
            .cta-section { padding: 5rem 1.5rem; }
            footer { padding: 3rem 1.5rem 2rem; }
            .footer-top { grid-template-columns: 1fr 1fr; gap: 2rem; }
            .footer-bottom { flex-direction: column; align-items: flex-start; }
        }
        @media (max-width: 500px) {
            .platforms-grid { grid-template-columns: repeat(3, 1fr); }
            .footer-top { grid-template-columns: 1fr; }
        }
        @media (max-width: 600px) {
            /* Stack header text left on mobile */
            .reseller-hdr-p { text-align: left !important; max-width: 100% !important; }
            /* Section padding */
            .reseller-section { padding: 4rem 1.2rem !important; }
        }
        </style>
</head>
<body>

    <div class="cursor" id="cursor"></div>
    <div class="cursor-ring" id="cursorRing"></div>

    <nav id="nav">
        <a href="#" class="logo">Prox<span>World</span></a>
        <ul class="nav-links">
            <li><a href="#features">Features</a></li>
            <li><a href="#platforms">Platforms</a></li>
            <li><a href="{{ route('api.docs') }}">Api</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
        <div class="nav-btns">
            <a href="{{ route('login') }}" class="btn btn-ghost">Login</a>
            <a href="{{ route('register') }}" class="btn btn-solid">Get Started</a>
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
                Residential, datacenter, ISP and mobile proxies across 190+ countries.
                Fast, reliable, and built for scraping, automation, and anonymity at scale.
            </p>
            <div class="hero-actions">
                <a href="{{ route('register') }}" class="btn btn-solid btn-large">Get Proxies</a>
                <a href="#features" class="btn btn-ghost btn-large">See How</a>
            </div>
        </div>

        <div class="hero-right">
            <div class="social-panel">
                <div class="orbit-icons">
                    <div class="orbit-icon"><i class="fas fa-house-user"></i></div>
                    <div class="orbit-icon"><i class="fas fa-server"></i></div>
                    <div class="orbit-icon"><i class="fas fa-network-wired"></i></div>
                    <div class="orbit-icon"><i class="fas fa-mobile-screen"></i></div>
                    <div class="orbit-icon"><i class="fas fa-globe"></i></div>
                    <div class="orbit-icon"><i class="fas fa-shield-halved"></i></div>
                </div>

                <div class="live-badge">
                    <div class="live-dot"></div>
                    Live Network
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
                        <span style="font-family:'DM Mono',monospace;font-size:0.68rem;letter-spacing:1px;color:var(--muted);text-transform:uppercase;">Proxies provisioned today</span>
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
            <div class="platform-cell"><span style="font-size:1.6rem;">🇫🇷</span><span>France</span></div>
            <div class="platform-cell"><span style="font-size:1.6rem;">🇯🇵</span><span>Japan</span></div>
            <div class="platform-cell"><i class="fas fa-earth-americas"></i><span>+ many more</span></div>
        </div>
    </section>

<!-- RESELLER / API SECTION -->
<section style="padding: 6rem 3rem; background: var(--navy); position: relative; overflow: hidden;">
    
    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:900px;height:500px;background:radial-gradient(ellipse,rgba(37,99,235,0.15) 0%,transparent 70%);pointer-events:none;"></div>
    <div style="position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,0.04) 1px,transparent 1px);background-size:36px 36px;pointer-events:none;"></div>

    <div style="max-width:1400px;margin:0 auto;position:relative;z-index:1;">

        <div style="margin-bottom:4rem;">
            <div class="section-tag reveal" style="color:#60A5FA;">
                <span style="background:#60A5FA;"></span>
                For Resellers & Developers
            </div>
            <div style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:2rem;border-bottom:1px solid rgba(255,255,255,0.08);padding-bottom:2rem;">
                <h2 class="reveal" style="font-family:'Bebas Neue',sans-serif;font-size:clamp(2.5rem,6vw,5.5rem);line-height:1;color:#fff;">
                    Build On<br>
                    <span style="-webkit-text-stroke:1px rgba(255,255,255,0.2);color:transparent;">ProxWorld</span>
                </h2>
                <p class="reveal" style="color:#94A3B8;font-size:0.95rem;max-width:380px;line-height:1.7;text-align:right;">
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
                        <span style="color:#94A3B8;font-size:clamp(0.8rem, 1.5vw, 0.85rem);">Manage customers & transactions</span>
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
                    <div><span style="color:#8B5CF6;">POST</span> <span style="color:#60A5FA;">{{ url('/api/v2') }}</span></div>
                    <div style="margin-top:0.5rem;"><span style="color:#94A3B8;">action</span> <span style="color:#475569;">=</span> <span style="color:#10B981;">"services"</span></div>
                    <div><span style="color:#94A3B8;">key</span> <span style="color:#475569;">=</span> <span style="color:#10B981;">"bst_xxxxxxxxxxxx"</span></div>
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

    <section class="partner-section">
        <div class="partner-inner">
            <div class="partner-left reveal">
                <div class="section-tag">Partner Services</div>
                <h2>Buy &amp; Sell<br>Verified Accounts</h2>
                <p>Need an established account to jumpstart your presence? Or ready to sell your aged profiles? accpond is our trusted marketplace partner for safe, verified account transactions.</p>
                <div class="partner-badges">
                    <span class="badge">Verified Accounts</span>
                    <span class="badge">Secure Transactions</span>
                    <span class="badge">Instant Delivery</span>
                    <span class="badge">24/7 Support</span>
                </div>
                <a href="https://www.accpond.com.ng" target="_blank" class="btn btn-solid btn-large">
                    Visit accpond &nbsp;<i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="partner-right reveal">
                <div class="partner-card">
                    <div class="partner-logo">Acco<span>pond</span></div>
                    <p>Nigeria's trusted marketplace for buying and selling social media accounts. Thousands of verified buyers and sellers, every transaction protected.</p>
                    <a href="https://www.accpond.com.ng" target="_blank" class="btn btn-solid" style="display:block; text-align:center;">
                        Go to accpond.com.ng
                    </a>
                    <div style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid var(--border-2);display:flex;gap:0.5rem;align-items:center;">
                        <i class="fas fa-shield-alt" style="color:var(--accent);font-size:0.8rem;"></i>
                        <span style="font-size:0.75rem;color:var(--muted);font-family:'DM Mono',monospace;letter-spacing:1px;">TRUSTED BY THOUSANDS WORLDWIDE</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <h2 class="reveal">
            Go<br>
            <span class="outline">Anonymous</span><br>
            Today
        </h2>
        <p class="reveal">Join 50,000+ developers, marketers, and businesses running on ProxWorld's network.</p>
        <a href="{{ route('register') }}" class="btn btn-solid btn-large reveal">Create Free Account</a>
    </section>

    <footer id="contact">
        <div class="footer-top">
            <div class="footer-brand">
                <span class="logo-text">Prox<span>World</span></span>
                <p>Your trusted proxy provider worldwide. Safe, fast, and built for results.</p>
                <div class="footer-socials">
                    <a href="https://whatsapp.com/channel/0029Vb7I1e3JuyAL31rIia0r" target="_blank" class="soc-btn"><i class="fab fa-whatsapp"></i></a>
                    <a href="https://www.tiktok.com/@ProxWorld3928" target="_blank" class="soc-btn"><i class="fab fa-tiktok"></i></a>
                    <a href="#" class="soc-btn"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="soc-btn"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h4>Navigate</h4>
                <ul>
                    <li><a href="{{ route('login') }}">Login</a></li>
                    <li><a href="{{ route('register') }}">Sign Up</a></li>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#platforms">Platforms</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Legal</h4>
                <ul>
                    <li><a href="/faq">FAQ</a></li>
                    <li><a href="/terms-of-use">Terms of Use</a></li>
                    <li><a href="/refund-policy">Refund Policy</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Contact</h4>
                <div class="contact-item"><i class="fas fa-envelope"></i> info@proxworld.xyz</div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2025 ProxWorld. All rights reserved.</p>
            <p>ProxWorld is operated by Phastverse Enterprise.</p>
        </div>
    </footer>

    <script>
    const socialCards = [
        { icon: 'fas fa-house-user', bg: 'res-bg', name: 'Tolani Adewale', handle: 'Residential Proxy', metric: 'Bandwidth', count: '50 GB', countColor: 'green', gained: '+50GB', from: '🇳🇬 Nigeria' },
        { icon: 'fas fa-server', bg: 'dc-bg', name: 'Marcus Chen', handle: 'Datacenter Proxy', metric: 'IPs', count: '100', countColor: 'green', gained: '+100 IPs', from: '🇺🇸 USA' },
        { icon: 'fas fa-network-wired', bg: 'isp-bg', name: 'Amira Hassan', handle: 'ISP Proxy', metric: 'IPs', count: '25', countColor: 'green', gained: '+25 IPs', from: '🇦🇪 UAE' },
        { icon: 'fas fa-mobile-screen', bg: 'mob-bg', name: 'Jake Morrison', handle: 'Mobile Proxy', metric: 'Bandwidth', count: '10 GB', countColor: 'green', gained: '+10GB', from: '🇬🇧 UK' },
        { icon: 'fas fa-house-user', bg: 'res-bg', name: 'Chidinma Obi', handle: 'Residential Proxy', metric: 'Bandwidth', count: '200 GB', countColor: 'gold', gained: '+200GB', from: '🇳🇬 Nigeria' },
        { icon: 'fas fa-server', bg: 'dc-bg', name: 'Sofia Reyes', handle: 'Datacenter Proxy', metric: 'IPs', count: '500', countColor: 'green', gained: '+500 IPs', from: '🇲🇽 Mexico' },
        { icon: 'fas fa-network-wired', bg: 'isp-bg', name: 'David Okonkwo', handle: 'ISP Proxy', metric: 'IPs', count: '50', countColor: 'green', gained: '+50 IPs', from: '🇬🇭 Ghana' },
        { icon: 'fas fa-mobile-screen', bg: 'mob-bg', name: 'Luca Ferri', handle: 'Mobile Proxy', metric: 'Bandwidth', count: '20 GB', countColor: 'green', gained: '+20GB', from: '🇮🇹 Italy' },
        { icon: 'fas fa-house-user', bg: 'res-bg', name: 'Priya Kapoor', handle: 'Residential Proxy', metric: 'Bandwidth', count: '75 GB', countColor: 'green', gained: '+75GB', from: '🇮🇳 India' },
        { icon: 'fas fa-server', bg: 'dc-bg', name: 'Kwame Asante', handle: 'Datacenter Proxy', metric: 'IPs', count: '1,000', countColor: 'gold', gained: '+1K IPs', from: '🇬🇭 Ghana' },
        { icon: 'fas fa-network-wired', bg: 'isp-bg', name: 'Fatima Al-Rashid', handle: 'ISP Proxy', metric: 'IPs', count: '30', countColor: 'green', gained: '+30 IPs', from: '🇸🇦 Saudi Arabia' },
        { icon: 'fas fa-house-user', bg: 'res-bg', name: 'Ryan Brooks', handle: 'Residential Proxy', metric: 'Bandwidth', count: '150 GB', countColor: 'gold', gained: '+150GB', from: '🇺🇸 USA' },
        { icon: 'fas fa-mobile-screen', bg: 'mob-bg', name: 'Yemi Daniels', handle: 'Mobile Proxy', metric: 'Bandwidth', count: '15 GB', countColor: 'green', gained: '+15GB', from: '🇳🇬 Nigeria' },
        { icon: 'fas fa-server', bg: 'dc-bg', name: 'Ling Wei', handle: 'Datacenter Proxy', metric: 'IPs', count: '2,000', countColor: 'gold', gained: '+2K IPs', from: '🇨🇳 China' },
        { icon: 'fas fa-house-user', bg: 'res-bg', name: "Emeka's Team", handle: 'Residential Proxy', metric: 'Bandwidth', count: '500 GB', countColor: 'green', gained: '+500GB', from: '🌍 Worldwide' },
        { icon: 'fas fa-network-wired', bg: 'isp-bg', name: 'Isabella Moreno', handle: 'ISP Proxy', metric: 'IPs', count: '40', countColor: 'gold', gained: '+40 IPs', from: '🇧🇷 Brazil' },
        { icon: 'fas fa-house-user', bg: 'res-bg', name: 'Tunde Marketing', handle: 'Residential Proxy', metric: 'Bandwidth', count: '300 GB', countColor: 'green', gained: '+300GB', from: '🌍 Multi-Country' },
        { icon: 'fas fa-mobile-screen', bg: 'mob-bg', name: 'Emma Johansson', handle: 'Mobile Proxy', metric: 'Bandwidth', count: '25 GB', countColor: 'green', gained: '+25GB', from: '🇸🇪 Sweden' },
        { icon: 'fas fa-server', bg: 'dc-bg', name: 'Ahmed Yusuf', handle: 'Datacenter Proxy', metric: 'IPs', count: '150', countColor: 'green', gained: '+150 IPs', from: '🇰🇪 Kenya' },
        { icon: 'fas fa-house-user', bg: 'res-bg', name: 'Carlos Mendez', handle: 'Residential Proxy', metric: 'Bandwidth', count: '400 GB', countColor: 'gold', gained: '+400GB', from: '🇦🇷 Argentina' },
    ];

    function shuffle(arr) {
        const a = [...arr];
        for (let i = a.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [a[i], a[j]] = [a[j], a[i]];
        }
        return a;
    }

    function buildCard(card) {
        return `
        <div class="soc-card">
            <div class="soc-card-icon ${card.bg}"><i class="${card.icon}" style="color:#fff"></i></div>
            <div class="soc-card-info">
                <div class="soc-card-name">${card.name}</div>
                <div class="soc-card-handle">${card.handle}</div>
            </div>
            <div class="soc-card-right">
                <div class="soc-card-count ${card.countColor}">${card.count}</div>
                <div class="soc-card-metric">${card.metric}</div>
                <div class="soc-card-country">${card.gained} added</div>
            </div>
        </div>`;
    }

    const track = document.getElementById('cardsTrack');
    const shuffled = shuffle(socialCards);
    const doubled = [...shuffled, ...shuffled];
    track.innerHTML = doubled.map(buildCard).join('');

    // ACTIVITY FEED
    const activityEvents = [
        { icon: 'fas fa-house-user', bg: '#2563EB', text: '<strong>Tolani A.</strong> just provisioned <span class="hl">+50GB residential</span> from 🇳🇬 Nigeria' },
        { icon: 'fas fa-server', bg: '#7C3AED', text: '<strong>Emeka B.</strong> received <span class="hl">+100 datacenter IPs</span> from 🇺🇸 USA' },
        { icon: 'fas fa-mobile-screen', bg: '#EA580C', text: '<strong>Sofia R.</strong> activated <span class="hl">+10GB mobile</span> proxy from 🇲🇽 Mexico' },
        { icon: 'fas fa-network-wired', bg: '#0D9488', text: '<strong>KwameBuilds</strong> got <span class="hl">+25 ISP IPs</span> from 🇬🇧 UK' },
        { icon: 'fas fa-house-user', bg: '#2563EB', text: '<strong>Ryan B.</strong> provisioned <span class="hl">+150GB residential</span> from 🇺🇸 United States' },
        { icon: 'fas fa-server', bg: '#7C3AED', text: '<strong>Chidinma O.</strong> received <span class="hl">+200 datacenter IPs</span> instantly' },
        { icon: 'fas fa-network-wired', bg: '#0D9488', text: '<strong>Fatima A.</strong> added <span class="hl">+30 ISP IPs</span> from 🇦🇪 UAE' },
        { icon: 'fas fa-mobile-screen', bg: '#EA580C', text: '<strong>Luca F.</strong> got <span class="hl">+20GB mobile</span> from 🇮🇹 Italy' },
        { icon: 'fas fa-house-user', bg: '#2563EB', text: '<strong>Tunde M.</strong> activated <span class="hl">+300GB residential</span> from 🇬🇧 UK' },
        { icon: 'fas fa-server', bg: '#7C3AED', text: '<strong>Priya K.</strong> provisioned <span class="hl">+500 datacenter IPs</span> from 🇮🇳 India' },
        { icon: 'fas fa-house-user', bg: '#2563EB', text: '<strong>Marcus C.</strong> got <span class="hl">+75GB residential</span> from 🇺🇸 USA' },
        { icon: 'fas fa-network-wired', bg: '#0D9488', text: '<strong>David O.</strong> received <span class="hl">+50 ISP IPs</span> from 🇬🇭 Ghana' },
        { icon: 'fas fa-mobile-screen', bg: '#EA580C', text: '<strong>Yemi D.</strong> activated <span class="hl">+15GB mobile</span> proxy within minutes' },
        { icon: 'fas fa-server', bg: '#7C3AED', text: '<strong>Emma J.</strong> received <span class="hl">+1,000 datacenter IPs</span> from 🇸🇪 Sweden' },
        { icon: 'fas fa-house-user', bg: '#2563EB', text: '<strong>Aisha Group</strong> provisioned <span class="hl">+500GB residential</span> from 🇿🇦 South Africa' },
        { icon: 'fas fa-network-wired', bg: '#0D9488', text: '<strong>Jake M.</strong> got <span class="hl">+40 ISP IPs</span> from 🇬🇧 UK' },
    ];

    let activityIndex = 0;
    const activityTrack = document.getElementById('activityTrack');
    function timeAgo() {
        const opts = ['just now', '1m ago', '2m ago', '3m ago', '5m ago'];
        return opts[Math.floor(Math.random() * opts.length)];
    }
    function addActivity() {
        const event = activityEvents[activityIndex % activityEvents.length];
        activityIndex++;
        const item = document.createElement('div');
        item.className = 'activity-item';
        item.innerHTML = `
            <div class="activity-icon" style="background:${event.bg}18;border:1px solid ${event.bg}30">
                <i class="${event.icon}" style="color:${event.bg}"></i>
            </div>
            <div class="activity-text">${event.text}</div>
            <div class="activity-time">${timeAgo()}</div>
        `;
        activityTrack.insertBefore(item, activityTrack.firstChild);
        while (activityTrack.children.length > 4) {
            activityTrack.removeChild(activityTrack.lastChild);
        }
    }
    addActivity(); addActivity(); addActivity();
    setInterval(addActivity, 2600);

    // ORDER COUNTER
    const counterEl = document.getElementById('order-counter');
    let count = 2100 + Math.floor(Math.random() * 400);
    counterEl.textContent = count.toLocaleString();
    setInterval(() => {
        if (Math.random() > 0.35) {
            count += Math.floor(Math.random() * 4) + 1;
            counterEl.textContent = count.toLocaleString();
        }
    }, 2800);

    // CURSOR
    const cursor = document.getElementById('cursor');
    const ring = document.getElementById('cursorRing');
    let mx = 0, my = 0, rx = 0, ry = 0;
    document.addEventListener('mousemove', e => {
        mx = e.clientX; my = e.clientY;
        cursor.style.left = mx - 5 + 'px';
        cursor.style.top  = my - 5 + 'px';
    });
    function animRing() {
        rx += (mx - rx) * 0.12;
        ry += (my - ry) * 0.12;
        ring.style.left = rx - 18 + 'px';
        ring.style.top  = ry - 18 + 'px';
        requestAnimationFrame(animRing);
    }
    animRing();
    document.querySelectorAll('a, button').forEach(el => {
        el.addEventListener('mouseenter', () => { cursor.style.transform = 'scale(2)'; ring.style.transform = 'scale(1.4)'; ring.style.opacity = '0.6'; });
        el.addEventListener('mouseleave', () => { cursor.style.transform = 'scale(1)';  ring.style.transform = 'scale(1)';   ring.style.opacity = '0.35'; });
    });

    // NAV SCROLL
    const nav = document.getElementById('nav');
    window.addEventListener('scroll', () => { nav.classList.toggle('scrolled', window.scrollY > 60); });

    // REVEAL
    const reveals = document.querySelectorAll('.reveal');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                setTimeout(() => entry.target.classList.add('visible'), i * 80);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    reveals.forEach(el => observer.observe(el));

    // SMOOTH SCROLL
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const target = document.querySelector(a.getAttribute('href'));
            if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        });
    });
    </script>
</body>
</html>