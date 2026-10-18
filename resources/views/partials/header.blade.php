@php
    // Resources dropdown. Change URLs/labels here only.
    $resourceLinks = [
        ['icon' => 'fa-code',        'color' => 'ico-dc',  'title' => 'API docs',     'text' => 'Order and manage proxies from code', 'url' => route('api.docs')],
        ['icon' => 'fa-book-open',   'color' => 'ico-res', 'title' => 'Setup guides', 'text' => 'Connect in cURL, Python, Node and PHP', 'url' => route('guides')],
        ['icon' => 'fa-circle-question', 'color' => 'ico-isp', 'title' => 'FAQ',         'text' => 'Answers to the common questions',    'url' => route('faq')],
        ['icon' => 'fa-newspaper',   'color' => 'ico-mob', 'title' => 'Blog',          'text' => 'Tutorials and proxy know-how',        'url' => route('blog.index')],
    ];
@endphp

<header class="site-header" id="siteHeader">
    <div class="hdr-inner">
        <a href="{{ route('welcome') }}" class="logo" aria-label="ProxWorld home">Prox<span>World</span></a>

        <ul class="hdr-links" id="hdrLinks">
            <li class="has-sub">
                <button type="button" class="sub-toggle" aria-expanded="false" aria-haspopup="true">
                    Products <i class="fas fa-chevron-down" aria-hidden="true"></i>
                </button>
                <ul class="sub">
                    @foreach (config('products') as $slug => $p)
                        <li>
                            <a href="{{ route('products.show', $slug) }}">
                                <span class="s-ico {{ $p['color'] }}"><i class="fas {{ $p['icon'] }}" aria-hidden="true"></i></span>
                                <span><strong>{{ $p['name'] }}</strong><small>{{ $p['tagline'] }}</small></span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
            <li><a href="{{ route('pricing') }}" class="{{ request()->routeIs('pricing') ? 'active' : '' }}">Pricing</a></li>
            <li><a href="{{ route('use-cases') }}" class="{{ request()->routeIs('use-cases') ? 'active' : '' }}">Use cases</a></li>
            <li><a href="{{ route('locations') }}" class="{{ request()->routeIs('locations') ? 'active' : '' }}">Locations</a></li>
            <li class="has-sub">
                <button type="button" class="sub-toggle" aria-expanded="false" aria-haspopup="true">
                    Resources <i class="fas fa-chevron-down" aria-hidden="true"></i>
                </button>
                <ul class="sub">
                    @foreach ($resourceLinks as $r)
                        <li>
                            <a href="{{ $r['url'] }}">
                                <span class="s-ico {{ $r['color'] }}"><i class="fas {{ $r['icon'] }}" aria-hidden="true"></i></span>
                                <span><strong>{{ $r['title'] }}</strong><small>{{ $r['text'] }}</small></span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
            <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a></li>

            {{-- Shown only inside the mobile menu --}}
            <li class="mobile-only">
                <a href="{{ route('register') }}" class="btn btn-solid">Get started</a>
                <a href="{{ route('login') }}" class="btn btn-ghost">Log in</a>
            </li>
        </ul>

        <div class="hdr-actions">
            <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Login</a>
            <a href="{{ route('register') }}" class="btn btn-solid btn-sm">Get started</a>
            <button type="button" class="menu-btn" id="menuBtn" aria-label="Open menu" aria-expanded="false" aria-controls="hdrLinks">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>
