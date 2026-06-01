@extends('layouts.app')

@php
    use App\Support\SeoResolver;

    $siteName = config('seo.site_name', 'ЄНОТ-24');
    $servicesCount = $category->getAllServices()->count();
    $fallbackTitle = $category->name . ' у Києві — послуги та ціни | ' . $siteName;
    $fallbackDescription = 'Послуги ' . $category->name . ' від ' . $siteName . '. '
        . ($servicesCount > 0 ? $servicesCount . ' послуг у категорії. ' : '')
        . 'Хімчистка з кур\'єрською доставкою. Актуальні ціни.';
    $serviceNames = $category->services->take(5)->pluck('name')->implode(', ');
    $fallbackKeywords = $category->name . ', хімчистка, послуги, ціни, Київ, ' . $siteName
        . ($serviceNames ? ', ' . $serviceNames : '');

    $pageTitle = SeoResolver::title($category, $fallbackTitle);
    $pageDescription = SeoResolver::description($category, $fallbackDescription);
    $pageUrl = SeoResolver::canonical($category, route('category_page', $category->href, false));
    $keywords = SeoResolver::keywords($category, $fallbackKeywords);
    $robots = SeoResolver::robots($category);
    $ogTitle = SeoResolver::ogTitle($category, $pageTitle);
    $ogDescription = SeoResolver::ogDescription($category, $pageDescription);
    $ogImage = SeoResolver::ogImageUrl($category);
@endphp

@section('title')
    {{ $pageTitle }}
@endsection

@section('seo_tags')
    @include('includes.seo.meta', [
        'pageTitle' => $pageTitle,
        'pageDescription' => $pageDescription,
        'pageUrl' => $pageUrl,
        'keywords' => $keywords,
        'robots' => $robots,
        'ogTitle' => $ogTitle,
        'ogDescription' => $ogDescription,
        'ogImage' => $ogImage,
        'modifiedTime' => optional($category->updated_at)->toAtomString(),
    ])
@endsection

@php
    $breadcrumbs = [
        breadcrumb_home(),
        ['name' => 'Послуги та ціни', 'url' => route('services')],
        ['name' => $category->name],
    ];
@endphp

