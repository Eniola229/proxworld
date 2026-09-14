<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog — ProxWorld | Proxy Guides, Tips &amp; News</title>
    <meta name="description" content="Guides, tips, and news on residential, datacenter, ISP, and mobile proxies from ProxWorld.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Syne:wght@400;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!--! BEGIN: Favicon-->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/LOGO.png') }}" />
    <!--! END: Favicon-->
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg:        #F5F7FF;
            --bg-2:      #EEF1FA;
            --surface:   #FFFFFF;
            --accent:    #2563EB;
            --electric:  #3B82F6;
            --gold:      #D97706;
            --navy:      #0F172A;
            --muted:     #64748B;
            --soft:      #94A3B8;
            --border:    rgba(37, 99, 235, 0.14);
            --border-2:  rgba(37, 99, 235, 0.22);
            --shadow:    0 2px 20px rgba(15, 23, 42, 0.07);
            --shadow-lg: 0 8px 40px rgba(15, 23, 42, 0.12);
        }

        html { scroll-behavior: smooth; }
        body { font-family: 'Syne', sans-serif; background: var(--bg); color: var(--navy); }

        @keyframes rise {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* NAV */
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
        .nav-links a { color: var(--muted); text-decoration: none; font-size: 0.85rem; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; transition: color 0.3s; }
        .nav-links a:hover, .nav-links a.active { color: var(--navy); }
        .nav-btns { display: flex; gap: 1rem; align-items: center; }

        .btn { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 0.8rem; letter-spacing: 1.5px; text-transform: uppercase; padding: 0.7rem 1.8rem; border-radius: 4px; text-decoration: none; border: none; display: inline-block; transition: all 0.3s; }
        .btn-ghost { color: var(--muted); background: transparent; border: 1px solid rgba(100, 116, 139, 0.3); }
        .btn-ghost:hover { color: var(--navy); border-color: var(--navy); }
        .btn-solid { background: var(--accent); color: #fff; box-shadow: 0 4px 18px rgba(37, 99, 235, 0.28); }
        .btn-solid:hover { background: var(--electric); box-shadow: 0 6px 28px rgba(59, 130, 246, 0.38); transform: translateY(-2px); }

        /* PAGE HEADER */
        .blog-header {
            padding: 6rem 3rem 3rem;
            text-align: center;
            position: relative;
        }
        .blog-eyebrow {
            font-family: 'DM Mono', monospace;
            font-size: 0.75rem; color: var(--accent);
            letter-spacing: 3px; text-transform: uppercase;
            margin-bottom: 1rem;
            opacity: 0; animation: rise 0.8s ease forwards 0.1s;
        }
        .blog-header h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(3rem, 6vw, 5.5rem);
            line-height: 0.95;
            opacity: 0; animation: rise 0.9s ease forwards 0.25s;
        }
        .blog-header h1 .blue { color: var(--accent); }
        .blog-header p {
            color: var(--muted); max-width: 480px; margin: 1.2rem auto 0;
            font-size: 1rem; line-height: 1.7;
            opacity: 0; animation: rise 0.9s ease forwards 0.4s;
        }

        /* GRID */
        .blog-grid {
            max-width: 1200px; margin: 0 auto;
            padding: 1rem 3rem 6rem;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }
        @media (max-width: 900px) { .blog-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px) { .blog-grid { grid-template-columns: 1fr; padding: 1rem 1.5rem 4rem; } }

        .post-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: var(--shadow);
            text-decoration: none;
            color: inherit;
            display: flex; flex-direction: column;
            transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s;
            opacity: 0; animation: rise 0.7s ease forwards;
        }
        .post-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); border-color: var(--electric); }

        .post-card-image {
            width: 100%; aspect-ratio: 16/10;
            background: var(--bg-2) center/cover no-repeat;
            display: flex; align-items: center; justify-content: center;
            color: var(--soft); font-size: 2rem;
        }
        .post-card-body { padding: 1.5rem; display: flex; flex-direction: column; gap: 0.6rem; flex: 1; }
        .post-card-date {
            font-family: 'DM Mono', monospace; font-size: 0.68rem;
            color: var(--soft); letter-spacing: 1px; text-transform: uppercase;
        }
        .post-card-title {
            font-family: 'Syne', sans-serif; font-weight: 700; font-size: 1.15rem;
            line-height: 1.35; color: var(--navy);
        }
        .post-card-excerpt {
            color: var(--muted); font-size: 0.88rem; line-height: 1.6;
            display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
        }
        .post-card-readmore {
            margin-top: auto; padding-top: 0.5rem;
            font-family: 'DM Mono', monospace; font-size: 0.72rem;
            color: var(--accent); letter-spacing: 1px; text-transform: uppercase;
        }

        .empty-state {
            grid-column: 1 / -1;
            text-align: center; padding: 4rem 2rem;
            color: var(--muted);
        }

        .pagination-wrap { display: flex; justify-content: center; padding-bottom: 4rem; }
        .pagination-wrap nav { background: transparent; border: none; padding: 0; position: static; backdrop-filter: none; }

        @media (max-width: 900px) {
            nav { padding: 1.1rem 1.5rem; }
            .nav-links { display: none; }
            .nav-btns { gap: 0.6rem; }
            .btn { padding: 0.6rem 1.2rem; font-size: 0.72rem; }
        }

        footer {
            border-top: 1px solid var(--border);
            padding: 2.5rem 3rem;
            text-align: center;
            color: var(--soft);
            font-size: 0.8rem;
        }
    </style>
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

    <header class="blog-header">
        <div class="blog-eyebrow">// The ProxWorld Journal</div>
        <h1>Proxy <span class="blue">Insights</span> &amp; Guides</h1>
        <p>Tips, tutorials, and news on residential, datacenter, ISP, and mobile proxies — straight from the team building them.</p>
    </header>

    <main class="blog-grid">
        @forelse($posts as $index => $post)
            <a href="{{ route('blog.show', $post->slug) }}" class="post-card" style="animation-delay: {{ $index * 0.08 }}s;">
                <div class="post-card-image" @if($post->featured_image_url) style="background-image:url('{{ $post->featured_image_url }}');" @endif>
                    @unless($post->featured_image_url)
                        <i class="fa-regular fa-image"></i>
                    @endunless
                </div>
                <div class="post-card-body">
                    <span class="post-card-date">{{ $post->sent_at->format('M d, Y') }}</span>
                    <h2 class="post-card-title">{{ $post->subject }}</h2>
                    @if($post->excerpt)
                        <p class="post-card-excerpt">{{ $post->excerpt }}</p>
                    @endif
                    <span class="post-card-readmore">Read Article →</span>
                </div>
            </a>
        @empty
            <div class="empty-state">
                <p>No posts published yet — check back soon.</p>
            </div>
        @endforelse
    </main>

    <div class="pagination-wrap">
        {{ $posts->links() }}
    </div>

    <footer>
        &copy; {{ date('Y') }} ProxWorld. All rights reserved.
    </footer>

</body>
</html>