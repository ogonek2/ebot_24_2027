@extends('layouts.app')

@section('title', 'Наші приймальні пункти - Єнот 24 / Хімчистка одягу та килимів у Києві')

@php
    $siteName = config('app.name', 'ЄНОТ 24');
    $pageTitle = 'Локації - ' . $siteName;
    $pageDescription = 'Знайдіть найближче відділення ЄНОТ 24 у вашому місті. Хімчистка одягу та домашнього текстилю з кур\'єрською доставкою. Адреси, графік роботи, контакти.';
    $pageUrl = route('locations_page');
    $ogImage = asset('storage/src/logo/full_logo.svg');
    $cityNames = $cities->pluck('city')->implode(', ');
    $keywords = 'локації, адреси, відділення, хімчистка, ЄНОТ 24, ' . $cityNames . ', графік роботи, контакти';
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
