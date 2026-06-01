@if(isset($relatedServices) && $relatedServices->count() > 0)
    <div class="rounded-2xl bg-white ring-1 ring-gray-200/80 p-5">
        <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-concierge-bell text-primary text-sm"></i>
            Рекомендовані послуги
        </h2>
        <ul class="space-y-3">
            @foreach($relatedServices as $relService)
                @php
                    $relCategory = $relService->getPrimaryCategory();
                    $relSlug = $relService->transform_url ?? $relService->href;
                @endphp
                @if($relCategory && $relSlug)
                    <li>
                        <a href="{{ route('service_page', [$relCategory->href, $relSlug]) }}"
                           class="group block rounded-xl border border-gray-100 p-3 hover:border-primary/30 hover:bg-primary/5 transition-colors">
                            <span class="font-semibold text-sm text-gray-900 group-hover:text-primary line-clamp-2 leading-snug">
                                {{ $relService->name }}
                            </span>
                            @if($relService->price && (float) $relService->price > 0)
                                <span class="text-primary text-sm font-bold mt-1 block">
                                    від {{ number_format((float) $relService->price, 0, ',', ' ') }} ₴
                                </span>
                            @endif
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
        <a href="{{ route('services') }}"
           class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-primary hover:text-enot-pink transition-colors">
            Усі послуги <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>
@endif
