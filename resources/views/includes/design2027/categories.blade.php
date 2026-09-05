@php
    $ctaHeaders = \App\Models\CtaHeader::with('iconRelation')
        ->where('is_active', true)
        ->orderBy('sort_order', 'asc')
        ->get();
    $tones = ['bg-[#EDE8FF]', 'bg-[#FFE4EE]', 'bg-[#E8F9C8]', 'bg-[#DDF4FF]'];
    $activeCategories = isset($categories)
        ? $categories->filter(fn($c) => $c->getAllServices()->isNotEmpty())
        : collect();
@endphp

<section class="py-14 sm:py-16 relative">
    <div class="site-container">
        @if($ctaHeaders->count() > 0)
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-12 sm:mb-14">
                @foreach($ctaHeaders as $index => $ctaHeader)
                    <a href="{{ $ctaHeader->resolved_url }}" target="_blank"
                       class="glass-strong rounded-[24px] px-3 py-5 sm:p-5 text-center transition-transform duration-400 hover:-translate-y-1 no-underline block">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full {{ $tones[$index % 4] }} flex items-center justify-center mx-auto mb-3 overflow-hidden">
                            @if($ctaHeader->iconRelation && $ctaHeader->iconRelation->file_path)
                                <img src="{{ asset('storage/' . $ctaHeader->iconRelation->file_path) }}" alt="{{ $ctaHeader->title }}" class="w-10 h-10 object-contain">
                            @elseif($ctaHeader->icon)
                                <img src="{{ asset('storage/' . $ctaHeader->icon) }}" alt="{{ $ctaHeader->title }}" class="w-10 h-10 object-contain">
                            @else
                                <i class="fas fa-tag text-[#FF2D6A] text-xl"></i>
                            @endif
                        </div>
                        <div class="font-bold text-[12px] sm:text-[14px] text-[#1A1A2E] leading-snug">{{ $ctaHeader->title }}</div>
                        @if($ctaHeader->subtitle && $ctaHeader->subtitle !== '-')
                            <div class="text-[11px] text-[#1A1A2E]/50 mt-1">{{ $ctaHeader->subtitle }}</div>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif

        @if($activeCategories->count() > 0)
            <div class="text-center mb-8">
                <div class="tag-badge mb-4 mx-auto w-fit">Категорії</div>
                <h2 class="text-section text-[#1A1A2E]">Що ми чистимо?</h2>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3">
                @foreach($activeCategories as $category)
                    <a href="{{ route('category_page', $category->href) }}"
                       class="glass-card group w-full text-center p-4 sm:p-5 h-full no-underline block">
                        <div class="w-14 h-14 rounded-2xl bg-white/55 flex items-center justify-center mx-auto mb-3 transition-transform duration-500 group-hover:scale-110 overflow-hidden">
                            @if($category->category_img)
                                <img src="{{ asset('storage/' . $category->category_img) }}" alt="{{ $category->name }}" class="w-10 h-10 object-contain rounded-full">
                            @else
                                <i class="fas fa-tshirt text-[#FF2D6A] text-2xl"></i>
                            @endif
                        </div>
                        <div class="font-bold text-[13px] xl:text-[14px] text-[#1A1A2E] mb-1 leading-snug">{{ $category->name }}</div>
                        <div class="text-[11px] font-bold text-[#FF2D6A]">{{ $category->getAllServices()->count() }} послуг</div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>
