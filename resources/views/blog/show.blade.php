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

        footer { border-top: 1px solid var(--border); padding: 2.5rem 3rem; text-align: center; color: var(--soft); font-size: 0.8rem; }
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

    <article class="post-wrap">
        <div class="post-breadcrumb"><a href="{{ route('blog.index') }}">← Back to Blog</a></div>

        <div class="post-meta">
            {{ $post->sent_at->format('F d, Y') }}
            @if($post->creator) &middot; By {{ $post->creator->name }} @endif
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

    <footer>
        &copy; {{ date('Y') }} ProxWorld. All rights reserved.
    </footer>

</body>
</html>