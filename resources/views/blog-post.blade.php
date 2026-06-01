@extends('layouts.app')

@php
    use App\Support\SeoResolver;

    $siteName = config('app.name', 'Єнот-24');
    $fallbackTitle = $post->title . ' | Блог ' . $siteName;
    $fallbackDescription = \Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 160);
    $defaultPath = '/blog/' . $post->slug;

    $pageTitle = SeoResolver::title($post, $fallbackTitle);
    $pageDescription = SeoResolver::description($post, $fallbackDescription);
    $pageUrl = SeoResolver::canonical($post, $defaultPath);
    $keywords = SeoResolver::keywords($post, 'хімчистка, прання, ' . $post->title . ', ' . $siteName);
    $robots = SeoResolver::robots($post);
    $ogTitle = SeoResolver::ogTitle($post, $pageTitle);
    $ogDescription = SeoResolver::ogDescription($post, $pageDescription);
    $ogImage = SeoResolver::ogImageUrl($post, $post->featured_image);
    $ogType = 'article';
    $breadcrumbs = [
        breadcrumb_home(),
        ['name' => 'Блог', 'url' => route('blog.index')],
        ['name' => $post->title],
    ];
    $readingMin = max(1, (int) ceil(mb_strlen(strip_tags($post->content ?? '')) / 1200));
    $hasSidebar = $relatedServices->isNotEmpty() || $relatedPosts->isNotEmpty();
@endphp

@section('title')
    {{ $pageTitle }}
@endsection

