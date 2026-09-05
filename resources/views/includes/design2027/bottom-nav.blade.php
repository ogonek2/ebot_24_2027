@php
    $isHome = request()->routeIs('welcome');
    $isServices = request()->routeIs('services');
@endphp

<nav class="bottom-nav-dock is-stock lg:hidden" id="enot-bottom-nav" aria-label="Мобільна навігація">
    <div class="bottom-nav-shell is-stock" id="enot-bottom-nav-shell">
        <a href="{{ route('welcome') }}" class="bottom-nav-tab {{ $isHome ? 'is-active' : '' }}">
            <i class="fas fa-home text-lg"></i>
            <span>Головна</span>
        </a>
        <a href="{{ route('services') }}" class="bottom-nav-tab {{ $isServices ? 'is-active' : '' }}">
            <i class="fas fa-tags text-lg"></i>
            <span>Ціни</span>
        </a>
        <button type="button" class="bottom-nav-fab modal_fade" data-modal="feedbackmd" aria-label="Замовити">
            <i class="fas fa-plus text-lg"></i>
        </button>
        <a href="{{ route('promotions') }}" class="bottom-nav-tab">
            <i class="fas fa-percent text-lg"></i>
            <span>Акції</span>
        </a>
        <a href="{{ route('contacts_page') }}" class="bottom-nav-tab">
            <i class="fas fa-phone text-lg"></i>
            <span>Контакти</span>
        </a>
    </div>
</nav>
