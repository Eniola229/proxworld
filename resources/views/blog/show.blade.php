@extends('layouts.app')

@php
    $postUrl = route('blog.show', $post->slug);

    $ogDescription = $post->excerpt
        ?: Str::limit(trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($post->body)))), 160);

    $ogImage = $post->featured_image_url ?: asset('assets/images/LOGO.png');
    if (!Str::startsWith($ogImage, ['http://', 'https://'])) {
        $ogImage = url($ogImage);
    }

    $articleSchema = [
        '@context'         => 'https://schema.org',
        '@type'            => 'BlogPosting',
        'headline'         => $post->subject,
        'description'      => $ogDescription,
        'image'            => [$ogImage],
        'datePublished'    => $post->sent_at->toIso8601String(),
        'dateModified'     => ($post->updated_at ?? $post->sent_at)->toIso8601String(),
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $postUrl],
        'author'           => ['@type' => 'Person', 'name' => optional($post->creator)->name ?: 'ProxWorld'],
        'publisher'        => [
            '@type' => 'Organization',
            'name'  => 'ProxWorld',
            'logo'  => ['@type' => 'ImageObject', 'url' => asset('assets/images/LOGO.png')],
        ],
    ];

    $breadcrumbSchema = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('welcome')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => route('blog.index')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $post->subject, 'item' => $postUrl],
        ],
    ];
@endphp

@section('title', $post->subject . ' — ProxWorld Blog')
@section('description', $ogDescription)
@section('canonical', $postUrl)
@section('og_type', 'article')
@section('og_image', $ogImage)

@push('meta')
<meta property="article:published_time" content="{{ $post->sent_at->toIso8601String() }}">
@if($post->updated_at)
<meta property="article:modified_time" content="{{ $post->updated_at->toIso8601String() }}">
@endif
@if($post->creator)
<meta property="article:author" content="{{ $post->creator->name }}">
@endif
@endpush

@push('jsonld')
<script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@push('styles')
<style>
@keyframes rise { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }

.post-wrap { max-width: 760px; margin: 0 auto; padding: calc(var(--hdr-h) + 3rem) 2rem 2rem; }
.post-breadcrumb {
  font-family: 'DM Mono', monospace; font-size: 0.72rem; color: var(--soft); letter-spacing: 1px;
  text-transform: uppercase; margin-bottom: 1.5rem; opacity: 0; animation: rise 0.6s ease forwards;
}
.post-breadcrumb a { color: var(--accent); text-decoration: none; }
.post-meta {
  font-family: 'DM Mono', monospace; font-size: 0.72rem; color: var(--soft); letter-spacing: 1px;
  text-transform: uppercase; margin-bottom: 1rem; opacity: 0; animation: rise 0.6s ease forwards 0.1s;
}
.post-wrap h1 {
  font-family: 'Bebas Neue', Impact, sans-serif; font-weight: 400; font-size: clamp(2.4rem, 5vw, 3.8rem);
  line-height: 1.02; margin-bottom: 2rem; opacity: 0; animation: rise 0.7s ease forwards 0.2s;
}
.post-featured-image {
  width: 100%; aspect-ratio: 16/9; border-radius: 14px; background: var(--bg-2) center/cover no-repeat;
  margin-bottom: 2.5rem; box-shadow: var(--shadow); opacity: 0; animation: rise 0.8s ease forwards 0.3s;
}
.post-content { font-size: 1.05rem; line-height: 1.85; color: var(--navy); opacity: 0; animation: rise 0.8s ease forwards 0.4s; overflow-wrap: break-word; }
.post-content p { margin-bottom: 1.4rem; }
.post-content h2 { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.5rem; margin: 2.2rem 0 1rem; }
.post-content h3 { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 1.2rem; margin: 1.8rem 0 0.8rem; }
.post-content ul, .post-content ol { margin: 0 0 1.4rem 1.4rem; }
.post-content li { margin-bottom: 0.5rem; }
.post-content a { color: var(--accent); }
.post-content img { max-width: 100%; height: auto; border-radius: 10px; margin: 1.5rem 0; }
.post-content pre, .post-content table { max-width: 100%; overflow-x: auto; display: block; }
.post-content blockquote { border-left: 3px solid var(--accent); padding-left: 1.2rem; margin: 1.5rem 0; color: var(--muted); font-style: italic; }

.related-section { max-width: 1100px; margin: 3rem auto 0; padding: 3rem 2rem; border-top: 1px solid var(--border); }
.related-section h2 { font-family: 'Bebas Neue', Impact, sans-serif; font-weight: 400; font-size: 1.8rem; margin-bottom: 1.5rem; text-align: center; }
.related-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
.related-card {
  background: var(--surface); border: 1px solid var(--border); border-radius: 12px; overflow: hidden;
  text-decoration: none; color: inherit; box-shadow: var(--shadow); transition: transform 0.3s, box-shadow 0.3s;
}
.related-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }
.related-card-image { width: 100%; aspect-ratio: 16/10; background: var(--bg-2) center/cover no-repeat; }
.related-card-body { padding: 1.1rem; }
.related-card-title { font-weight: 700; font-size: 0.95rem; line-height: 1.4; }

