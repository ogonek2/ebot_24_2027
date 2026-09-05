@if(isset($discounts) && $discounts->count() > 0)
<section class="py-16 sm:py-20" id="promo">
    <div class="site-container">
        <div class="glass rounded-[28px] p-5 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
                <div>
                    <div class="tag-badge mb-3 w-fit">Акції</div>
                    <h2 class="text-section text-[#1A1A2E]">Акції та спеціальні пропозиції</h2>
                    <p class="text-[15px] text-[#1A1A2E]/55 mt-2">Актуальні знижки від ЄНОТ 24</p>
                </div>
                <a href="{{ route('promotions') }}" class="btn-primary px-6 py-3 text-[13px] self-start no-underline">Усі акції</a>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($discounts->take(4) as $discount)
                    <a href="{{ route('promotion_page', $discount->id) }}"
                       class="glass-card p-5 text-left bg-gradient-to-br from-[#FF2D6A]/12 to-transparent no-underline block">
                        @if($discount->discount_action)
                            <div class="text-[28px] font-black text-[#FF2D6A] mb-1">{{ $discount->discount_action }}</div>
                        @endif
                        <div class="font-bold text-[15px] text-[#1A1A2E] mb-1">{{ $discount->name ?? 'Акція' }}</div>
                        @if($discount->locations)
                            <div class="text-[12px] text-[#1A1A2E]/45 mt-1">{{ $discount->locations }}</div>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
