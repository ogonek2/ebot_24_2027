@extends('layouts.app')

@section('title')
    Доставка - Єнот 24 / Хімчистка одягу та килимів / Доставка Київ
@endsection

@php
    $siteName = config('app.name', 'ЄНОТ 24');
    $pageTitle = 'Доставка та кур\'єр - ' . $siteName;
    $pageDescription = 'Швидка та надійна доставка одягу та домашнього текстилю прямо до дверей від ЄНОТ 24. Кур\'єрська доставка, зручне обслуговування, професійна хімчистка.';
    $pageUrl = route('delivery_page');
    
    // Используем дефолтное изображение для страницы доставки
    $ogImage = asset('storage/src/logo/full_logo.svg');
    
    // Формируем keywords
    $keywords = 'доставка, кур\'єр, хімчистка, одяг, текстиль, кур\'єрська доставка, швидка доставка, ЄНОТ 24, двері до дверей';
@endphp

@section('seo_tags')
    {{-- Basic Meta Tags --}}
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="keywords" content="{{ $keywords }}">
    
    {{-- Open Graph Meta Tags --}}
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
    
    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $pageUrl }}">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    
    {{-- Additional Meta Tags --}}
    <meta name="robots" content="{{ config('seo.robots_index') }}">
    <link rel="canonical" href="{{ $pageUrl }}">
    <meta name="author" content="{{ $siteName }}">
@endsection

@php
    $breadcrumbs = [breadcrumb_home(), ['name' => 'Доставка']];
@endphp

@section('content')
    @include('includes.elements.breadcrumbs', ['breadcrumbs' => $breadcrumbs])

    {{-- Hero Section --}}
    <div class="container mx-auto px-4 mb-20">
        <div class="flex flex-col-reverse lg:flex-row gap-12 items-center">
            {{-- Content --}}
            <div class="space-y-8 text-center lg:text-left flex-1">
                {{-- Badge --}}
                <div class="inline-flex items-center px-4 py-2 bg-primary/10 text-primary rounded-full text-sm font-medium">
                    <i class="fas fa-shipping-fast mr-2"></i>
                    Швидка доставка
                </div>
                
                {{-- Main Title --}}
                <div class="space-y-6">
                    <h1 class="text-4xl lg:text-6xl font-bold leading-tight text-gray-900">
                        <span class="text-primary">Доставка</span> одягу
                    </h1>
                    <p class="text-xl text-gray-600 leading-relaxed max-w-2xl">
                        Швидка та надійна доставка ваших речей прямо до дверей. 
                    </p>
                </div>
                
                {{-- CTA Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <button class="gradient-button text-white px-8 py-4 rounded-2xl font-semibold text-lg transition-all duration-300 shadow-lg hover:shadow-2xl modal_fade" data-modal="feedbackmd">
                        <i class="fas fa-phone mr-2"></i>
                        Замовити доставку
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delivery Info --}}
    @include('includes.elements.delivery-box')

    {{-- Courier Form --}}
    @include('includes.elements.courier_form-box')
@endsection

@section('scripts')
    <script src="{{ asset('js/scripts/price_slide.js') }}"></script>
@endsection
