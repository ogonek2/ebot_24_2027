@extends('layouts.app')

@section('title')
    Викликати кур'єра - Екочистка одягу та домашнього текстилю
@endsection

@php
    $breadcrumbs = [breadcrumb_home(), ['name' => 'Викликати кур\'єра']];
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
                    <i class="fas fa-truck mr-2"></i>
                    Безкоштовна доставка
                </div>
                
                {{-- Main Title --}}
                <div class="space-y-6">
                    <h1 class="text-4xl lg:text-6xl font-bold leading-tight text-gray-900">
                        Викликати <span class="text-primary">кур'єра</span>
                    </h1>
                    <p class="text-xl text-gray-600 leading-relaxed max-w-2xl">
                        Швидко, зручно та безкоштовно. Кур'єр приїде за вашими речами в 
                        <span class="font-semibold text-primary">зручний час</span>. 
                        Без черг, без турбот!
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Courier Form --}}
    @include('includes.elements.courier_form-box')

    {{-- Delivery Info --}}
    @include('includes.elements.delivery-box')

    {{-- Consultation Section --}}
    @include('includes.elements.consultation')
@endsection