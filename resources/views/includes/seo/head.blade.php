@php
    $keywords = $keywords ?? null;
    $robots = $robots ?? config('seo.robots_index');
    $ogTitle = $ogTitle ?? $pageTitle;
    $ogDescription = $ogDescription ?? $pageDescription;
    $ogImage = $ogImage ?? seo_og_image();
    $ogType = $ogType ?? 'website';
    $siteName = config('seo.site_name');
    $pageDescription = seo_truncate($pageDescription, 160);
@endphp
@if($keywords)
    <meta name="keywords" content="{{ $keywords }}">
@endif
<meta name="description" content="{{ $pageDescription }}">
<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ $pageUrl }}">
@if(!empty($modifiedTime))
    <meta property="article:modified_time" content="{{ $modifiedTime }}">
@endif
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:url" content="{{ $pageUrl }}">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDescription }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:locale" content="uk_UA">
<meta property="og:site_name" content="{{ $siteName }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $ogTitle }}">
<meta name="twitter:description" content="{{ $ogDescription }}">
<meta name="twitter:image" content="{{ $ogImage }}">
