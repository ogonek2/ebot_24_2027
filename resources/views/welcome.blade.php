@extends('layouts.app')

@section('title')
    Єнот-24 - Прання/Хімчистка Килимів та Одягу в Києві
@endsection
@section('seo_tags')
    <meta name="description" content="Замовити послугу прання/хімчистки килимів та одягу в компанії ЄНОТ-24. Краща якість хімчистки. Безпечна хімія. Кур'єрська доставка по Києву.">

    <meta property="og:description"
        content="Замовити послугу прання/хімчистки килимів та одягу Ви можете в компанії Єнот-24. Краща якість хімчистки. Безпечна хімія. Найкоротші терміни. Замовляйте просто зараз!">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('storage/src/logo/enot-white-bg.png') }}">
    <meta name="format-detection" content="telephone=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    
    <!-- Локалізація та адреса -->
    <meta name="geo.position" content="50.4501;30.5234">
    <meta name="geo.region" content="UA-30">
    <meta name="geo.placename" content="Київ, Україна">
    <meta name="DC.title" content="Єнот-24 - Прання/Хімчистка Килимів та Одягу в Києві">
    <meta name="DC.description" content="Замовити послугу прання/хімчистки килимів та одягу Ви можете в компанії Єнот-24. Краща якість хімчистки. Безпечна хімія. Найкоротші терміни. Замовляйте просто зараз!">
    <meta name="DC.subject" content="Прання, хімчистка, Київ, Єнот-24, чистка килимів, одяг">
    
    <!-- Оптимізація для пошукових систем -->
    <meta name="robots" content="{{ config('seo.robots_index') }}">
    <link rel="canonical" href="{{ seo_canonical('/') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Єнот-24 - Прання/Хімчистка Килимів та Одягу в Києві">
    <meta property="og:description" content="Якісні послуги прання та хімчистки в Києві. Звертайтеся до Єнот-24 для чистоти, яку ви заслуговуєте!">
    <meta property="og:url" content="{{ seo_canonical('/') }}">
    
    <!-- Додаткові метатеги для SEO -->
    <meta name="city" content="Київ">
    <meta name="coverage" content="Київ, Україна">
    <meta name="revisit-after" content="3 days">
    <meta name="author" content="Єнот-24">
    <meta name="rating" content="general">
    
    <!-- Контактна інформація для локального SEO -->
    <meta name="business" content="Єнот-24 - Прання/Хімчистка Килимів та Одягу в Києві">
    <meta name="address" content="Київ, Україна">
    <meta name="phone" content="+38 (067) 887-2233">
    
    <!-- Покращення швидкості відображення сторінки -->
    <meta http-equiv="x-dns-prefetch-control" content="on">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://enot-24.com.ua/" crossorigin>
    
    <!-- Підказка для мобільних пристроїв (Google та інші пошукові системи враховують адаптивність сайту) -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="format-detection" content="telephone=yes">
    
    <!-- Додаткові метатеги для пошукової оптимізації -->
    <meta property="og:locale" content="uk_UA">
    <meta property="og:site_name" content="Єнот-24">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Єнот-24 - Прання/Хімчистка Килимів та Одягу в Києві">
    <meta name="twitter:description" content="Професійне прання та хімчистка килимів і одягу в Києві. Чистота, якість та вигідні ціни від Єнот-24!">
    <meta name="twitter:url" content="{{ seo_canonical('/') }}">
    
    <!-- Локалізаційні теги для Google My Business -->
    <meta itemprop="name" content="Єнот-24 - Прання/Хімчистка Килимів та Одягу в Києві">
    <meta itemprop="addressLocality" content="Київ">
    <meta itemprop="telephone" content="+38 (067) 887-2233">
    
    <!-- Підказки для пошукових систем -->
    <meta name="google-site-verification" content="-Tye_Cwi5cK0K8x7A1C8Heuxg5Nmxgjh-H5j3vGd6gQ" />
    
    <!-- Теги для локального SEO, спрямовані на підвищення залученості користувачів -->
    <meta name="service" content="Прання килимів Київ, хімчистка одягу Київ, чистка меблів, професійне прання">
    <meta name="distribution" content="global">
    <meta name="target" content="all">
    <meta name="audience" content="Кияни, люди, зацікавлені у чистоті та догляді за одягом і килимами">
    
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "CleaningService",
      "name": "ЄНОТ-24",
      "url": "{{ seo_site_url() }}",
      "telephone": "{{ config('seo.telephone') }}",
      "image": "{{ seo_og_image() }}",
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Київ",
        "addressCountry": "UA"
      },
      "openingHours": "Mo-Su 09:00-21:00",
      "priceRange": "$$",
      "description": "Професійні послуги прання та хімчистки одягу в Києві від ЄНОТ-24."
    }
    </script>
