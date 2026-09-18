<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->subject }} — ProxWorld Blog</title>
    <meta name="description" content="{{ $post->excerpt ?? Str::limit(strip_tags($post->body), 160) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Syne:wght@400;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @php
        $ogImage = $post->featured_image_url ?? asset('assets/images/LOGO.png');
        $ogDescription = $post->excerpt ?? Str::limit(strip_tags($post->body), 160);
    @endphp

    <link rel="canonical" href="{{ route('blog.show', $post->slug) }}">

    <!-- Open Graph / Facebook, WhatsApp, Telegram, LinkedIn -->
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="ProxWorld">
    <meta property="og:url" content="{{ route('blog.show', $post->slug) }}">
    <meta property="og:title" content="{{ $post->subject }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="article:published_time" content="{{ $post->sent_at->toIso8601String() }}">
    @if($post->creator)
        <meta property="article:author" content="{{ $post->creator->name }}">
    @endif

    <link rel="shortcut icon" type="image/x-icon" href="{{ $ogImage }}" />

    <!-- Twitter/X Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $post->subject }}">
    <meta name="twitter:description" content="{{ $ogDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
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
        body { font-family: 'Syne', sans-serif; background: var(--bg); color: var(--navy); }

        @keyframes rise {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        nav {
            position: sticky; top: 0; z-index: 1000;
            padding: 1.4rem 3rem;
            display: flex; justify-content: space-between; align-items: center;
            background: rgba(245, 247, 255, 0.94);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
        }
        .logo { font-family: 'Bebas Neue', sans-serif; font-size: 1.9rem; letter-spacing: 3px; color: var(--navy); text-decoration: none; }
        .logo span { color: var(--accent); }
        .nav-links { display: flex; gap: 2.5rem; list-style: none; }
        .nav-links a { color: var(--muted); text-decoration: none; font-size: 0.85rem; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; }
        .nav-links a:hover, .nav-links a.active { color: var(--navy); }
        .nav-btns { display: flex; gap: 1rem; align-items: center; }
        .btn { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 0.8rem; letter-spacing: 1.5px; text-transform: uppercase; padding: 0.7rem 1.8rem; border-radius: 4px; text-decoration: none; border: none; display: inline-block; transition: all 0.3s; }
        .btn-ghost { color: var(--muted); background: transparent; border: 1px solid rgba(100, 116, 139, 0.3); }
        .btn-ghost:hover { color: var(--navy); border-color: var(--navy); }
        .btn-solid { background: var(--accent); color: #fff; box-shadow: 0 4px 18px rgba(37, 99, 235, 0.28); }
        .btn-solid:hover { background: var(--electric); transform: translateY(-2px); }

        .post-wrap { max-width: 760px; margin: 0 auto; padding: 4rem 2rem 2rem; }

        .post-breadcrumb {
            font-family: 'DM Mono', monospace; font-size: 0.72rem;
            color: var(--soft); letter-spacing: 1px; text-transform: uppercase;
            margin-bottom: 1.5rem;
            opacity: 0; animation: rise 0.6s ease forwards;
        }
        .post-breadcrumb a { color: var(--accent); text-decoration: none; }

        .post-meta {
            font-family: 'DM Mono', monospace; font-size: 0.72rem;
            color: var(--soft); letter-spacing: 1px; text-transform: uppercase;
            margin-bottom: 1rem;
            opacity: 0; animation: rise 0.6s ease forwards 0.1s;
        }

        .post-wrap h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(2.4rem, 5vw, 3.8rem);
            line-height: 1.02;
            margin-bottom: 2rem;
            opacity: 0; animation: rise 0.7s ease forwards 0.2s;
        }

        .post-featured-image {
            width: 100%; aspect-ratio: 16/9;
            border-radius: 14px;
            background: var(--bg-2) center/cover no-repeat;
            margin-bottom: 2.5rem;
            box-shadow: var(--shadow);
            opacity: 0; animation: rise 0.8s ease forwards 0.3s;
        }

        .post-content {
            font-size: 1.05rem; line-height: 1.85; color: var(--navy);
            opacity: 0; animation: rise 0.8s ease forwards 0.4s;
        }
        .post-content p { margin-bottom: 1.4rem; }
        .post-content h2 { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.5rem; margin: 2.2rem 0 1rem; }
        .post-content h3 { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 1.2rem; margin: 1.8rem 0 0.8rem; }
        .post-content ul, .post-content ol { margin: 0 0 1.4rem 1.4rem; }
        .post-content li { margin-bottom: 0.5rem; }
        .post-content a { color: var(--accent); }
        .post-content img { max-width: 100%; border-radius: 10px; margin: 1.5rem 0; }
        .post-content blockquote {
            border-left: 3px solid var(--accent);
            padding-left: 1.2rem; margin: 1.5rem 0;
            color: var(--muted); font-style: italic;
        }

        .related-section { max-width: 1100px; margin: 3rem auto 0; padding: 3rem 2rem; border-top: 1px solid var(--border); }
        .related-section h3 {
            font-family: 'Bebas Neue', sans-serif; font-size: 1.8rem;
            margin-bottom: 1.5rem; text-align: center;
        }
        .related-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
        @media (max-width: 800px) { .related-grid { grid-template-columns: 1fr; } }

        .related-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 12px; overflow: hidden; text-decoration: none; color: inherit;
            box-shadow: var(--shadow); transition: transform 0.3s, box-shadow 0.3s;
        }
        .related-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }
        .related-card-image { width: 100%; aspect-ratio: 16/10; background: var(--bg-2) center/cover no-repeat; }
        .related-card-body { padding: 1.1rem; }
        .related-card-title { font-weight: 700; font-size: 0.95rem; line-height: 1.4; }

         @media (max-width: 900px) {
            nav { padding: 1.1rem 1.5rem; }
            .nav-links { display: none; }
            .nav-btns { gap: 0.6rem; }
            .btn { padding: 0.6rem 1.2rem; font-size: 0.72rem; }
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
            footer { padding: 3rem 1.5rem 2rem; }
            .footer-top { grid-template-columns: 1fr 1fr; gap: 2rem; }
            .footer-bottom { flex-direction: column; align-items: flex-start; }
        }
        @media (max-width: 500px) {
            .platforms-grid { grid-template-columns: repeat(3, 1fr); }
            .footer-top { grid-template-columns: 1fr; }
        }

                .share-bar {
            display: flex; align-items: center; gap: 0.6rem;
            margin-bottom: 1.8rem;
            opacity: 0; animation: rise 0.6s ease forwards 0.15s;
        }
        .share-label {
            font-family: 'DM Mono', monospace; font-size: 0.7rem;
            color: var(--soft); letter-spacing: 1px; text-transform: uppercase;
            margin-right: 0.2rem;
        }
        .share-btn {
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid var(--border-2);
            border-radius: 50%;
            background: var(--surface);
            color: var(--muted);
            font-size: 0.9rem;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.25s;
            position: relative;
        }
        .share-btn:hover { border-color: var(--electric); color: var(--electric); transform: translateY(-2px); box-shadow: var(--shadow); }
        .share-btn.copied::after {
            content: 'Copied!';
            position: absolute; top: -32px; left: 50%; transform: translateX(-50%);
            background: var(--navy); color: #fff;
            font-size: 0.65rem; font-family: 'DM Mono', monospace;
            padding: 0.3rem 0.6rem; border-radius: 4px;
            white-space: nowrap;
        }
    </style>
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

    <nav>
        <a href="/" class="logo">Prox<span>World</span></a>
        <ul class="nav-links">
            <li><a href="{{ route('welcome') }}#features">Features</a></li>
            <li><a href="{{ route('welcome') }}#platforms">Platforms</a></li>
            <li><a href="{{ route('api.docs') }}">Api</a></li>
            <li><a href="{{ route('blog.index') }}">Blog</a></li>
            <li><a href="{{ route('welcome') }}#contact">Contact</a></li>
        </ul>
        <div class="nav-btns">
            <a href="/login" class="btn btn-ghost">Login</a>
            <a href="/register" class="btn btn-solid">Get Started</a>
        </div>
    </nav>

    <article class="post-wrap">
        <div class="post-breadcrumb"><a href="{{ route('blog.index') }}">← Back to Blog</a></div>

        <div class="post-meta">
            {{ $post->sent_at->format('F d, Y') }}
            @if($post->creator) &middot; By {{ $post->creator->name }} @endif
        </div>

        <div class="share-bar">
            <span class="share-label">Share:</span>
            <a href="https://api.whatsapp.com/send?text={{ urlencode($post->subject.' - '.route('blog.show', $post->slug)) }}" target="_blank" rel="noopener" class="share-btn" title="Share on WhatsApp">
                <i class="fab fa-whatsapp"></i>
            </a>
            <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->subject) }}&url={{ urlencode(route('blog.show', $post->slug)) }}" target="_blank" rel="noopener" class="share-btn" title="Share on X">
                <i class="fab fa-twitter"></i>
            </a>
            <a href="https://t.me/share/url?url={{ urlencode(route('blog.show', $post->slug)) }}&text={{ urlencode($post->subject) }}" target="_blank" rel="noopener" class="share-btn" title="Share on Telegram">
                <i class="fab fa-telegram"></i>
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.show', $post->slug)) }}" target="_blank" rel="noopener" class="share-btn" title="Share on Facebook">
                <i class="fab fa-facebook-f"></i>
            </a>
            <button type="button" class="share-btn" id="copy-link-btn" title="Copy link" data-url="{{ route('blog.show', $post->slug) }}">
                <i class="fa-solid fa-link"></i>
            </button>
        </div>

        <h1>{{ $post->subject }}</h1>

        @if($post->featured_image_url)
            <div class="post-featured-image" style="background-image:url('{{ $post->featured_image_url }}');"></div>
        @endif

        @if($post->featured_video_url)
            <div style="margin-bottom:2.5rem; border-radius:14px; overflow:hidden; box-shadow: var(--shadow);">
                <video src="{{ $post->featured_video_url }}" controls style="width:100%; display:block;"></video>
            </div>
        @endif

        <div class="post-content">
            {!! $post->body !!}
        </div>
    </article>

    @if($related->isNotEmpty())
        <section class="related-section">
            <h3>More from the Blog</h3>
            <div class="related-grid">
                @foreach($related as $r)
                    <a href="{{ route('blog.show', $r->slug) }}" class="related-card">
                        <div class="related-card-image" @if($r->featured_image_url) style="background-image:url('{{ $r->featured_image_url }}');" @endif></div>
                        <div class="related-card-body">
                            <div class="related-card-title">{{ $r->subject }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <footer id="contact">
        <div class="footer-top">
            <div class="footer-brand">
                <span class="logo-text">Prox<span>World</span></span>
                <p>Your trusted proxy provider worldwide. Safe, fast, and built for results.</p>
                <div class="footer-socials">
                    <a href="https://whatsapp.com/channel/0029VbEIyPKFi8xhoHYXrk2i" target="_blank" class="soc-btn"><i class="fab fa-whatsapp"></i></a>
                    <a href="https://t.me/proxworldhq" target="_blank" class="soc-btn"><i class="fab fa-telegram"></i></a>
                    <a href="https://www.tiktok.com/@ProxWorld3928" target="_blank" class="soc-btn"><i class="fab fa-tiktok"></i></a>
                    <a href="https://www.x.com/prox_world" class="soc-btn"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.instagram.com/proxworldhq" class="soc-btn"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h4>Navigate</h4>
                <ul>
                    <li><a href="{{ route('login') }}">Login</a></li>
                    <li><a href="{{ route('register') }}">Sign Up</a></li>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#platforms">Platforms</a></li>
                    <li><a href="{{ route('blog.index') }}">Blog</a></li>
                </ul>
            </div>
                <div class="footer-col">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="{{ route('faq') }}">FAQ</a></li>
                        <li><a href="{{ route('terms-of-use') }}">Terms of Use</a></li>
                        <li><a href="{{ route('privacy-policy') }}">Privacy Policy</a></li>
                        <li><a href="{{ route('refund-policy') }}">Refund Policy</a></li>
                        <li><a href="{{ route('acceptable-use-policy') }}">Acceptable Use Policy</a></li>
                        <li><a href="{{ route('reseller-agreement') }}">Reseller Agreement</a></li>
                        <li><a href="{{ route('cookie-policy') }}">Cookie Policy</a></li>
                    </ul>
                </div>
            <div class="footer-col">
                <h4>Contact</h4>
                <div class="contact-item"><i class="fas fa-envelope"></i> info@proxworld.shop</div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2025 ProxWorld. All rights reserved.</p>
            <p>ProxWorld is operated by AfricGEM International Company Limited</p>
        </div>
    </footer>
    <script>
        document.getElementById('copy-link-btn').addEventListener('click', function () {
            var btn = this;
            var url = btn.dataset.url;

            navigator.clipboard.writeText(url).then(function () {
                btn.classList.add('copied');
                setTimeout(function () { btn.classList.remove('copied'); }, 1800);
            });
        });
    </script>
</body>
</html>
