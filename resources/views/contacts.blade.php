@extends('layouts.app')

@section('title')
    Контакти - Єнот 24 / Хімчистка одягу та килимів у Києві
@endsection

@php
    $siteName = config('app.name', 'ЄНОТ 24');
    $pageTitle = 'Контакти - ' . $siteName;
    $pageDescription = 'Зв\'яжіться з ЄНОТ 24 будь-яким зручним способом. Телефони, email, Instagram. Ми завжди на зв\'язку! Хімчистка одягу та домашнього текстилю з кур\'єрською доставкою.';
    $pageUrl = route('contacts_page');
    
    // Используем дефолтное изображение для страницы контактов
    $ogImage = asset('storage/src/logo/full_logo.svg');
    
    // Формируем keywords
    $keywords = 'контакти, телефон, email, Instagram, зв\'язок, хімчистка, ЄНОТ 24, кур\'єрська доставка, одяг, текстиль';
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
    $breadcrumbs = [breadcrumb_home(), ['name' => 'Контакти']];
@endphp

@section('content')
    @include('includes.elements.breadcrumbs', ['breadcrumbs' => $breadcrumbs])

    {{-- Hero Section --}}
    <div class="container mx-auto px-4 mb-20">
        <div class="flex flex-col items-center text-center space-y-6">
            {{-- Badge --}}
            <div class="inline-flex items-center px-4 py-2 bg-primary/10 text-primary rounded-full text-sm font-medium">
                <i class="fas fa-address-card mr-2"></i>
                Зв'яжіться з нами
            </div>
            
            {{-- Main Title --}}
            <div class="space-y-4">
                <h1 class="text-4xl lg:text-6xl font-bold leading-tight text-gray-900">
                    Наші <span class="text-primary">контакти</span>
                </h1>
                <p class="text-xl text-gray-600 leading-relaxed max-w-2xl">
                    Ми завжди на зв'язку! Зв'яжіться з нами будь-яким зручним способом
                </p>
            </div>
        </div>
    </div>

    {{-- Contacts Section --}}
    <div class="container mx-auto px-4 mb-20">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
            {{-- Website --}}
            <a href="https://enot-24.com.ua" target="_blank" class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-primary/20">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary/20 to-accent/20 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-globe text-primary text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Веб-сайт</h3>
                        <p class="text-primary font-semibold">enot-24.com.ua</p>
                    </div>
                    <div class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-external-link-alt mr-1"></i>
                        Відкрити сайт
                    </div>
                </div>
            </a>

            {{-- Instagram --}}
            <a href="https://instagram.com/enot24cleaner" target="_blank" class="group bg-gradient-to-br from-pink-500 to-purple-600 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 text-white">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fab fa-instagram text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold mb-2">Instagram</h3>
                        <p class="font-semibold">@enot24cleaner</p>
                    </div>
                    <div class="text-sm text-white/80 mt-2">
                        <i class="fas fa-external-link-alt mr-1"></i>
                        Перейти до профілю
                    </div>
                </div>
            </a>

            {{-- Phone 1 --}}
            <a href="tel:+380678872233" class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-primary/20">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary/20 to-accent/20 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-phone text-primary text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Телефон</h3>
                        <p class="text-primary font-semibold text-lg">+38 (067) 887-22-33</p>
                    </div>
                    <div class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-phone-alt mr-1"></i>
                        Зателефонувати
                    </div>
                </div>
            </a>

            {{-- Phone 2 --}}
            <a href="tel:+380443372233" class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-primary/20">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary/20 to-accent/20 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-phone text-primary text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Телефон</h3>
                        <p class="text-primary font-semibold text-lg">+38 (044) 337-22-33</p>
                    </div>
                    <div class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-phone-alt mr-1"></i>
                        Зателефонувати
                    </div>
                </div>
            </a>

            {{-- Email --}}
            <a href="mailto:office.enot24@gmail.com" class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-primary/20 md:col-span-2 lg:col-span-1">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary/20 to-accent/20 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-envelope text-primary text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Email</h3>
                        <p class="text-primary font-semibold break-all">office.enot24@gmail.com</p>
                    </div>
                    <div class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-envelope-open mr-1"></i>
                        Написати листа
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Consultation Section --}}
    @include('includes.elements.consultation')
@endsection

