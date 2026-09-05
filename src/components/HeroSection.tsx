import { Link } from "react-router-dom";
import { ROUTES } from "@/lib/routes";
import { openFeedbackModal } from "@/context/FeedbackContext";
import HeroOrbit from "./hero/HeroOrbit";

export default function HeroSection() {
  return (
    <section
      id="hero"
      className="relative min-h-[100svh] flex flex-col overflow-hidden pt-20 pb-28 lg:pb-8"
    >
      <div className="pointer-events-none absolute inset-0 z-0" aria-hidden>
        <div className="absolute -top-24 left-1/2 -translate-x-1/2 w-[90vw] max-w-[900px] h-[90vw] max-h-[900px] rounded-full bg-[#f97171]/14 blur-[100px]" />
        <div className="absolute bottom-0 right-[-10%] w-[50vw] h-[50vw] rounded-full bg-[#7C6AFF]/10 blur-[110px]" />
      </div>

      <div className="site-container relative z-10 flex-1 flex flex-col">
        <div className="relative lg:flex lg:items-center flex-1 lg:min-h-[min(88svh,860px)]">
          {/* Текст — ліва колонка, обмежена ширина */}
          <div className="relative z-10 w-full max-w-[540px] xl:max-w-[580px] pt-2 lg:pt-10 lg:pb-8">
            <div className="inline-flex items-center gap-2 rounded-full bg-white/45 border border-white/55 backdrop-blur-md px-3 py-1.5 mb-5 sm:mb-8 w-fit anim-fade-up shadow-sm">
              <span className="w-1.5 h-1.5 rounded-full bg-[#f97171] animate-pulse" />
              <span className="text-[11px] sm:text-[12px] font-semibold tracking-[0.14em] uppercase flex items-start gap-1 text-[#1A1A2E]/55">
                <span>#найякісніша_хімчистка_столиці</span>
              </span>
            </div>

            <h1 className="font-display font-black tracking-[-0.05em] leading-[0.82] text-[#1A1A2E] anim-fade-up stagger-1">
              <span className="block text-[clamp(2.75rem,10vw,5rem)] lg:text-[clamp(3.25rem,4.8vw,6.5rem)] xl:text-[clamp(3.75rem,5.2vw,7.25rem)]">
                ХІМЧИСТКА
              </span>
              <span className="block text-[clamp(2.75rem,10vw,5rem)] lg:text-[clamp(3.25rem,4.8vw,6.5rem)] xl:text-[clamp(3.75rem,5.2vw,7.25rem)] text-[#f97171]">
                ОДЯГУ
              </span>
            </h1>

            <p className="mt-5 sm:mt-8 max-w-md text-[15px] sm:text-[18px] font-medium text-[#1A1A2E]/60 leading-relaxed anim-fade-up stagger-2">
              Гіпоаллергенно, якісно, та з увагою до деталей по догляду за вашим одягом
            </p>

            <div className="mt-7 sm:mt-10 flex flex-wrap items-center gap-3 anim-fade-up stagger-3">
              <button type="button" onClick={openFeedbackModal} className="btn-primary px-7 py-3.5 text-[14px] sm:text-[15px]">
                Замовити
              </button>
              <Link
                to={ROUTES.services}
                className="rounded-full px-6 py-3.5 text-[14px] sm:text-[15px] font-bold text-[#1A1A2E]/70 hover:text-[#1A1A2E] hover:bg-white/55 border border-[#1A1A2E]/40 backdrop-blur-sm transition-all no-underline"
              >
                Дивитись ціни
              </Link>
            </div>
          </div>

          {/* Орбіта — статично справа, не перекриває текст */}
          <div className="hero-orbit-anchor mt-12 sm:mt-14 lg:mt-0 pointer-events-none">
            <div className="pointer-events-auto">
              <HeroOrbit />
            </div>
          </div>
        </div>

        <div className="mt-auto pt-5 flex flex-wrap items-center justify-between gap-3 border-t border-[#1A1A2E]/08">
        <div className="mt-5">
              <ul className="flex items-center gap-2">
                <li className="inline-block">
                  <a className="text-md color-[#1A1A2E]/40" href="https://instagram.com/enot24cleaner" target="_blank" rel="referrer"><i className="fa-brands fa-instagram"></i></a>
                </li>
                <li className="inline-block">
                  <a className="text-md color-[#1A1A2E]/40" href="https://t.me/enot24ServiceBot" target="_blank" rel="referrer"><i className="fa-brands fa-telegram"></i></a>
                </li>
                <li className="inline-block">
                  <a className="text-md color-[#1A1A2E]/40" href="https://www.tiktok.com/@enot24_cleanner" target="_blank" rel="referrer"><i className="fa-brands fa-tiktok"></i></a>
                </li>
              </ul>
            </div>
          <p className="text-[12px] sm:text-[13px] font-semibold text-[#1A1A2E]/40 tracking-wide">
            Безкоштовна доставка по Києву · натисніть знак на орбіті — підкажемо значення ↓
          </p>
          <a href="#prices" className="group inline-flex items-center gap-2 text-[12px] sm:text-[13px] font-bold text-[#f97171] no-underline">
            Прайс
            <span className="inline-block transition-transform duration-400 group-hover:translate-y-0.5">↓</span>
          </a>
        </div>
      </div>
    </section>
  );
}
