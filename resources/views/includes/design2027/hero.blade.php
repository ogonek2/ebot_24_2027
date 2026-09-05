<section id="hero" class="relative min-h-[100svh] flex flex-col overflow-hidden pt-20 pb-28 lg:pb-8">
    <div class="pointer-events-none absolute inset-0 z-0" aria-hidden="true">
        <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-[90vw] max-w-[900px] h-[90vw] max-h-[900px] rounded-full bg-[#FF2D6A]/14 blur-[100px]"></div>
        <div class="absolute bottom-0 right-[-10%] w-[50vw] h-[50vw] rounded-full bg-[#7C6AFF]/10 blur-[110px]"></div>
    </div>

    <div class="site-container relative z-10 flex-1 flex flex-col">
        <div class="max-w-xl xl:max-w-2xl pt-2 lg:py-10 lg:flex-1 lg:flex lg:flex-col lg:justify-center">
            <div class="inline-flex items-center gap-2 rounded-full bg-white/45 border border-white/55 backdrop-blur-md px-3 py-1.5 mb-5 sm:mb-8 w-fit anim-fade-up shadow-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-[#FF2D6A] animate-pulse"></span>
                <span class="text-[11px] sm:text-[12px] font-semibold tracking-[0.14em] uppercase text-[#1A1A2E]/55">Київ · 24/7 прийом</span>
            </div>

            <h1 class="font-display font-black tracking-[-0.05em] leading-[0.82] text-[#1A1A2E] anim-fade-up stagger-1">
                <span class="block text-[clamp(3.6rem,12vw,11rem)]">ЄНОТ</span>
                <span class="block text-[clamp(3.6rem,12vw,11rem)] text-[#FF2D6A]">24</span>
            </h1>

            <p class="mt-5 sm:mt-8 max-w-md text-[15px] sm:text-[18px] font-medium text-[#1A1A2E]/60 leading-relaxed anim-fade-up stagger-2">
                Хімчистка, яка відчувається легко — гіпоалергенно й без зайвого шуму.
            </p>

            <div class="mt-7 sm:mt-10 flex flex-wrap items-center gap-3 anim-fade-up stagger-3">
                <button type="button" class="btn-primary px-7 py-3.5 text-[14px] sm:text-[15px] modal_fade" data-modal="feedbackmd">
                    Замовити
                </button>
                <a href="{{ route('services') }}" class="rounded-full px-6 py-3.5 text-[14px] sm:text-[15px] font-bold text-[#1A1A2E]/70 hover:text-[#1A1A2E] hover:bg-white/55 border border-white/40 backdrop-blur-sm transition-all no-underline">
                    Дивитись ціни
                </a>
            </div>
        </div>

        <div class="mt-auto pt-5 flex flex-wrap items-center justify-between gap-3 border-t border-[#1A1A2E]/08">
            <p class="text-[12px] sm:text-[13px] font-semibold text-[#1A1A2E]/40 tracking-wide">
                Безкоштовна доставка по Києву
            </p>
            <a href="#prices" class="group inline-flex items-center gap-2 text-[12px] sm:text-[13px] font-bold text-[#FF2D6A] no-underline">
                Прайс
                <span class="inline-block transition-transform duration-300 group-hover:translate-y-0.5">↓</span>
            </a>
        </div>
    </div>
</section>
