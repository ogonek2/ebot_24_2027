@extends('layouts.app')

@section('title')
    {{ $promotion->name }} - Акції / Активна знижка на послуги з хімчистки. Єнот 24 / Знижка {{ $promotion->discount_action }}
@endsection

@php
    $siteName = config('app.name', 'ЄНОТ 24');
    $pageTitle = $promotion->name . ' - Акції - ' . $siteName;
    $pageDescription = $promotion->discount_action 
        ? Str::limit(strip_tags($promotion->discount_action), 160)
        : 'Спеціальна акція від ЄНОТ 24. Хімчистка одягу та домашнього текстилю з кур\'єрською доставкою.';
    $pageUrl = route('promotion_page', $promotion->id);
    
    // Используем баннер акции или дефолтное изображение
    $ogImage = $promotion->banner 
        ? asset('storage/' . $promotion->banner)
        : asset('storage/src/logo/full_logo.svg');
@endphp

@section('seo_tags')
    {{-- Basic Meta Tags --}}
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="keywords" content="акції, знижки, хімчистка, одяг, текстиль, {{ $promotion->name }}, ЄНОТ 24">
    
    {{-- Open Graph Meta Tags --}}
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ $pageUrl }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $promotion->name }}">
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
    $breadcrumbs = [
        breadcrumb_home(),
        ['name' => 'Акції', 'url' => route('promotions')],
        ['name' => $promotion->name ?? 'Акція'],
    ];
@endphp

@section('content')
    <div class="pb-8 md:pb-12">
        <div class="container mx-auto px-4 md:px-6">
            @include('includes.elements.breadcrumbs', ['breadcrumbs' => $breadcrumbs, 'wrapperClass' => 'px-0'])

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Main Content --}}
                <div class="lg:col-span-2">
                    {{-- Conditions --}}
                    @if($promotion->umowy)
                        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 mb-6">
                            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                                <i class="fas fa-file-contract text-primary"></i>
                                Умови акції
                            </h2>
                            <div class="prose prose-lg max-w-none text-gray-700">
                                <p class="text-base md:text-lg leading-relaxed whitespace-pre-line">
                                    {!! $promotion->umowy !!}
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- CTA Button --}}
                    <div class="bg-gradient-to-r from-primary to-secondary rounded-2xl shadow-lg p-6 md:p-8 text-center">
                        <h3 class="text-2xl md:text-3xl font-bold text-white mb-4">
                            Отримати консультацію
                        </h3>
                        <p class="text-white/90 mb-6 text-lg">
                            Залиште заявку і ми зв'яжемося з вами найближчим часом
                        </p>
                        <button class="modal_fade inline-block bg-white text-primary px-8 py-4 rounded-xl font-semibold text-lg hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                                data-modal="feedbackmd">
                            <i class="fas fa-phone mr-2"></i>
                            Зв'язатися з нами
                        </button>
                    </div>
                </div>

                {{-- Sidebar - Other Promotions --}}
                @if($otherPromotions->count() > 0)
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
                            <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-6">
                                Інші акції
                            </h3>
                            <div class="space-y-4">
                                @foreach($otherPromotions as $otherPromotion)
                                    <a href="{{ route('promotion_page', $otherPromotion->id) }}" 
                                       class="block group hover:bg-gray-50 rounded-xl p-4 transition-colors duration-300">
                                        <h4 class="font-bold text-gray-900 group-hover:text-primary transition-colors duration-300 mb-2">
                                            {{ $otherPromotion->name }}
                                        </h4>
                                        @if($otherPromotion->discount_action)
                                            <p class="text-sm text-gray-600 line-clamp-2">
                                                {{ Str::limit($otherPromotion->discount_action, 80) }}
                                            </p>
                                        @endif
                                        <div class="mt-2 flex items-center gap-2 text-primary text-sm font-semibold">
                                            <span>Детальніше</span>
                                            <i class="fas fa-arrow-right"></i>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                            <a href="{{ route('promotions') }}" 
                               class="mt-6 block text-center text-primary font-semibold hover:text-primary/80 transition-colors duration-300">
                                Всі акції →
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