@section('content')
    <div class="pb-8 md:pb-12">
        <div class="container mx-auto md:px-4">
            @include('includes.elements.breadcrumbs', ['breadcrumbs' => $breadcrumbs, 'wrapperClass' => 'px-0 md:px-0'])

            {{-- Category Header --}}
            <div class="mb-2 md:mb-4">
                <div class="py-4">
                    <div class="flex flex-col md:flex-row items-center gap-4">
                        {{-- Category Image --}}
                        @if($category->category_img)
                            <div class="flex-shrink-0">
                                <img src="{{ asset('storage/' . $category->category_img) }}" 
                                     alt="{{ $category->name }}" 
                                     class="w-[60px] h-[60px] rounded-full object-cover">
                            </div>
                        @else
                            <div class="flex-shrink-0 w-24 h-24 md:w-32 md:h-32 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center">
                                <i class="fas fa-tag text-4xl md:text-5xl text-primary/50"></i>
                            </div>
                        @endif
                        
                        {{-- Category Info --}}
                        <div class="flex-1 text-center md:text-left">
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                                {{ $category->name }}
                            </h1>
                            <p class="text-gray-600 text-lg">
                                {{ $category->getAllServices()->count() }} {{ $category->getAllServices()->count() === 1 ? 'послуга' : 'послуг' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Subcategories Navigation (if exists) --}}
            @if($category->children->count() > 0)
                <div class="mb-6">
                    <div class="bg-white rounded-xl shadow-md p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Підкатегорії:</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($category->children as $child)
                                <a href="#category-{{ $child->id }}" 
                                   class="px-4 py-2 bg-gray-100 hover:bg-primary hover:text-white rounded-lg transition-colors duration-200 text-sm font-medium">
                                    {{ $child->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Services List from Main Category --}}
            @if($category->services->count() > 0)
                <div class="mb-8 md:mb-12" id="category-{{ $category->id }}">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        {{-- Table Headers --}}
                        <div class="hidden md:grid md:grid-cols-12 gap-4 bg-gradient-to-r from-gray-50 to-gray-100 px-6 md:px-8 py-4 border-b-2 border-gray-200">
                            <div class="md:col-span-6">
                                <span class="text-sm font-bold text-gray-800 uppercase">Послуга</span>
                            </div>
                            <div class="md:col-span-2 text-right">
                                <span class="text-sm font-bold text-gray-800 uppercase">Потокова ціна</span>
                            </div>
                            <div class="md:col-span-2 text-right">
                                <span class="text-sm font-bold text-gray-800 uppercase">Індивідуальна ціна</span>
                            </div>
                            <div class="md:col-span-2 text-right">
                                <span class="text-sm font-bold text-gray-800 uppercase">Дія</span>
                            </div>
                        </div>
                        
                        <div class="divide-y divide-gray-200">
                            @foreach($category->services as $index => $service)
                                <div class="px-6 md:px-8 py-4 md:py-6 hover:bg-gray-50 transition-colors duration-200 group">
                                    <div class="grid grid-cols-2 md:grid-cols-12 items-center gap-4">
                                        
                                        {{-- Service Name --}}
                                        <div class="md:col-span-6 col-span-2">
                                            @php
                                                $primaryCategory = $service->getPrimaryCategory() ?? $category;
                                            @endphp
                                            <a href="{{ route('service_page', [$primaryCategory->href, $service->transform_url ?? $service->href]) }}" 
                                               class="block">
                                                <h3 class="text-lg font-sans text-gray-800 group-hover:text-primary transition-colors duration-200 cursor-pointer">
                                                    {{ $service->name }}
                                                </h3>
                                                @if($service->marker !== null) <span class="p-2 rounded-xl font-sans gradient-button from-primary via-secondary to-primary text-white block mt-2" style="width: fit-content;">{{ $service->marker }}</span> @endif
                                            </a>
                                        </div>
                                        
                                        {{-- Current Price --}}
                                        <div class="md:col-span-2 text-left md:text-right">
                                            <span class="text-sm text-gray-500 md:hidden">Потокова</span><br class="md:hidden">
                                            @php
                                                $originalPrice = floatval($service->price ?? 0);
                                                $hasPrice = $originalPrice > 0;
                                            @endphp
                                            @if($hasPrice)
                                                @if($category->hasActiveDiscount())
                                                    <div class="flex flex-col items-start md:items-end gap-1">
                                                        <span class="text-gray-400 text-sm line-through">
                                                            {{ number_format($originalPrice, 0, ',', ' ') }} грн
                                                        </span>
                                                        <span class="text-lg font-bold text-primary">
                                                            {{ number_format(floatval($category->calculateDiscountedPrice($originalPrice)), 0, ',', ' ') }} грн
                                                        </span>
                                                    </div>
                                                @else
                                                    <span class="text-base font-bold text-primary">
                                                        {{ number_format($originalPrice, 0, ',', ' ') }} грн
                                                        @if($service->marker !== null)<br><span class="text-right font-sans text-sm text-secondary">Акційна ціна!</span> @endif
                                                    </span>
                                                    
                                                @endif
                                            @else
                                                <span class="text-sm text-gray-400 italic">Ціна за запитом</span>
                                            @endif
                                        </div>
                                        
                                        {{-- Individual Price --}}
                                        <div class="md:col-span-2 text-left md:text-right">
                                            <span class="text-sm text-gray-500 md:hidden">Індивідуальна</span><br class="md:hidden">
                                            @if($service->individual_price)
                                                <span class="text-base font-bold text-secondary">
                                                    {{ number_format($service->individual_price, 0, ',', ' ') }} грн
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-sm italic">—</span>
                                            @endif
                                        </div>
                                        
                                        {{-- Add to Cart Button --}}
                                        <div class="md:col-span-2 flex items-center justify-end col-span-2 md:col-span-1">
                                            @php
                                                $finalPrice = $hasPrice ? ($category->hasActiveDiscount() ? $category->calculateDiscountedPrice($originalPrice) : $originalPrice) : 0;
                                            @endphp
                                            @if($hasPrice)
                                                @include('components.add-to-cart-button', [
                                                    'serviceId' => $service->id,
                                                    'serviceName' => $service->name,
                                                    'hasIndividual' => ($service->individual_price ?? 0) > 0,
                                                    'price' => $finalPrice,
                                                    'individualPrice' => $service->individual_price ?? 0
                                                ])
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Services from Subcategories --}}
            @if($category->children->count() > 0)
                @foreach($category->children as $childCategory)
                    @if($childCategory->services->count() > 0)
                        <div class="mb-8 md:mb-12" id="category-{{ $childCategory->id }}">
                            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                                {{-- Subcategory Header --}}
                                <div class="bg-gradient-to-r from-primary/10 to-secondary/10 px-6 md:px-8 py-4 border-b-2 border-primary/20">
                                    <h2 class="text-xl md:text-2xl font-bold text-gray-800">
                                        {{ $childCategory->name }}
                                        <span class="text-sm font-normal text-gray-600 ml-2">
                                            ({{ $childCategory->services->count() }} {{ $childCategory->services->count() === 1 ? 'послуга' : 'послуг' }})
                                        </span>
                                    </h2>
                                </div>
                                
                                {{-- Table Headers --}}
                                <div class="hidden md:grid md:grid-cols-12 gap-4 bg-gradient-to-r from-gray-50 to-gray-100 px-6 md:px-8 py-4 border-b-2 border-gray-200">
                                    <div class="md:col-span-6">
                                        <span class="text-sm font-bold text-gray-800 uppercase">Послуга</span>
                                    </div>
                                    <div class="md:col-span-2 text-right">
                                        <span class="text-sm font-bold text-gray-800 uppercase">Потокова ціна</span>
                                    </div>
                                    <div class="md:col-span-2 text-right">
                                        <span class="text-sm font-bold text-gray-800 uppercase">Індивідуальна ціна</span>
                                    </div>
                                    <div class="md:col-span-2 text-right">
                                        <span class="text-sm font-bold text-gray-800 uppercase">Дія</span>
                                    </div>
                                </div>
                                
                                <div class="divide-y divide-gray-200">
                                    @foreach($childCategory->services as $index => $service)
                                        <div class="px-6 md:px-8 py-4 md:py-6 hover:bg-gray-50 transition-colors duration-200 group">
                                            <div class="grid grid-cols-2 md:grid-cols-12 items-center gap-4">
                                                
                                                {{-- Service Name --}}
                                                <div class="md:col-span-6 col-span-2">
                                                    @php
                                                $primaryCategory = $service->getPrimaryCategory() ?? $category;
                                            @endphp
                                            <a href="{{ route('service_page', [$primaryCategory->href, $service->transform_url ?? $service->href]) }}" 
                                                       class="block">
                                                        <h3 class="text-lg font-sans text-gray-800 group-hover:text-primary transition-colors duration-200 cursor-pointer">
                                                            {{ $service->name }}
                                                        </h3>
                                                        @if($service->marker !== null) <span class="p-2 rounded-xl font-sans gradient-button from-primary via-secondary to-primary text-white block mt-2" style="width: fit-content;">{{ $service->marker }}</span> @endif
                                                    </a>
                                                </div>
                                                
                                                {{-- Current Price --}}
                                                <div class="md:col-span-2 text-left md:text-right">
                                                    <span class="text-sm text-gray-500 md:hidden">Потокова</span><br class="md:hidden">
                                                    @php
                                                        $originalPrice = floatval($service->price ?? 0);
                                                        $hasPrice = $originalPrice > 0;
                                                    @endphp
                                                    @if($hasPrice)
                                                        @if($childCategory->hasActiveDiscount() || $category->hasActiveDiscount())
                                                            @php
                                                                $activeDiscount = $childCategory->hasActiveDiscount() ? $childCategory : $category;
                                                            @endphp
                                                            <div class="flex flex-col items-start md:items-end gap-1">
                                                                <span class="text-gray-400 text-sm line-through">
                                                                    {{ number_format($originalPrice, 0, ',', ' ') }} грн
                                                                </span>
                                                                <span class="text-lg font-bold text-primary">
                                                                    {{ number_format(floatval($activeDiscount->calculateDiscountedPrice($originalPrice)), 0, ',', ' ') }} грн
                                                                </span>
                                                            </div>
                                                        @else
                                                            <span class="text-base font-bold text-primary">
                                                                {{ number_format($originalPrice, 0, ',', ' ') }} грн
                                                                @if($service->marker !== null)<br><span class="text-right font-sans text-sm text-secondary">Акційна ціна!</span> @endif
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span class="text-sm text-gray-400 italic">Ціна за запитом</span>
                                                    @endif
                                                </div>
                                                
                                                {{-- Individual Price --}}
                                                <div class="md:col-span-2 text-left md:text-right">
                                                    <span class="text-sm text-gray-500 md:hidden">Індивідуальна</span><br class="md:hidden">
                                                    @if($service->individual_price)
                                                        <span class="text-base font-bold text-secondary">
                                                            {{ number_format($service->individual_price, 0, ',', ' ') }} грн
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400 text-sm italic">—</span>
                                                    @endif
                                                </div>
                                                
                                                {{-- Add to Cart Button --}}
                                                <div class="md:col-span-2 flex items-center justify-end col-span-2 md:col-span-1">
                                                    @php
                                                        $activeDiscount = $childCategory->hasActiveDiscount() ? $childCategory : ($category->hasActiveDiscount() ? $category : null);
                                                        $finalPrice = $hasPrice ? ($activeDiscount ? $activeDiscount->calculateDiscountedPrice($originalPrice) : $originalPrice) : 0;
                                                    @endphp
                                                    @if($hasPrice)
                                                        @include('components.add-to-cart-button', [
                                                            'serviceId' => $service->id,
                                                            'serviceName' => $service->name,
                                                            'hasIndividual' => ($service->individual_price ?? 0) > 0,
                                                            'price' => $finalPrice,
                                                            'individualPrice' => $service->individual_price ?? 0
                                                        ])
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif

            {{-- Other Categories --}}
            @if($otherCategories->count() > 0)
                <div class="mb-8">
                    <div class="py-6">
                        <div class="text-center mb-8">
                            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Інші категорії</h2>
                            <p class="text-gray-600">Оберіть іншу категорію послуг</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 md:gap-4">
                            @foreach($otherCategories as $otherCategory)
                                <a href="{{ route('category_page', $otherCategory->href) }}" 
                                   class="group bg-white/40 rounded-xl p-4 md:p-6 hover:border-primary hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex gap-4 md:flex-col md:items-center md:text-center">
                                        @if($otherCategory->category_img)
                                            <img src="{{ asset('storage/' . $otherCategory->category_img) }}" 
                                                 alt="{{ $otherCategory->name }}" 
                                                 class="w-16 h-16 md:w-20 md:h-20 rounded-full object-cover group-hover:scale-110 transition-transform duration-300">
                                        @else
                                            <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                                                <i class="fas fa-tag text-2xl md:text-3xl text-primary/50"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="text-base md:text-lg font-semibold text-gray-800 group-hover:text-primary transition-colors duration-300 mb-2">
                                                {{ $otherCategory->name }}
                                            </h3>
                                            <p class="text-sm text-gray-500">
                                                {{ $otherCategory->services->count() }} {{ $otherCategory->services->count() === 1 ? 'послуга' : 'послуг' }}
                                            </p>
                                            @if($otherCategory->hasActiveDiscount())
                                                <span class="mt-2 inline-block bg-primary/10 text-primary text-xs font-semibold px-2 py-1 rounded-full">
                                                    -{{ $otherCategory->getDiscountPercent() }}%
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Дополнительные скрипты при необходимости --}}
@endsection