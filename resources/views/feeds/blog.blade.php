{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>Блог {{ config('seo.site_name') }}</title>
        <link>{{ seo_canonical('/blog') }}</link>
        <description>Корисні статті про хімчистку, прання та догляд за одягом у Києві від {{ config('seo.site_name') }}.</description>
        <language>uk</language>
        <lastBuildDate>{{ now()->toRfc2822String() }}</lastBuildDate>
        <atom:link href="{{ route('blog.feed') }}" rel="self" type="application/rss+xml"/>
        @foreach($posts as $post)
            <item>
                <title>{{ $post->meta_title ?: $post->title }}</title>
                <link>{{ seo_canonical('/blog/' . $post->slug) }}</link>
                <guid isPermaLink="true">{{ seo_canonical('/blog/' . $post->slug) }}</guid>
                <pubDate>{{ $post->published_at?->toRfc2822String() }}</pubDate>
                <description><![CDATA[{!! \Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 500) !!}]]></description>
            </item>
        @endforeach
    </channel>
</rss>
