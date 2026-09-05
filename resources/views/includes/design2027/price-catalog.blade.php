@php
    $variant = $variant ?? 'section';
    $activeCategories = isset($categories)
        ? $categories->filter(fn($c) => $c->getAllServices()->isNotEmpty())
        : collect();
    $firstCategory = $activeCategories->first();
@endphp

@if($activeCategories->count() > 0)
<section id="prices" class="{{ $variant === 'section' ? 'py-14 sm:py-16' : 'py-8 sm:py-10' }} relative">
    <div class="site-container">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-3 {{ $variant === 'section' ? 'mb-5' : 'mb-6' }}">
            <div>
                @if($variant === 'section')
                    <div class="tag-badge mb-3 w-fit">Ціни</div>
                @endif
                <h2 class="text-section text-[#1A1A2E]">Послуги та ціни</h2>
                <p class="text-[14px] xl:text-[15px] text-[#1A1A2E]/55 mt-2 max-w-2xl">
                    Прозорі ціни: індивідуальна та потокова чистка. Акції позначені окремо.
                </p>
            </div>
            @if($variant === 'section')
                <a href="{{ route('services') }}" class="btn-outline px-4 py-2.5 text-[13px] self-start no-underline">
                    Повний прайс →
                </a>
            @endif
        </div>

        <div id="enot-catalog-sentinel" class="h-px w-full" aria-hidden="true"></div>

        <div class="enot-catalog-sticky mb-4" id="enot-catalog-sticky">
            <div class="glass-strong rounded-[24px] p-3 sm:p-4 w-full" id="enot-catalog-bar">
                <div class="relative mb-3">
                    <i class="fas fa-search pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[#1A1A2E]/35 text-sm"></i>
                    <input type="search" id="enot-catalog-search" placeholder="Пошук послуг… наприклад «килим» або «пуховик»"
                           class="glass-input glass-input--search text-[15px]" autocomplete="off">
                </div>

                <div class="relative">
                    <button type="button" id="enot-cats-prev" class="hidden sm:flex absolute left-0 top-1/2 -translate-y-1/2 z-10 w-8 h-8 rounded-full bg-white/80 items-center justify-center shadow-sm border border-white/60" aria-label="Назад">
                        <i class="fas fa-chevron-left text-xs text-[#1A1A2E]/60"></i>
                    </button>
                    <div id="enot-catalog-cats" class="flex gap-2 overflow-x-auto scrollbar-none px-0 sm:px-9 py-1">
                        @foreach($activeCategories as $index => $category)
                            <button type="button"
                                    class="enot-catalog-cat-btn {{ $index === 0 ? 'is-active' : '' }}"
                                    data-category="{{ $category->href }}">
                                @if($category->category_img)
                                    <img src="{{ asset('storage/' . $category->category_img) }}" alt="" class="w-5 h-5 rounded-full object-cover">
                                @endif
                                <span>{{ $category->name }}</span>
                                <span class="text-[10px] opacity-60">({{ $category->getAllServices()->count() }})</span>
                            </button>
                        @endforeach
                    </div>
                    <button type="button" id="enot-cats-next" class="hidden sm:flex absolute right-0 top-1/2 -translate-y-1/2 z-10 w-8 h-8 rounded-full bg-white/80 items-center justify-center shadow-sm border border-white/60" aria-label="Вперед">
                        <i class="fas fa-chevron-right text-xs text-[#1A1A2E]/60"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="enot-catalog-panels">
            @foreach($activeCategories as $index => $category)
                @php $services = $category->getAllServices()->sortBy('name'); @endphp
                <div class="enot-catalog-panel {{ $index === 0 ? 'is-active' : '' }}" data-category="{{ $category->href }}">
                    <div class="hidden lg:grid grid-cols-[1fr_80px_80px_120px] gap-4 px-4 py-2 mb-2 text-[10px] font-bold uppercase tracking-wider text-[#1A1A2E]/40">
                        <span>Послуга</span>
                        <span class="text-center">Індивід.</span>
                        <span class="text-center">Потокова</span>
                        <span class="text-center">Кошик</span>
                    </div>
                    @foreach($services as $service)
                        @include('includes.design2027.service-row', ['service' => $service, 'category' => $category])
                    @endforeach
                </div>
            @endforeach
        </div>

        <div id="enot-catalog-empty" class="hidden text-center py-12 text-[#1A1A2E]/50">
            <i class="fas fa-search text-3xl mb-3 opacity-30"></i>
            <p>Нічого не знайдено. Спробуйте інший запит.</p>
        </div>
    </div>
</section>
@endif
