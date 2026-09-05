@php
    $isHome = request()->routeIs('welcome');
    $isServices = request()->routeIs('services');
@endphp

<header class="enot-header" id="enot-header">
    <div class="site-container">
        <div class="enot-header__inner" id="enot-header-inner">
            <a href="{{ route('welcome') }}" class="flex items-center gap-2 shrink-0 no-underline">
                <img src="{{ asset('storage/src/logo/nobg_enot24.svg') }}" alt="ЄНОТ 24" width="30" height="30" class="w-[30px] h-[30px]">
                <span class="font-display font-black text-[15px] sm:text-[16px] tracking-tight text-[#1A1A2E]">ЄНОТ 24</span>
            </a>

            <nav class="enot-header__nav hidden lg:flex items-center gap-0.5 flex-1 justify-center min-w-0">
                <a href="{{ route('services') }}" class="{{ $isServices ? 'is-active' : '' }}">Послуги</a>
                <a href="{{ route('b2b_page') }}">B2B</a>
                <a href="{{ $isHome ? '#promo' : route('promotions') }}">Акції</a>
                <a href="{{ route('delivery_page') }}">Доставка</a>
                <a href="{{ route('contacts_page') }}">Контакти</a>
            </nav>

            <div class="flex items-center gap-1.5 sm:gap-2 ml-auto shrink-0">
                <a href="tel:+380678872233" class="hidden sm:inline-flex items-center gap-2 rounded-full px-3 py-2 text-[12px] font-bold text-[#1A1A2E]/70 hover:bg-white/50 no-underline transition-colors">
                    <i class="fas fa-phone text-[#FF2D6A] text-[11px]"></i>
                    067 887 22 33
                </a>
                <button type="button" class="hidden sm:inline-flex btn-primary px-4 py-2 text-[12px] modal_fade" data-modal="feedbackmd">
                    Замовити
                </button>
                <button type="button" id="enot-menu-toggle" class="lg:hidden w-9 h-9 rounded-full bg-white/50 flex items-center justify-center text-[#1A1A2E]" aria-label="Меню">
                    <i class="fas fa-bars text-sm"></i>
                </button>
            </div>
        </div>
    </div>
</header>

<div class="enot-mobile-nav" id="enot-mobile-nav" aria-hidden="true">
    <div class="enot-mobile-nav__panel">
        <a href="{{ route('services') }}">Послуги та ціни</a>
        <a href="{{ route('b2b_page') }}">B2B</a>
        <a href="{{ route('promotions') }}">Акції</a>
        <a href="{{ route('delivery_page') }}">Доставка</a>
        <a href="{{ route('locations_page') }}">Локації</a>
        <a href="{{ route('blog.index') }}">Блог</a>
        <a href="{{ route('contacts_page') }}">Контакти</a>
        <a href="tel:+380678872233" class="text-[#FF2D6A]">067 887 22 33</a>
        <button type="button" class="btn-primary w-full mt-6 py-3 modal_fade" data-modal="feedbackmd">Замовити</button>
    </div>
</div>
