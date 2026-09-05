<footer class="pb-6 px-3 sm:px-4 mt-8" id="contacts">
    <div class="site-container glass-dark rounded-[32px] px-6 sm:px-10 py-12 sm:py-14">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
            <div class="col-span-2 lg:col-span-1">
                <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 mb-4 no-underline">
                    <img src="{{ asset('storage/src/logo/enot24.svg') }}" alt="ЄНОТ 24" class="h-8 brightness-0 invert">
                </a>
                <p class="text-white/50 text-[13px] leading-relaxed mb-5">
                    Найякісніша хімчистка Києва. Гіпоалергенно, якісно та з увагою до деталей.
                </p>
                <div class="flex gap-2">
                    <a href="https://instagram.com/enot24cleaner" target="_blank" rel="noopener" class="w-9 h-9 rounded-full bg-white/10 border border-white/10 flex items-center justify-center hover:bg-[#FF2D6A]/40 transition-all" aria-label="Instagram">
                        <i class="fab fa-instagram text-sm"></i>
                    </a>
                    <a href="https://t.me/enot24ServiceBot" target="_blank" rel="noopener" class="w-9 h-9 rounded-full bg-white/10 border border-white/10 flex items-center justify-center hover:bg-[#FF2D6A]/40 transition-all" aria-label="Telegram">
                        <i class="fab fa-telegram text-sm"></i>
                    </a>
                    <a href="viber://pa?chatURI=enot24" class="w-9 h-9 rounded-full bg-white/10 border border-white/10 flex items-center justify-center hover:bg-[#FF2D6A]/40 transition-all" aria-label="Viber">
                        <i class="fab fa-viber text-sm"></i>
                    </a>
                </div>
            </div>

            <div>
                <h4 class="font-bold text-[13px] text-white mb-4 uppercase tracking-wider">Послуги</h4>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('services') }}" class="text-white/45 hover:text-white text-[13px] no-underline transition-colors">Послуги та ціни</a></li>
                    <li><a href="{{ route('courier_page') }}" class="text-white/45 hover:text-white text-[13px] no-underline transition-colors">Викликати кур'єра</a></li>
                    <li><a href="{{ route('delivery_page') }}" class="text-white/45 hover:text-white text-[13px] no-underline transition-colors">Доставка</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-[13px] text-white mb-4 uppercase tracking-wider">Компанія</h4>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('b2b_page') }}" class="text-white/45 hover:text-white text-[13px] no-underline transition-colors">B2B рішення</a></li>
                    <li><a href="{{ route('locations_page') }}" class="text-white/45 hover:text-white text-[13px] no-underline transition-colors">Локації</a></li>
                    <li><a href="{{ route('blog.index') }}" class="text-white/45 hover:text-white text-[13px] no-underline transition-colors">Блог</a></li>
                    <li><a href="{{ route('promotions') }}" class="text-white/45 hover:text-white text-[13px] no-underline transition-colors">Акції</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-[13px] text-white mb-4 uppercase tracking-wider">Контакти</h4>
                <ul class="space-y-3 text-[13px] text-white/50">
                    <li class="flex items-center gap-2">
                        <i class="fas fa-phone text-[#FF2D6A]"></i>
                        <a href="tel:+380678872233" class="hover:text-white no-underline transition-colors">067 887 22 33</a>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-envelope text-[#FF2D6A]"></i>
                        <a href="mailto:info@enot-24.com.ua" class="hover:text-white no-underline transition-colors">info@enot-24.com.ua</a>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-map-marker-alt text-[#FF2D6A] mt-0.5"></i>
                        <span>Київ, Україна</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4 text-[12px] text-white/35">
            <p>© {{ date('Y') }} ЄНОТ 24. Всі права захищені.</p>
            <div class="flex gap-4">
                <a href="{{ url('/privacy-policy') }}" class="hover:text-white/60 no-underline text-white/35 transition-colors">Політика конфіденційності</a>
                <a href="{{ url('/oferta') }}" class="hover:text-white/60 no-underline text-white/35 transition-colors">Оферта</a>
            </div>
        </div>
    </div>
</footer>