@section('styles')
    <style>
        .blog-post-layout,
        .blog-post-layout .container {
            width: 100%;
            max-width: 100%;
            min-width: 0;
        }

        .blog-post-layout {
            overflow-x: clip;
        }

        .blog-post-prose {
            font-size: 1.0625rem;
            line-height: 1.75;
            color: #1f2937;
            overflow-wrap: break-word;
            word-wrap: break-word;
            width: 100%;
            max-width: 100%;
            min-width: 0;
        }

        .blog-post-prose figure {
            max-width: 100%;
            min-width: 0;
            overflow: visible;
            display: block !important;
        }

        .blog-post-prose > *:first-child { margin-top: 0; }
        .blog-post-prose > *:last-child { margin-bottom: 0; }
        .blog-post-prose p { margin-bottom: 1.125rem; }

        .blog-post-prose h1,
        .blog-post-prose h2,
        .blog-post-prose h3,
        .blog-post-prose h4,
        .blog-post-prose h5,
        .blog-post-prose h6 {
            font-weight: 700;
            color: #111827;
            line-height: 1.3;
            margin-top: 2rem;
            margin-bottom: 0.875rem;
        }

        .blog-post-prose h2 {
            font-size: 1.625rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid rgba(116, 112, 191, 0.15);
        }

        .blog-post-prose h3 { font-size: 1.375rem; color: #374151; }
        .blog-post-prose h4 { font-size: 1.125rem; }

        .blog-post-prose ul,
        .blog-post-prose ol {
            margin: 1rem 0 1.25rem;
            padding-left: 1.5rem;
        }

        .blog-post-prose ul { list-style-type: disc; }
        .blog-post-prose ol { list-style-type: decimal; }
        .blog-post-prose li { margin-bottom: 0.5rem; }
        .blog-post-prose li::marker { color: #7470BF; }

        .blog-post-prose a {
            color: #E75A84;
            text-decoration: underline;
            text-underline-offset: 3px;
            font-weight: 500;
        }

        .blog-post-prose a:hover { color: #7470BF; }

        .blog-post-prose strong,
        .blog-post-prose b { font-weight: 600; color: #111827; }

        .blog-post-prose blockquote {
            margin: 1.5rem 0;
            padding: 1rem 1.25rem;
            border-left: 4px solid #7470BF;
            background: linear-gradient(90deg, rgba(116, 112, 191, 0.08), rgba(231, 90, 132, 0.05));
            border-radius: 0 0.75rem 0.75rem 0;
            color: #374151;
            font-style: italic;
        }

        .blog-post-prose img {
            max-width: 100%;
            height: auto;
            border-radius: 1rem;
            margin: 1.5rem auto;
            display: block;
        }

        .blog-post-prose pre {
            margin: 1.25rem 0;
            padding: 1rem 1.25rem;
            background: #1e1b4b;
            color: #e9e7ff;
            border-radius: 0.75rem;
            overflow-x: auto;
            font-size: 0.875rem;
        }

        .blog-post-prose code {
            font-family: ui-monospace, monospace;
            font-size: 0.875em;
            background: #f3f2ff;
            color: #5b21b6;
            padding: 0.15em 0.4em;
            border-radius: 0.35rem;
        }

        .blog-post-prose pre code {
            background: transparent;
            color: inherit;
            padding: 0;
        }

        /* Скрол тільки всередині блоку таблиці */
        .blog-post-table-scroll {
            display: block;
            box-sizing: border-box;
            width: 100%;
            max-width: 100%;
            min-width: 0;
            margin: 1.5rem 0;
            overflow-x: auto;
            overflow-y: visible;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior-x: contain;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            background: #fff;
        }

        .blog-post-table-scroll table {
            width: max-content !important;
            min-width: 100%;
            max-width: none !important;
            margin: 0 !important;
            border-collapse: collapse;
            font-size: 0.9375rem;
            table-layout: auto;
        }

        .blog-post-table-scroll th,
        .blog-post-table-scroll td {
            border: 1px solid #e5e7eb;
            padding: 0.625rem 0.875rem;
            text-align: left;
            vertical-align: top;
        }

        .blog-post-table-scroll th {
            background: #f9f8ff;
            font-weight: 600;
            color: #111827;
        }

        .blog-post-table-scroll tbody tr:nth-child(even) td {
            background-color: #fafafa;
        }
    </style>
@endsection

@section('seo_tags')
    @include('includes.seo.meta', [
        'pageTitle' => $pageTitle,
        'pageDescription' => $pageDescription,
        'pageUrl' => $pageUrl,
        'keywords' => $keywords,
        'robots' => $robots,
        'ogTitle' => $ogTitle,
        'ogDescription' => $ogDescription,
        'ogImage' => $ogImage,
        'ogType' => 'article',
        'modifiedTime' => $post->updated_at->toIso8601String(),
    ])
    <meta property="og:image:alt" content="{{ $post->title }}">
    <meta property="article:published_time" content="{{ $post->published_at?->toIso8601String() }}">
    <meta name="author" content="{{ $siteName }}">
    <link rel="alternate" type="application/rss+xml" title="Блог {{ $siteName }}" href="{{ route('blog.feed') }}">
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $post->title,
        'description' => $pageDescription,
        'datePublished' => $post->published_at?->toIso8601String(),
        'dateModified' => $post->updated_at->toIso8601String(),
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $pageUrl],
        'image' => [$ogImage],
        'publisher' => [
            '@type' => 'Organization',
            'name' => $siteName,
            'logo' => ['@type' => 'ImageObject', 'url' => asset('storage/src/logo/logo-enot24.png')],
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@endsection

@section('content')
    <div class="blog-post-layout pb-10 md:pb-16">
        <div class="container mx-auto min-w-0 max-w-7xl px-4">
            @include('includes.elements.breadcrumbs', ['breadcrumbs' => $breadcrumbs, 'wrapperClass' => 'px-0'])

            <div @class([
                'mt-6 grid grid-cols-1 gap-8 lg:gap-10 items-start min-w-0',
                'lg:grid-cols-[minmax(0,1fr)_300px] xl:grid-cols-[minmax(0,1fr)_320px]' => $hasSidebar,
            ])>
                <article class="min-w-0 max-w-full">
                    @if($post->featured_image)
                        <figure class="mb-6 overflow-hidden rounded-2xl ring-1 ring-gray-200/80 md:mb-8">
                            <img src="{{ asset('storage/' . $post->featured_image) }}"
                                 alt="{{ $post->title }}"
                                 class="h-auto max-h-[420px] w-full object-cover">
                        </figure>
                    @endif

                    <header class="mb-6 md:mb-8">
                        <div class="mb-4 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">
                                <i class="far fa-calendar-alt"></i>
                                <time datetime="{{ $post->published_at?->toIso8601String() }}">
                                    {{ $post->published_at?->format('d.m.Y') }}
                                </time>
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                                <i class="far fa-clock"></i> {{ $readingMin }} хв читання
                            </span>
                        </div>
                        <h1 class="text-2xl font-bold leading-tight tracking-tight text-gray-900 sm:text-3xl lg:text-4xl">
                            {{ $post->title }}
                        </h1>
                        @if($post->meta_description)
                            <p class="mt-4 text-lg leading-relaxed text-gray-600">
                                {{ $post->meta_description }}
                            </p>
                        @endif
                    </header>

                    <div class="blog-post-prose min-w-0">
                        @include('includes.blog.post-content', ['content' => $post->content])
                    </div>

                    <footer class="mt-10 border-t border-gray-200 pt-8">
                        <p class="mb-4 text-sm text-gray-600">Потрібна професійна хімчистка в Києві?</p>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('services') }}"
                               class="inline-flex items-center gap-2 rounded-full bg-primary px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-primary/90">
                                Послуги та ціни <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                            <a href="{{ route('courier_page') }}"
                               class="inline-flex items-center gap-2 rounded-full px-5 py-2.5 text-sm font-semibold text-gray-800 ring-1 ring-gray-200 transition-all hover:ring-primary/30">
                                <i class="fas fa-truck text-primary"></i> Кур'єр
                            </a>
                        </div>
                    </footer>
                </article>

                @if($hasSidebar)
                    <aside class="order-last min-w-0 space-y-5 lg:sticky lg:top-24 lg:order-none">
                        @include('includes.blog.sidebar-services', ['relatedServices' => $relatedServices])
                        @include('includes.blog.sidebar-posts', ['relatedPosts' => $relatedPosts])
                    </aside>
                @endif
            </div>
        </div>
    </div>
@endsection
