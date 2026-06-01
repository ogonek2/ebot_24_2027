@extends('layouts.app')

@section('title')
    {{ $b2b->title }} / B2B - Екочистка одягу та домашнього текстилю
@endsection

@php
    $breadcrumbs = [
        breadcrumb_home(),
        ['name' => 'Для бізнесу', 'url' => route('b2b_page')],
        ['name' => $b2b->name ?? $b2b->title],
    ];
@endphp

@section('content')
    @include('includes.elements.breadcrumbs', ['breadcrumbs' => $breadcrumbs])

    {{-- Services Navigation --}}
    @include('includes.elements.header-3-box')
    
    {{-- Hero Section --}}
    <div class="relative bg-gradient-to-br from-primary to-secondary py-20 overflow-hidden">
        {{-- Background Pattern --}}
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-30"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center flex flex-col-reverse lg:flex-row">
                {{-- Content --}}
                <div class="text-white space-y-6 animate-fade-in-left">
                    {{-- Main Title --}}
                    <h1 class="text-4xl lg:text-5xl font-bold leading-tight">
                        {{ $b2b->title }}
                    </h1>
                    
                    {{-- Description --}}
                    <p class="text-lg text-gray-200 leading-relaxed">
                        {{ $b2b->description ?? 'Професійна послуга для вашого бізнесу з кур\'єрською доставкою.' }}
                    </p>
                    
                    {{-- CTA Button --}}
                    <div class="pt-4">
                        <button class="bg-white text-secondary hover:bg-gray-100 px-8 py-4 rounded-full font-semibold text-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl modal_fade" data-modal="feedbackmd">
                            <i class="fas fa-phone mr-2"></i>
                            Замовити зараз
                        </button>
                    </div>
                </div>
                
                {{-- Hero Image --}}
                <div class="relative animate-fade-in-right">
                    <div class="relative z-10">
                        @if ($b2b->banner == 'Empty')
                            <div class="h-96 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-2xl flex items-center justify-center">
                                <img src="{{ asset('storage/source/images/bgk_vcloyhe.png') }}" alt="{{ $b2b->name }}" class="w-32 h-32 opacity-50" loading="lazy">
                            </div>
                        @else
                            <img src="{{ asset('storage/' . $b2b->banner) }}" 
                                 alt="{{ $b2b->name }}" 
                                 class="w-full h-96 object-cover rounded-2xl shadow-2xl"
                                 loading="lazy">
                        @endif
                    </div>
                    {{-- Floating Elements --}}
                    <div class="absolute -top-4 -right-4 bg-primary text-white p-4 rounded-full shadow-lg animate-bounce-slow">
                        <i class="fas fa-truck text-2xl"></i>
                    </div>
                    <div class="absolute -bottom-4 -left-4 bg-success text-white p-4 rounded-full shadow-lg animate-pulse-slow">
                        <i class="fas fa-check text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Service Features --}}
    <div class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-secondary mb-4">Чому обирають нас</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Професійний підхід, швидка доставка та гарантія якості
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-white p-8 rounded-2xl shadow-lg text-center hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="bg-primary/10 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Гарантія якості</h3>
                    <p class="text-gray-600">100% гарантія на всі наші послуги</p>
                </div>
                
                <div class="bg-white p-8 rounded-2xl shadow-lg text-center hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="bg-primary/10 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Швидке виконання</h3>
                    <p class="text-gray-600">Терміни виконання від 24 годин</p>
                </div>
                
                <div class="bg-white p-8 rounded-2xl shadow-lg text-center hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="bg-primary/10 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-leaf text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Екологічність</h3>
                    <p class="text-gray-600">Використання екологічно чистих засобів</p>
                </div>
                
                <div class="bg-white p-8 rounded-2xl shadow-lg text-center hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="bg-primary/10 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-tie text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Персональний менеджер</h3>
                    <p class="text-gray-600">Індивідуальний підхід до кожного клієнта</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Courier Service Highlight --}}
    <div class="bg-gradient-to-r from-primary to-secondary py-16">
        <div class="container mx-auto px-4">
            <div class="text-center text-white mb-12">
                <h2 class="text-4xl font-bold mb-4">
                    <i class="fas fa-truck mr-3"></i>
                    Кур'єрська доставка
                </h2>
                <p class="text-xl max-w-3xl mx-auto opacity-90">
                    Забираємо та доставляємо ваші речі прямо до офісу. Без черг, без турбот!
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white/10 backdrop-blur-sm p-8 rounded-2xl text-center text-white hover:bg-white/20 transition-all duration-300">
                    <div class="bg-white/20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-map-marker-alt text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Забираємо</h3>
                    <p class="opacity-90">Кур'єр приїжджає за вашими речами в зручний час</p>
                </div>
                
                <div class="bg-white/10 backdrop-blur-sm p-8 rounded-2xl text-center text-white hover:bg-white/20 transition-all duration-300">
                    <div class="bg-white/20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-cogs text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Обробляємо</h3>
                    <p class="opacity-90">Професійна екочистка на сучасному обладнанні</p>
                </div>
                
                <div class="bg-white/10 backdrop-blur-sm p-8 rounded-2xl text-center text-white hover:bg-white/20 transition-all duration-300">
                    <div class="bg-white/20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-home text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Доставляємо</h3>
                    <p class="opacity-90">Повертаємо чистий одяг прямо до вашого офісу</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Consultation Section --}}
    @include('includes.elements.consultation')
    
    {{-- Courier Form --}}
    @include('includes.elements.courier_form-box')
    
    {{-- Delivery Info --}}
    @include('includes.elements.delivery-box')
@endsection

@section('scripts')
    <script src="{{ asset('js/scripts/price_slide.js') }}"></script>
    <script>
        // Page-specific animations
        document.addEventListener('DOMContentLoaded', function() {
            // Add floating animation to feature cards
            document.querySelectorAll('.grid > div').forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('animate-fade-in-up');
            });
        });
    </script>
@endsection
