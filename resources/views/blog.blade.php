@extends('layouts.app')

@php
    use App\Models\SeoPage;
    use App\Support\SeoResolver;

    $siteName = config('seo.site_name', 'ЄНОТ-24');
    $currentPage = $posts->currentPage();
    $seoPage = SeoPage::forKey(SeoPage::KEY_BLOG_INDEX);

    $defaults = [
        'title' => $currentPage > 1
            ? 'Блог — сторінка ' . $currentPage . ' | ' . $siteName
            : 'Блог | ' . $siteName,
        'description' => $currentPage > 1
            ? 'Сторінка ' . $currentPage . ' блогу ' . $siteName . ': статті про догляд за одягом, килимами та текстилем у Києві.'
            : 'Корисні матеріали про догляд за одягом, килимами та текстилем. Поради від хімчистки у Києві.',
        'url' => seo_paginator_url($posts->url($currentPage)),
        'keywords' => 'блог, хімчистка, прання, догляд за одягом, ' . $siteName . ', Київ',
    ];

    $seo = SeoResolver::fromSeoPage($seoPage, $defaults);
    if ($currentPage > 1 && blank($seoPage->meta_title)) {
        $seo['pageTitle'] = $defaults['title'];
    }
    if ($currentPage > 1 && blank($seoPage->meta_description)) {
        $seo['pageDescription'] = seo_truncate($defaults['description'], 160);
    }
    if ($currentPage > 1) {
        $seo['pageUrl'] = $defaults['url'];
    }

    extract($seo, EXTR_SKIP);
    $pageTitle = $seo['pageTitle'];
    $pageDescription = $seo['pageDescription'];
    $pageUrl = $seo['pageUrl'];
    $keywords = $seo['keywords'];
    $robots = $seo['robots'];
    $ogTitle = $seo['ogTitle'];
    $ogDescription = $seo['ogDescription'];
    $ogImage = $seo['ogImage'];
    $breadcrumbs = [breadcrumb_home()];
    if ($currentPage > 1) {
        $breadcrumbs[] = ['name' => 'Блог', 'url' => route('blog.index')];
        $breadcrumbs[] = ['name' => 'Сторінка ' . $currentPage];
    } else {
        $breadcrumbs[] = ['name' => 'Блог'];
    }
@endphp

@section('title')
    {{ $pageTitle }}
@endsection

@section('seo_tags')
    @include('includes.seo.meta', compact(
        'pageTitle', 'pageDescription', 'pageUrl', 'keywords', 'robots',
        'ogTitle', 'ogDescription', 'ogImage'
    ))
    <link rel="alternate" type="application/rss+xml" title="Блог {{ $siteName }}" href="{{ route('blog.feed') }}">
    @if($posts->previousPageUrl())
        <link rel="prev" href="{{ seo_paginator_url($posts->previousPageUrl()) }}">
    @endif
    @if($posts->nextPageUrl())
        <link rel="next" href="{{ seo_paginator_url($posts->nextPageUrl()) }}">
    @endif
@endsection

@section('styles')
<style>
    .blog-index-hero {
        background: linear-gradient(135deg, rgba(116, 112, 191, 0.08) 0%, rgba(243, 242, 255, 0.95) 100%);
    }

    .blog-pager {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }

    .blog-pager__list {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 0.375rem;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .blog-pager__btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.375rem;
        min-width: 2.5rem;
        height: 2.5rem;
        padding: 0 0.75rem;
        border-radius: 9999px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #374151;
        font-size: 0.875rem;
        font-weight: 600;
        line-height: 1;
        text-decoration: none;
        transition: border-color 0.2s, background 0.2s, color 0.2s, box-shadow 0.2s;
    }

    .blog-pager__btn:hover:not(.is-disabled):not(.is-active) {
        border-color: rgba(116, 112, 191, 0.45);
        color: #7470BF;
        box-shadow: 0 2px 8px rgba(116, 112, 191, 0.12);
    }

    .blog-pager__btn.is-active {
        background: #7470BF;
        border-color: #7470BF;
        color: #fff;
        box-shadow: 0 4px 14px rgba(116, 112, 191, 0.35);
        cursor: default;
    }

    .blog-pager__btn--nav {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .blog-pager__btn.is-disabled {
        opacity: 0.45;
        cursor: not-allowed;
        background: #f9fafb;
    }

    .blog-pager__ellipsis {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        height: 2.5rem;
        color: #9ca3af;
        font-size: 0.875rem;
        font-weight: 600;
        user-select: none;
    }

    .blog-pager__meta {
        margin: 0;
        font-size: 0.8125rem;
        color: #6b7280;
        text-align: center;
    }

    .blog-pager__meta strong {
        color: #111827;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
    <div class="pb-10 md:pb-16">
        <div class="blog-index-hero border-b border-primary/10">
            <div class="container mx-auto px-4 py-8 md:py-10">
                @include('includes.elements.breadcrumbs', ['breadcrumbs' => $breadcrumbs, 'wrapperClass' => 'px-0 mb-6'])

                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                    <div class="max-w-2xl">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/80 ring-1 ring-primary/20 px-3 py-1 text-xs font-semibold text-primary mb-4">
                            <i class="fas fa-book-open"></i> Блог ЄНОТ-24
                        </span>
                        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 tracking-tight mb-3">
                            Поради з <span class="text-primary">догляду за речами</span>
                        </h1>
                        <p class="text-gray-600 text-base md:text-lg leading-relaxed">
                            Статті про хімчистку, прання, килими та текстиль — від практиків з Києва.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('services') }}"
                           class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-gray-800 ring-1 ring-gray-200 hover:ring-primary/40 transition-all">
                            <i class="fas fa-tags text-primary"></i> Послуги та ціни
                        </a>
                        <a href="{{ route('blog.feed') }}"
                           class="inline-flex items-center gap-2 rounded-full bg-primary text-white px-5 py-2.5 text-sm font-semibold shadow-md shadow-primary/20 hover:bg-primary/90 transition-all"
                           title="RSS-стрічка">
                            <i class="fas fa-rss"></i> RSS
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 pt-8 md:pt-10">
            @if($posts->count() > 0)
                <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4 md:gap-5">
                    @foreach($posts as $post)
                        @include('includes.blog.card', ['post' => $post])
                    @endforeach
                </div>

                <div class="mt-10 md:mt-12">
                    {{ $posts->links('pagination.blog') }}
                </div>
            @else
                <div class="rounded-3xl bg-white ring-1 ring-gray-200 p-12 md:p-16 text-center max-w-lg mx-auto shadow-sm">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-pen-nib text-2xl text-primary"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Статті незабаром</h2>
                    <p class="text-gray-600 mb-8">Ми готуємо корисні матеріали про догляд за одягом і текстилем.</p>
                    <a href="{{ route('services') }}"
                       class="inline-flex items-center gap-2 rounded-full bg-primary text-white px-8 py-3 font-semibold hover:bg-primary/90 transition-colors">
                        До послуг <i class="fas fa-arrow-right text-sm"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
