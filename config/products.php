<?php

/*
 | One place for every product page, the pricing page, the header menu and the footer.
 |
 | CHECK EVERY LINE AGAINST WHAT YOU REALLY SELL. I wrote the specs from your welcome-page
 | copy (190+ countries, rotating, SOCKS5) plus how these proxy types normally work.
 |
 | 'from'  => price text such as '$1.50' or '₦2,500'. Leave null to show "See live price" on the
 |            site. If prices are in your DB / currency helper, set it in the controller instead
 |            (PageController@pricing is the place) — the views only print what they are given.
 | 'color' => ico-res | ico-isp | ico-dc | ico-mob  (defined in site.css)
 */

return [

    'residential' => [
        'name'    => 'Residential Proxies',
        'short'   => 'Residential',
        'icon'    => 'fa-house-user',
        'color'   => 'ico-res',
        'tagline' => 'Real home IPs that look like real visitors',
        'intro'   => 'Your traffic goes out through IP addresses that belong to real households, so sites treat it like any other visitor. Use them when datacenter IPs get blocked.',
        'model'   => 'Pay per GB',
        'unit'    => 'GB',
        'from'    => null,
        'best_for' => ['Web scraping', 'SERP and SEO tracking', 'Price monitoring', 'Ad verification', 'Social accounts'],
        'features' => [
            ['fa-rotate',        'Rotating or sticky sessions', 'Change IP on every request, or keep the same one for as long as your task needs it.'],
            ['fa-location-dot',  'Pick your location',          'Target by country, and by state or city where available.'],
            ['fa-plug',          'HTTP(S) and SOCKS5',          'Works with browsers, scrapers, bots and anything else that accepts a proxy.'],
            ['fa-wallet',        'Traffic that stays yours',    'You pay for the data you use. Fund your wallet, buy a plan, start sending requests.'],
        ],
        'specs' => [
            'Pricing'   => 'Per GB',
            'IP type'   => 'Residential',
            'Rotation'  => 'Rotating or sticky',
            'Protocols' => 'HTTP(S), SOCKS5',
            'Targeting' => 'Country, state, city',
            'Coverage'  => '190+ countries',
        ],
    ],

    'isp' => [
        'name'    => 'ISP Proxies',
        'short'   => 'ISP',
        'icon'    => 'fa-network-wired',
        'color'   => 'ico-isp',
        'tagline' => 'Datacenter speed, residential trust',
        'intro'   => 'Static IPs registered to internet service providers. They are fast like datacenter proxies but look like home connections, and they keep the same address.',
        'model'   => 'Pay per proxy',
        'unit'    => 'proxy',
        'from'    => null,
        'best_for' => ['E-commerce automation', 'Sneaker and ticket sites', 'Account management', 'Ad verification', 'Long sessions'],
        'features' => [
            ['fa-thumbtack',     'Static IPs',            'The address does not change, so logins and sessions stay stable.'],
            ['fa-bolt',          'Fast connections',      'Built for speed-sensitive work where a slow proxy costs you sales or data.'],
            ['fa-shield-halved', 'Looks residential',     'ISP-registered IPs get fewer blocks than plain datacenter ranges.'],
            ['fa-plug',          'HTTP(S) and SOCKS5',    'Drop into any tool that supports proxy settings.'],
        ],
        'specs' => [
            'Pricing'   => 'Per proxy',
            'IP type'   => 'Static, ISP-registered',
            'Rotation'  => 'None (fixed IP)',
            'Protocols' => 'HTTP(S), SOCKS5',
            'Targeting' => 'Country',
            'Best at'   => 'Speed plus trust',
        ],
    ],

    'datacenter' => [
        'name'    => 'Datacenter Proxies',
        'short'   => 'Datacenter',
        'icon'    => 'fa-server',
        'color'   => 'ico-dc',
        'tagline' => 'Fast and cheap for high-volume jobs',
        'intro'   => 'IPs hosted in datacenters. They are the fastest and cheapest option, and a good fit for sites that do not block datacenter ranges.',
        'model'   => 'Pay per proxy',
        'unit'    => 'proxy',
        'from'    => null,
        'best_for' => ['Software testing', 'Bulk scraping of open sites', 'Uptime monitoring', 'SEO tools', 'Speed-critical tasks'],
        'features' => [
            ['fa-gauge-high',    'Top speed',             'Datacenter bandwidth and low latency for heavy jobs.'],
            ['fa-coins',         'Lowest cost',           'The cheapest way to get a large number of IPs.'],
            ['fa-globe',         'Many locations',        'Choose the country that matches your target.'],
            ['fa-plug',          'HTTP(S) and SOCKS5',    'Works with every common tool and library.'],
        ],
        'specs' => [
            'Pricing'   => 'Per proxy',
            'IP type'   => 'Datacenter',
            'Rotation'  => 'Fixed or rotating',
            'Protocols' => 'HTTP(S), SOCKS5',
            'Targeting' => 'Country',
            'Best at'   => 'Speed and price',
        ],
    ],

    'mobile' => [
        'name'    => 'Mobile Proxies',
        'short'   => 'Mobile',
        'icon'    => 'fa-mobile-screen',
        'color'   => 'ico-mob',
        'tagline' => 'Real 4G and 5G carrier IPs',
        'intro'   => 'Traffic goes through real mobile carrier networks. Thousands of real phone users share each IP, so platforms rarely flag it. The strongest option for social apps.',
        'model'   => 'Pay per day',
        'unit'    => 'day',
        'from'    => null,
        'best_for' => ['Social media platforms', 'Mobile app testing', 'Regional ad checks', 'Hard-to-scrape sites'],
        'features' => [
            ['fa-signal',        'Real carrier IPs',      'Addresses from mobile networks, not hosting providers.'],
            ['fa-rotate',        'IP rotation on demand', 'Get a fresh IP when you need one.'],
            ['fa-user-shield',   'Highest trust',         'Sites are slow to block IPs shared by real phone users.'],
            ['fa-plug',          'HTTP(S) and SOCKS5',    'Use them in apps, browsers and scripts.'],
        ],
        'specs' => [
            'Pricing'   => 'Per day',
            'IP type'   => 'Mobile (4G/5G)',
            'Rotation'  => 'On demand',
            'Protocols' => 'HTTP(S), SOCKS5',
            'Targeting' => 'Country and carrier',
            'Best at'   => 'Trust',
        ],
    ],

];
