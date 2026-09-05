@php
    /** @var \App\Models\Service $service */
    /** @var \App\Models\Category $category */
    $servicePrimaryCategory = $service->getPrimaryCategory() ?? $category;
    $originalPrice = floatval($service->price ?? 0);
    $individualPrice = floatval($service->individual_price ?? 0);
    $hasPrice = $originalPrice > 0;
    $discountedPrice = $originalPrice;
    $hasDiscount = false;
    $discountCategory = $category;

    if ($hasPrice) {
        foreach ($service->categories as $serviceCategory) {
            if ($serviceCategory->hasActiveDiscount()) {
                $discountedPrice = floatval($serviceCategory->calculateDiscountedPrice($originalPrice));
                $hasDiscount = true;
                $discountCategory = $serviceCategory;
                break;
            }
        }
        if (!$hasDiscount && $category->hasActiveDiscount()) {
            $discountedPrice = floatval($category->calculateDiscountedPrice($originalPrice));
            $hasDiscount = true;
        }
    }
    $finalPrice = $hasDiscount ? $discountedPrice : $originalPrice;
@endphp

<div class="enot-service-row price-card p-4 sm:p-5 mb-2" data-name="{{ mb_strtolower($service->name) }}">
    <div class="flex flex-col lg:flex-row lg:items-center gap-4 lg:gap-6">
        <div class="flex-1 min-w-0">
            <a href="{{ route('service_page', [$servicePrimaryCategory->href, $service->transform_url ?? $service->href]) }}"
               class="font-bold text-[14px] sm:text-[15px] text-[#1A1A2E] hover:text-[#FF2D6A] no-underline transition-colors">
                {{ $service->name }}
            </a>
            @if($service->marker)
                <span class="inline-block mt-1 px-2 py-0.5 rounded-full bg-[#FF2D6A] text-white text-[11px] font-bold">{{ $service->marker }}</span>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-4 lg:gap-8">
            {{-- Individual --}}
            <div class="text-center min-w-[80px]">
                <div class="text-[10px] font-bold uppercase tracking-wider text-[#1A1A2E]/40 mb-1">Індивід.</div>
                @if($individualPrice > 0)
                    <div class="text-[15px] font-black text-[#1A1A2E]">{{ number_format($individualPrice, 0) }}₴</div>
                @elseif($hasPrice)
                    <div class="text-[15px] font-black text-[#1A1A2E]">{{ number_format($finalPrice, 0) }}₴</div>
                @else
                    <div class="text-[12px] text-[#1A1A2E]/40 italic">—</div>
                @endif
            </div>

            {{-- Batch --}}
            <div class="text-center min-w-[80px]">
                <div class="text-[10px] font-bold uppercase tracking-wider text-[#1A1A2E]/40 mb-1">Потокова</div>
                @if($hasPrice)
                    @if($hasDiscount)
                        <div class="text-[15px] font-black text-[#FF2D6A]">{{ number_format($discountedPrice, 0) }}₴</div>
                        <div class="text-[11px] text-[#1A1A2E]/35 line-through">{{ number_format($originalPrice, 0) }}₴</div>
                    @else
                        <div class="text-[15px] font-black text-[#1A1A2E]">{{ number_format($originalPrice, 0) }}₴</div>
                    @endif
                @else
                    <div class="text-[12px] text-[#1A1A2E]/40 italic">Запит</div>
                @endif
            </div>

            {{-- Cart --}}
            @if($hasPrice)
                <div class="min-w-[120px]">
                    @include('components.add-to-cart-button', [
                        'serviceId' => $service->id,
                        'serviceName' => $service->name,
                        'hasIndividual' => $individualPrice > 0,
                        'price' => $finalPrice,
                        'individualPrice' => $individualPrice,
                    ])
                </div>
            @endif
        </div>
    </div>
</div>