@endsection

@section('content')
    {{-- Hero Section with Background Image --}}
    <div class="relative flex items-center overflow-hidden">
        <div class="container mx-auto relative z-10">
            <div class="flex flex-col justify-center items-center">
                {{-- Left Side - White Box with Content --}}
                <div class="w-full">
                    {{-- White Content Box --}}
                    <div class="bg-white flex justify-center text-center md:text-left md:justify-start lg:justify-start lg:items-center align-stretch h-full rounded-2xl py-6 px-8 relative min-h-96 overflow-hidden">
                        <div class="h-100 flex flex-col justify-between gap-2 relative" style="z-index: 100;">
                            
                            <div class="py-4 mb-6">
                                {{-- Main Title --}}
                                <div>
                                    <p class="text-md text-black font-medium leading-tight">
                                        #найякісніша_хімчистка_столиці
                                    </p>
                                    <h1 class="text-6xl md:text-8xl lg:text-8xl uppercase text-enot-pink font-bold leading-tight">
                                        <span class="text-enot-purple"></span>хімчистка</span><br>одягу
                                    </h1>
                                    <p class="text-xl md:text-2xl uppercase text-black font-bold py-3">
                                        Гипоаллергенно, якісно, та з<br>увагою до деталей
                                    </p>
                                </div>

                                {{-- CTA Buttons --}}
                                <div class="flex flex-col sm:flex-row gap-3 sm:gap-2">
                                    <a href="{{ route('services') }}" class="w-full">
                                        <button class="text-white w-full text-sm bg-enot-pink p-4 border border-enot-pink rounded-full">
                                            Послуги та ціни
                                        </button>
                                    </a>
                                    <button
                                        class="text-black text-sm border border-black rounded-full p-4 modal_fade w-full"
                                        data-modal="feedbackmd">
                                        Замовити хімчистку
                                    </button>
                                </div>
                            </div>
                            <div>
                                <ul class="flex items-center gap-2">
                                    <li>
                                        <a href="https://t.me/enot24ServiceBot" target="_blank" class="text-2xl hover:text-primary">
                                            <i class="fab fa-telegram"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://instagram.com/enot24cleaner" target="_blank" class="text-2xl hover:text-primary">
                                            <i class="fab fa-instagram"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="absolute bottom-0 top-0 right-[0%] w-100 h-100 opacity-20 block lg:hidden" style="z-index: 10;">
                            <img src="{{ asset('storage/src/ill/Frame 1792.svg') }}" alt="enot 24 banner lines" class="w-100 h-full">
                        </div>
                        <div class="absolute bottom-0 top-0 right-[0%] w-100 h-100 hidden lg:block" style="z-index: 10;">
                            <img src="{{ asset('storage/src/ill/backgrond.svg') }}" alt="enot 24 banner lines" class="w-100 h-full">
                        </div>
                        
                        {{-- Interactive Pink Circle --}}
                        <div id="hero-interactive-circle" class="hero-interactive-circle hidden lg:flex" data-modal="feedbackmd">
                            <span class="hero-circle-text">Замовити хімчистку</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Services Navigation --}}
    @include('includes.elements.header-3-box')

    {{-- Promotions Slider --}}
    @include('includes.elements.promotions-slider')

    {{-- Price Section --}}
    @include('includes.elements.price-box')

    {{-- Branches Slider --}}
    @include('includes.elements.branches-slider')

    {{-- Services Navigation --}}
    <!-- @include('includes.elements.header-2-box') -->

    {{-- Consultation Section --}}
    @include('includes.elements.consultation')

    {{-- Recommendations Section --}}
    @include('includes.elements.recommendations-slider')

    @include('includes.elements.blog-slider')

    {{-- Delivery Section --}}
    @include('includes.elements.delivery-box')

    {{-- Courier Form --}}
    @include('includes.elements.courier_form-box')
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const benefitItems = document.querySelectorAll('.grid.grid-cols-2.lg\\:grid-cols-4 > div');
            benefitItems.forEach(function(item, index) {
                item.style.animationDelay = (index * 0.2) + 's';
                item.classList.add('animate-fade-in-up');
            });
        });
    </script>
@endsection