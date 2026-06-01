@include('includes.seo.head', [
    'pageTitle' => $pageTitle,
    'pageDescription' => $pageDescription,
    'pageUrl' => $pageUrl,
    'keywords' => $keywords ?? null,
    'robots' => $robots ?? null,
    'ogTitle' => $ogTitle ?? null,
    'ogDescription' => $ogDescription ?? null,
    'ogImage' => $ogImage ?? null,
    'ogType' => $ogType ?? 'website',
    'modifiedTime' => $modifiedTime ?? null,
])
