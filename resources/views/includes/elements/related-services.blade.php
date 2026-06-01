@if(isset($relatedServices) && $relatedServices->count() > 0)
    <div class="{{ $wrapperClass ?? 'mt-10 px-4 md:px-0' }}">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Релевантні послуги</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($relatedServices as $relService)
                @php
                    $relCategory = $relService->getPrimaryCategory();
                    $relSlug = $relService->transform_url ?? $relService->href;
                @endphp
                @if($relCategory && $relSlug)
                    <a href="{{ route('service_page', [$relCategory->href, $relSlug]) }}"
                       class="bg-white rounded-xl shadow p-5 hover:shadow-md transition-shadow border border-gray-100 group">
                        <h3 class="font-semibold text-gray-900 group-hover:text-primary transition-colors line-clamp-2 mb-2">
                            {{ $relService->name }}
                        </h3>
                        @if($relService->price && (float) $relService->price > 0)
                            <p class="text-primary font-bold text-sm">
                                від {{ number_format((float) $relService->price, 0, ',', ' ') }} ₴
                            </p>
                        @endif
                        <span class="text-sm text-primary mt-3 inline-flex items-center gap-1">
                            Замовити <i class="fas fa-arrow-right text-xs"></i>
                        </span>
                    </a>
                @endif
            @endforeach
        </div>
        <div class="mt-6 text-center sm:text-left">
            <a href="{{ route('services') }}" class="inline-flex items-center gap-2 text-primary font-semibold hover:underline">
                Усі послуги та ціни <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
@endif
