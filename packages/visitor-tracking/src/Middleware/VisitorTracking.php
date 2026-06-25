<?php

namespace MonishRoy\VisitorTracking\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use MonishRoy\VisitorTracking\Models\VisitorTable;

class VisitorTracking
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();
        if (in_array($ip, ['127.0.0.1', '::1'])) {
            $location = [
                'country' => 'Local',
                'region' => 'Local',
                'city' => 'Localhost',
            ];
        } else {
            try {
                $location = Http::get("https://ipinfo.io/{$ip}/json")->json();
            } catch (\Exception $e) {
                $location = [];
            }
        }

        $userAgent = $request->userAgent();

        $response = $next($request);

        $pageTitle = null;
        if (
            $response instanceof \Illuminate\Http\Response &&
            str_contains($response->headers->get('Content-Type'), 'text/html')
        ) {
            $content = $response->getContent();
            preg_match('/<title>(.*?)<\/title>/', $content, $matches);
            $pageTitle = $matches[1] ?? null;
        }

        VisitorTable::create([
            'ip' => $ip,
            'country' => $location['country'] ?? null,
            'region' => $location['region'] ?? null,
            'city' => $location['city'] ?? null,
            'device' => $this->detectDevice($userAgent),
            'os' => $this->detectOS($userAgent),
            'browser' => $this->detectBrowser($userAgent),
            'page_title' => $pageTitle,
            'url' => $request->fullUrl(),
        ]);

        return $response;
    }


    protected function detectDevice($userAgent): string
    {
        if (preg_match('/mobile|android|touch|webos|hpwos/i', $userAgent)) {
            return 'Mobile';
        }

        return 'Desktop';
    }

    protected function detectOS($userAgent): string
    {
        $oses = [
            'Windows' => 'Windows NT',
            'Mac OS' => 'Macintosh',
            'Linux' => 'Linux',
            'Android' => 'Android',
            'iOS' => 'iPhone|iPad',
        ];

        foreach ($oses as $os => $pattern) {
            if (preg_match("/{$pattern}/i", $userAgent)) {
                return $os;
            }
        }

        return 'Unknown';
    }

    protected function detectBrowser($userAgent): string
    {
        $browsers = [
            'Edge' => 'Edg',
            'Chrome' => 'Chrome',
            'Firefox' => 'Firefox',
            'Safari' => 'Safari',
            'Opera' => 'OPR|Opera',
            'Internet Explorer' => 'MSIE|Trident',
        ];

        foreach ($browsers as $browser => $pattern) {
            if (preg_match("/{$pattern}/i", $userAgent)) {
                return $browser;
            }
        }

        return 'Unknown';
    }
}
