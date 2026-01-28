@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ $siteName }}</title>
        <link>{{ $homeUrl }}</link>
        <description><![CDATA[{!! $siteDescription !!}]]></description>
        <language>{{ $language }}</language>
        <lastBuildDate>{{ $lastBuildDate }}</lastBuildDate>
        <atom:link href="{{ $feedUrl }}" rel="self" type="application/rss+xml"/>

        @foreach($items as $item)
            <item>
                <title>{{ $item['title'] }}</title>
                <link>{{ $item['link'] }}</link>
                <guid isPermaLink="true">{{ $item['guid'] }}</guid>
                <pubDate>{{ $item['pubDate'] }}</pubDate>
                <description><![CDATA[{!! $item['description'] !!}]]></description>
                <content:encoded><![CDATA[{!! $item['content'] !!}]]></content:encoded>
            </item>
        @endforeach
    </channel>
</rss>
