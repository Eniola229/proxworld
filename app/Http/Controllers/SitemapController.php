<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    public function index()
    {
        // Cached briefly — this runs on every crawler hit, and the URL set
        // barely changes minute to minute.
        $xml = Cache::remember('sitemap.xml', now()->addHours(6), function () {
            $urls = [
                ['loc' => route('welcome'), 'priority' => '1.0'],
                ['loc' => route('faq'), 'priority' => '0.6'],
                ['loc' => route('terms-of-use'), 'priority' => '0.3'],
                ['loc' => route('privacy-policy'), 'priority' => '0.3'],
                ['loc' => route('refund-policy'), 'priority' => '0.3'],
                ['loc' => route('acceptable-use-policy'), 'priority' => '0.3'],
                ['loc' => route('reseller-agreement'), 'priority' => '0.3'],
                ['loc' => route('cookie-policy'), 'priority' => '0.3'],
                ['loc' => route('blog.index'), 'priority' => '0.7'],
                ['loc' => route('register'), 'priority' => '0.8'],
                ['loc' => route('login'), 'priority' => '0.5'],
            ];

            Newsletter::publishedOnBlog()->latest('sent_at')->get(['slug'])->each(function ($post) use (&$urls) {
                $urls[] = [
                    'loc' => route('blog.show', $post->slug),
                    'priority' => '0.6',
                ];
            });

            $items = collect($urls)->map(function ($u) {
                $lastmod = isset($u['lastmod']) ? "<lastmod>{$u['lastmod']}</lastmod>" : '';

                return "<url><loc>{$u['loc']}</loc>{$lastmod}<priority>{$u['priority']}</priority></url>";
            })->implode('');

            return '<?xml version="1.0" encoding="UTF-8"?>'
                .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
                .$items
                .'</urlset>';
        });

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
