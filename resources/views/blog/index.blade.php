@extends('layouts.app')

@php
    $page = $posts->currentPage();
    $blogCanonical = $page > 1 ? $posts->url($page) : route('blog.index');
    $blogTitle = 'Blog — ProxWorld | Proxy Guides, Tips & News' . ($page > 1 ? ' (Page ' . $page . ')' : '');

    $blogSchema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Blog',
        'name'        => 'ProxWorld Blog',
        'url'         => route('blog.index'),
        'description' => 'Guides, tips, and news on residential, datacenter, ISP, and mobile proxies from ProxWorld.',
        'publisher'   => ['@type' => 'Organization', 'name' => 'ProxWorld', 'logo' => ['@type' => 'ImageObject', 'url' => asset('assets/images/LOGO.png')]],
        'blogPost'    => $posts->map(function ($p) {
            return [
                '@type'         => 'BlogPosting',
                'headline'      => $p->subject,
                'url'           => route('blog.show', $p->slug),
                'datePublished' => $p->sent_at->toIso8601String(),
            ];
        })->values()->all(),
    ];
@endphp

@section('title', $blogTitle)
@section('description', 'Guides, tips, and news on residential, datacenter, ISP, and mobile proxies from ProxWorld.')
@section('canonical', $blogCanonical)

@push('meta')
@if($posts->previousPageUrl())
<link rel="prev" href="{{ $posts->previousPageUrl() }}">
@endif
@if($posts->nextPageUrl())
<link rel="next" href="{{ $posts->nextPageUrl() }}">
@endif
@endpush

@push('jsonld')
<script type="application/ld+json">{!! json_encode($blogSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@push('styles')
<style>
    @keyframes rise { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }

    /* PAGE HEADER */
    .blog-header { padding: calc(var(--hdr-h) + 4rem) 3rem 3rem; text-align: center; position: relative; }
    .blog-eyebrow { font-family: 'DM Mono', monospace; font-size: 0.75rem; color: var(--accent); letter-spacing: 3px; text-transform: uppercase; margin-bottom: 1rem; opacity: 0; animation: rise 0.8s ease forwards 0.1s; }
    .blog-header h1 { font-family: 'Bebas Neue', sans-serif; font-weight: 400; font-size: clamp(3rem, 6vw, 5.5rem); line-height: 0.95; opacity: 0; animation: rise 0.9s ease forwards 0.25s; }
    .blog-header h1 .blue { color: var(--accent); }
    .blog-header p { color: var(--muted); max-width: 480px; margin: 1.2rem auto 0; font-size: 1rem; line-height: 1.7; opacity: 0; animation: rise 0.9s ease forwards 0.4s; }

    /* GRID */
    .blog-grid { max-width: 1200px; margin: 0 auto; padding: 1rem 3rem 4rem; display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; }
    @media (max-width: 900px) { .blog-grid { grid-template-columns: repeat(2, 1fr); } .blog-header { padding-left: 1.5rem; padding-right: 1.5rem; } }
    @media (max-width: 600px) { .blog-grid { grid-template-columns: 1fr; padding: 1rem 1.5rem 3rem; } }

    .post-card { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: var(--shadow); text-decoration: none; color: inherit; display: flex; flex-direction: column; transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s; opacity: 0; animation: rise 0.7s ease forwards; }
    .post-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); border-color: var(--electric); }
    .post-card-image { width: 100%; aspect-ratio: 16/10; background: var(--bg-2) center/cover no-repeat; display: flex; align-items: center; justify-content: center; color: var(--soft); font-size: 2rem; }
    .post-card-body { padding: 1.5rem; display: flex; flex-direction: column; gap: 0.6rem; flex: 1; }
    .post-card-date { font-family: 'DM Mono', monospace; font-size: 0.68rem; color: var(--soft); letter-spacing: 1px; text-transform: uppercase; }
    .post-card-title { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 1.15rem; line-height: 1.35; color: var(--navy); }
    .post-card-excerpt { color: var(--muted); font-size: 0.88rem; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    .post-card-readmore { margin-top: auto; padding-top: 0.5rem; font-family: 'DM Mono', monospace; font-size: 0.72rem; color: var(--accent); letter-spacing: 1px; text-transform: uppercase; }
    .empty-state { grid-column: 1 / -1; text-align: center; padding: 4rem 2rem; color: var(--muted); }

    /* PAGINATION */
    .pagination-wrap { display: flex; justify-content: center; padding: 0 1.5rem 5rem; }
    .pager { display: inline-flex; align-items: center; gap: 0.6rem; flex-wrap: wrap; justify-content: center; background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 0.5rem; box-shadow: var(--shadow); }
    .pager-pages { display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap; justify-content: center; }
    .pager-btn { min-width: 42px; height: 42px; padding: 0 0.9rem; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; font-family: 'DM Mono', monospace; font-size: 0.8rem; font-weight: 500; color: var(--muted); text-decoration: none; border-radius: 9px; border: 1px solid transparent; background: transparent; transition: all 0.25s; }
    a.pager-btn:hover { color: var(--accent); background: var(--bg-2); border-color: var(--border-2); transform: translateY(-1px); }
    .pager-btn.is-active { background: var(--accent); color: #fff; border-color: var(--accent); box-shadow: 0 4px 16px rgba(37, 99, 235, 0.3); cursor: default; }
    .pager-arrow { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 0.72rem; letter-spacing: 1.5px; text-transform: uppercase; color: var(--navy); border-color: var(--border-2); }
    .pager-arrow i { font-size: 0.75rem; }
    .pager-btn.is-disabled { opacity: 0.35; cursor: not-allowed; }
    .pager-dots { min-width: 28px; text-align: center; font-family: 'DM Mono', monospace; color: var(--soft); }
    @media (max-width: 600px) {
        .pager-label { display: none; }
        .pager-arrow { padding: 0; width: 38px; }
        .pager-btn { min-width: 38px; height: 38px; padding: 0 0.6rem; }
    }
</style>
@endpush

@section('content')

<header class="blog-header">
    <div class="blog-eyebrow">// The ProxWorld Journal</div>
    <h1>Proxy <span class="blue">Insights</span> &amp; Guides</h1>
    <p>Tips, tutorials, and news on residential, datacenter, ISP, and mobile proxies — straight from the team building them.</p>
</header>

<div class="blog-grid">
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
</div>

<div class="pagination-wrap">
    {{ $posts->links('pagination.proxworld') }}
</div>

@endsection
