@extends('layouts.app')

@section('title')
    Акції - Єнот 24 / Знижка на послуги з хімчистки. Актуальні акції
@endsection

@php
    $breadcrumbs = [breadcrumb_home(), ['name' => 'Акції']];
@endphp

@section('content')
    <div class="pb-8 md:pb-12">
        <div class="container mx-auto px-4 md:px-6">
            @include('includes.elements.breadcrumbs', ['breadcrumbs' => $breadcrumbs, 'wrapperClass' => 'px-0'])

            {{-- Promotions Block Component --}}
            @if($promotions->count() > 0)
                <div id="promotions-block-app" data-promotions="{{ json_encode($promotions->map(function($discount) {
                    return [
                        'id' => $discount->id,
                        'name' => $discount->name ?? 'Акція',
                        'discount_action' => $discount->discount_action ?? '-50%',
                        'locations' => $discount->locations ?? 'Всі',
                        'color' => $discount->color ?? null,
                        'text_color' => $discount->text_color ?? null,
                        'discount_color' => $discount->discount_color ?? null,
                        'banner' => $discount->banner ? asset('storage/' . $discount->banner) : null
                    ];
                })) }}"></div>
            @else
                {{-- No Promotions --}}
                <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-gift text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">
                        Наразі акцій немає
                    </h3>
                    <p class="text-gray-600 mb-6">
                        Запрошуємо вас переглянути наші послуги та ціни
                    </p>
                    <a href="{{ route('services') }}" 
                       class="inline-block bg-gradient-to-r from-primary to-secondary text-white px-8 py-3 rounded-xl font-semibold hover:from-primary/90 hover:to-secondary/90 transition-all duration-300 shadow-lg hover:shadow-xl">
                        Переглянути послуги
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