.share-bar { display: flex; flex-wrap: wrap; align-items: center; gap: 0.6rem; margin-bottom: 1.8rem; opacity: 0; animation: rise 0.6s ease forwards 0.15s; }
.share-label { font-family: 'DM Mono', monospace; font-size: 0.7rem; color: var(--soft); letter-spacing: 1px; text-transform: uppercase; margin-right: 0.2rem; }
.share-btn {
  width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;
  border: 1px solid var(--border-2); border-radius: 50%; background: var(--surface); color: var(--muted);
  font-size: 0.9rem; text-decoration: none; cursor: pointer; transition: all 0.25s; position: relative;
  -webkit-appearance: none; appearance: none; padding: 0;
}
.share-btn:hover { border-color: var(--electric); color: var(--electric); transform: translateY(-2px); box-shadow: var(--shadow); }
.share-btn.copied::after {
  content: 'Copied!'; position: absolute; top: -32px; left: 50%; transform: translateX(-50%);
  background: var(--navy); color: #fff; font-size: 0.65rem; font-family: 'DM Mono', monospace;
  padding: 0.3rem 0.6rem; border-radius: 4px; white-space: nowrap;
}

@media (max-width: 800px) { .related-grid { grid-template-columns: 1fr; } }
@media (max-width: 600px) { .post-wrap { padding-left: 1.2rem; padding-right: 1.2rem; } .related-section { padding-left: 1.2rem; padding-right: 1.2rem; } }
</style>
@endpush

@section('content')

<article class="post-wrap">

    <div class="post-breadcrumb"><a href="{{ route('blog.index') }}">← Back to Blog</a></div>

    <div class="post-meta">
        {{ $post->sent_at->format('F d, Y') }}
        @if($post->creator) &middot; By {{ $post->creator->name }} @endif
    </div>

    <div class="share-bar">
        <span class="share-label">Share:</span>
        <a href="https://api.whatsapp.com/send?text={{ urlencode($post->subject . ' - ' . $postUrl) }}" target="_blank" rel="noopener" class="share-btn" title="Share on WhatsApp" aria-label="Share on WhatsApp"><i class="fab fa-whatsapp"></i></a>
        <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->subject) }}&url={{ urlencode($postUrl) }}" target="_blank" rel="noopener" class="share-btn" title="Share on X" aria-label="Share on X"><i class="fab fa-twitter"></i></a>
        <a href="https://t.me/share/url?url={{ urlencode($postUrl) }}&text={{ urlencode($post->subject) }}" target="_blank" rel="noopener" class="share-btn" title="Share on Telegram" aria-label="Share on Telegram"><i class="fab fa-telegram"></i></a>
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($postUrl) }}" target="_blank" rel="noopener" class="share-btn" title="Share on Facebook" aria-label="Share on Facebook"><i class="fab fa-facebook-f"></i></a>
        <button type="button" class="share-btn" id="copy-link-btn" title="Copy link" aria-label="Copy link" data-url="{{ $postUrl }}"><i class="fa-solid fa-link"></i></button>
    </div>

    <h1>{{ $post->subject }}</h1>

    @if($post->featured_image_url)
        <div class="post-featured-image" role="img" aria-label="{{ $post->subject }}" style="background-image:url('{{ $post->featured_image_url }}');"></div>
    @endif

    @if($post->featured_video_url)
        <div style="margin-bottom:2.5rem; border-radius:14px; overflow:hidden; box-shadow: var(--shadow);">
            <video src="{{ $post->featured_video_url }}" controls playsinline preload="metadata" style="width:100%; display:block;"></video>
        </div>
    @endif

    <div class="post-content">
        {!! $post->body !!}
    </div>

</article>

@if($related->isNotEmpty())
<section class="related-section">
    <h2>More from the Blog</h2>
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

@endsection

@push('scripts')
<script>
(function () {
  var btn = document.getElementById('copy-link-btn');
  if (!btn) return;
  function done() {
    btn.classList.add('copied');
    setTimeout(function () { btn.classList.remove('copied'); }, 1800);
  }
  btn.addEventListener('click', function () {
    var url = btn.getAttribute('data-url');
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(url).then(done, fallback);
    } else { fallback(); }
    function fallback() {
      var t = document.createElement('textarea');
      t.value = url; t.setAttribute('readonly', ''); t.style.position = 'fixed'; t.style.opacity = '0';
      document.body.appendChild(t); t.select();
      try { document.execCommand('copy'); done(); } catch (e) {}
      document.body.removeChild(t);
    }
  });
})();
</script>
@endpush
