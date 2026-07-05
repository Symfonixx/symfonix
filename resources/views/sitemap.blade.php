@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
    @foreach($urls as $url)
        <url>
            <loc>{{ $url['loc'] }}</loc>
            @if(!empty($url['lastmod']))
                <lastmod>{{ $url['lastmod'] }}</lastmod>
            @endif
            @if(!empty($url['changefreq']))
                <changefreq>{{ $url['changefreq'] }}</changefreq>
            @endif
            @if(!empty($url['priority']))
                <priority>{{ $url['priority'] }}</priority>
            @endif
            @if(!empty($url['alternates']))
                @foreach($url['alternates'] as $alternate)
                    <xhtml:link rel="alternate" hreflang="{{ $alternate['hreflang'] }}" href="{{ $alternate['href'] }}"/>
                @endforeach
            @endif
        </url>
    @endforeach
</urlset>













