@extends('layouts.app')

@section('title')
    Для бізнесу / B2B - Єнот 24. Послуги хімчистки для вашого бізнесу
@endsection

@php
    $siteName = config('app.name', 'ЄНОТ 24');
    $pageTitle = 'Для бізнесу / B2B - ' . $siteName;
    $pageDescription = 'Послуги хімчистки одягу та домашнього текстилю для бізнесу від ЄНОТ 24. Корпоративні рішення, оптові ціни, професійне обслуговування.';
    $pageUrl = route('b2b_page');
    
    $firstB2b = $b2b_data->first();
    $ogImage = $firstB2b && $firstB2b->banner 
        ? asset('storage/' . $firstB2b->banner)
        : asset('storage/src/logo/full_logo.svg');
    
    $keywords = 'B2B, для бізнесу, корпоративні послуги, оптові ціни, хімчистка, одяг, текстиль, ЄНОТ 24, бізнес-рішення';
@endphp

@section('seo_tags')
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="keywords" content="{{ $keywords }}">
    
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $pageUrl }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $pageTitle }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="uk_UA">
    
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $pageUrl }}">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    
    <meta name="robots" content="{{ config('seo.robots_index') }}">
    <link rel="canonical" href="{{ $pageUrl }}">
    <meta name="author" content="{{ $siteName }}">
@endsection

@section('content')
    @include('includes.spa.mount')
@endsection

@section('scripts')
@endsection
