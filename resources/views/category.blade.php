@extends('layouts.app')

@php
    use App\Support\SeoResolver;

    $siteName = config('seo.site_name', 'ЄНОТ-24');
    $servicesCount = $category->getAllServices()->count();
    $fallbackTitle = $category->name . ' у Києві — послуги та ціни | ' . $siteName;
    $fallbackDescription = 'Послуги ' . $category->name . ' від ' . $siteName . '. '
        . ($servicesCount > 0 ? $servicesCount . ' послуг у категорії. ' : '')
        . 'Хімчистка з кур\'єрською доставкою. Актуальні ціни.';
    $serviceNames = $category->services->take(5)->pluck('name')->implode(', ');
    $fallbackKeywords = $category->name . ', хімчистка, послуги, ціни, Київ, ' . $siteName
        . ($serviceNames ? ', ' . $serviceNames : '');

    $pageTitle = SeoResolver::title($category, $fallbackTitle);
    $pageDescription = SeoResolver::description($category, $fallbackDescription);
    $pageUrl = SeoResolver::canonical($category, route('category_page', $category->href, false));
    $keywords = SeoResolver::keywords($category, $fallbackKeywords);
    $robots = SeoResolver::robots($category);
    $ogTitle = SeoResolver::ogTitle($category, $pageTitle);
    $ogDescription = SeoResolver::ogDescription($category, $pageDescription);
    $ogImage = SeoResolver::ogImageUrl($category);
@endphp

@section('title')
    {{ $pageTitle }}
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
        'modifiedTime' => optional($category->updated_at)->toAtomString(),
    ])
@endsection

@section('content')
    @include('includes.spa.mount')
@endsection

@section('scripts')
@endsection
