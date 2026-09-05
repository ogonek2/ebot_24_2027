@extends('layouts.app')

@php
    use App\Support\SeoResolver;

    $serviceSlug = $service->transform_url ?? $service->href;
    $defaultPath = route('service_page', [$primaryCategory->href, $serviceSlug], false);
    $pageUrl = SeoResolver::canonical($service, $defaultPath);
    $pageTitle = SeoResolver::title($service, seo_service_title($service->name));
    $pageDescription = SeoResolver::description($service, seo_service_description($service->name));
    $keywords = SeoResolver::keywords($service, $service->name . ', хімчистка, Київ, ЄНОТ-24');
    $robots = SeoResolver::robots($service);
    $ogTitle = SeoResolver::ogTitle($service, $pageTitle);
    $ogDescription = SeoResolver::ogDescription($service, $pageDescription);
    $ogImage = SeoResolver::ogImageUrl($service);

    $originalPrice = floatval($service->price ?? 0);
    $schemaPrice = $originalPrice;
    if ($originalPrice > 0 && $primaryCategory && $primaryCategory->hasActiveDiscount()) {
        $schemaPrice = floatval($primaryCategory->calculateDiscountedPrice($originalPrice));
    }

    $hasPrice = $originalPrice > 0;
    $faqItems = seo_service_faq_items($service, $hasPrice, $schemaPrice);
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
        'modifiedTime' => optional($service->updated_at)->toAtomString(),
    ])
    @include('includes.seo.schema-service', [
        'serviceName' => $service->name,
        'servicePrice' => $schemaPrice,
        'pageUrl' => $pageUrl,
    ])
    @include('includes.seo.schema-faq', ['faqItems' => $faqItems])
@endsection

@section('content')
    @include('includes.spa.mount')
@endsection
