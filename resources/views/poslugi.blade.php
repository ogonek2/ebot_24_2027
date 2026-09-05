@extends('layouts.app')

@section('title')
    Послуги та ціни - Єнот 24. Послуги на Хімчистку, Прання. Прасування, Ремонт та реставрація взуття
@endsection

@php
    $siteName = config('app.name', 'ЄНОТ 24');
    $pageTitle = 'Послуги та ціни - ' . $siteName;
    $pageDescription = 'Повний перелік послуг хімчистки одягу та домашнього текстилю від ЄНОТ 24. Актуальні ціни, кур\'єрська доставка, професійне обслуговування.';
    $pageUrl = route('services');
    
    $firstCategory = $categories->first();
    $ogImage = $firstCategory && $firstCategory->category_img 
        ? asset('storage/' . $firstCategory->category_img)
        : asset('storage/src/logo/full_logo.svg');
    
    $categoryNames = $categories->pluck('name')->take(5)->implode(', ');
    $keywords = 'хімчистка, послуги, ціни, одяг, текстиль, кур\'єрська доставка, ' . $categoryNames . ', ЄНОТ 24';
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
