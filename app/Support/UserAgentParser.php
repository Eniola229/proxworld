<?php

namespace App\Support;

class UserAgentParser
{
    public static function isBot(string $ua): bool
    {
        if (trim($ua) === '') {
            return true;
        }

        return (bool) preg_match(
            '/bot|crawl|spider|slurp|facebookexternalhit|whatsapp|telegram|discord|skype|slack|preview|monitor|uptime|curl|wget|python-requests|httpclient|headless|lighthouse|pingdom|gtmetrix/i',
            $ua
        );
    }

    public static function parse(string $ua): array
    {
        // ---- OS ----
        $os = 'Unknown';
        if (preg_match('/Windows NT/i', $ua)) {
            $os = 'Windows';
        } elseif (preg_match('/Android\s([\d\.]+)/i', $ua, $m)) {
            $os = 'Android ' . $m[1];
        } elseif (preg_match('/(?:iPhone|CPU) OS ([\d_]+)/i', $ua, $m)) {
            $os = 'iOS ' . str_replace('_', '.', $m[1]);
        } elseif (preg_match('/Mac OS X/i', $ua)) {
            $os = 'macOS';
        } elseif (preg_match('/CrOS/i', $ua)) {
            $os = 'ChromeOS';
        } elseif (preg_match('/Linux/i', $ua)) {
            $os = 'Linux';
        }

        // ---- Device type ----
        $isTablet = preg_match('/iPad|Tablet|PlayBook|Silk/i', $ua)
            || (preg_match('/Android/i', $ua) && ! preg_match('/Mobile/i', $ua));
        $isMobile = preg_match('/Mobile|iPhone|iPod|Windows Phone/i', $ua);

        $type = $isTablet ? 'tablet' : ($isMobile ? 'mobile' : 'desktop');

        // ---- Device name ----
        $name = null;
        if (preg_match('/iPhone/i', $ua)) {
            $name = 'iPhone';
        } elseif (preg_match('/iPad/i', $ua)) {
            $name = 'iPad';
        } elseif (preg_match('/Android [\d\.]+;\s*([^;\)]+?)(?:\sBuild|\))/i', $ua, $m)) {
            $name = trim($m[1]);
        } elseif (preg_match('/Windows NT/i', $ua)) {
            $name = 'Windows PC';
        } elseif (preg_match('/Macintosh/i', $ua)) {
            $name = 'Mac';
        } elseif (preg_match('/CrOS/i', $ua)) {
            $name = 'Chromebook';
        } elseif (preg_match('/Linux/i', $ua)) {
            $name = 'Linux PC';
        }

        // ---- Browser (in-app browsers first, they matter for social sources) ----
        $browsers = [
            ['/FBAN|FBAV/i', 'Facebook App'],
            ['/Instagram/i', 'Instagram App'],
            ['/TikTok|musical_ly|BytedanceWebview/i', 'TikTok App'],
            ['/LinkedInApp/i', 'LinkedIn App'],
            ['/Snapchat/i', 'Snapchat App'],
            ['/Edg(?:e|A|iOS)?\/(\d+)/i', 'Edge'],
            ['/OPR\/(\d+)|Opera\/(\d+)/i', 'Opera'],
            ['/SamsungBrowser\/(\d+)/i', 'Samsung Internet'],
            ['/(?:Firefox|FxiOS)\/(\d+)/i', 'Firefox'],
            ['/(?:Chrome|CriOS)\/(\d+)/i', 'Chrome'],
            ['/Version\/(\d+).*Safari/i', 'Safari'],
            ['/MSIE (\d+)|Trident.*rv:(\d+)/i', 'Internet Explorer'],
        ];

        $browser = 'Unknown';
        foreach ($browsers as [$pattern, $label]) {
            if (preg_match($pattern, $ua, $m)) {
                $version = '';
                foreach (array_slice($m, 1) as $part) {
                    if ($part !== '') {
                        $version = $part;
                        break;
                    }
                }
                $browser = trim($label . ' ' . $version);
                break;
            }
        }

        return [
            'device_type' => $type,
            'device_name' => $name ? mb_substr($name, 0, 120) : null,
            'os'          => mb_substr($os, 0, 60),
            'browser'     => mb_substr($browser, 0, 60),
        ];
    }
}