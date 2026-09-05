import { useEffect, useRef, useState } from "react";
import { Link, useLocation } from "react-router-dom";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faChevronDown, faPhone } from "@fortawesome/free-solid-svg-icons";
import RaccoonLogo from "./RaccoonLogo";
import ServicesMegaMenu from "./ServicesMegaMenu";
import CartButton from "./cart/CartButton";
import { ROUTES } from "@/lib/routes";
import { headerLinks } from "@/lib/siteNav";
import { pathIsServices } from "@/lib/navigation";
import { openFeedbackModal } from "@/context/FeedbackContext";

interface HeaderProps {
  onMenuToggle: () => void;
  isMobileNavOpen: boolean;
}

const MEGA_CLOSE_DELAY_MS = 160;

export default function Header({ onMenuToggle, isMobileNavOpen }: HeaderProps) {
  const [scrolled, setScrolled] = useState(false);
  const [megaOpen, setMegaOpen] = useState(false);
  const megaRef = useRef<HTMLDivElement>(null);
  const closeTimerRef = useRef<ReturnType<typeof setTimeout> | null>(null);
  const location = useLocation();

  const openMega = () => {
    if (closeTimerRef.current) {
      clearTimeout(closeTimerRef.current);
      closeTimerRef.current = null;
    }
    setMegaOpen(true);
  };

  const scheduleCloseMega = () => {
    if (closeTimerRef.current) clearTimeout(closeTimerRef.current);
    closeTimerRef.current = setTimeout(() => setMegaOpen(false), MEGA_CLOSE_DELAY_MS);
  };

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 24);
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  useEffect(() => {
    setMegaOpen(false);
  }, [location.pathname]);

  useEffect(() => {
    return () => {
      if (closeTimerRef.current) clearTimeout(closeTimerRef.current);
    };
  }, []);

  useEffect(() => {
    if (!megaOpen) return;
    const onKey = (e: KeyboardEvent) => {
      if (e.key === "Escape") setMegaOpen(false);
    };
    const onClick = (e: MouseEvent) => {
      if (megaRef.current && !megaRef.current.contains(e.target as Node)) {
        setMegaOpen(false);
      }
    };
    window.addEventListener("keydown", onKey);
    document.addEventListener("mousedown", onClick);
    return () => {
      window.removeEventListener("keydown", onKey);
      document.removeEventListener("mousedown", onClick);
    };
  }, [megaOpen]);

  const servicesActive = pathIsServices(location.pathname);

  const linkClass = (active: boolean) =>
    `px-3 py-1.5 rounded-full text-[13px] font-semibold transition-all duration-300 no-underline ${
      active ? "bg-[#1A1A2E] text-white" : "text-[#1A1A2E]/60 hover:text-[#1A1A2E] hover:bg-white/50"
    }`;

  return (
    <header className="fixed top-0 left-0 right-0 z-50 pointer-events-none pt-3 sm:pt-4">
      <div className="site-container pointer-events-auto">
        <div className="header-shell relative" ref={megaRef}>
          <div
            className={`flex items-center gap-3 rounded-full px-2 sm:px-2.5 pl-3 sm:pl-4 h-12 sm:h-14 transition-all duration-500 ${
              scrolled || megaOpen
                ? "glass-strong shadow-[0_12px_40px_rgba(26,26,46,0.12)]"
                : "bg-white/30 border border-white/45 backdrop-blur-[40px] backdrop-saturate-[185%]"
            }`}
          >
            <Link
              to={ROUTES.home}
              className="flex items-center gap-2 shrink-0 active:scale-95 transition-transform no-underline"
              aria-label="ЄНОТ 24 — на головну"
              onMouseEnter={() => setMegaOpen(false)}
            >
              <RaccoonLogo size={60} />
              <span className="font-display font-black text-[15px] sm:text-[16px] tracking-tight text-[#1A1A2E]">
                ЄНОТ 24
              </span>
            </Link>

            <nav className="hidden lg:flex items-center gap-0.5 flex-1 justify-center min-w-0">
              <div
                className="relative"
                onMouseEnter={openMega}
                onMouseLeave={scheduleCloseMega}
              >
                <button
                  type="button"
                  onClick={() => setMegaOpen((v) => !v)}
                  aria-expanded={megaOpen}
                  aria-haspopup="true"
                  className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[13px] font-semibold transition-all duration-300 ${
                    servicesActive || megaOpen
                      ? "bg-[#1A1A2E] text-white"
                      : "text-[#1A1A2E]/60 hover:text-[#1A1A2E] hover:bg-white/50"
                  }`}
                >
                  Послуги
                  <FontAwesomeIcon
                    icon={faChevronDown}
                    className={`text-[10px] transition-transform duration-300 ${megaOpen ? "rotate-180" : ""}`}
                  />
                </button>
              </div>

              {headerLinks.map((link) => (
                <Link
                  key={link.label}
                  to={link.href}
                  className={linkClass(location.pathname === link.href)}
                  onMouseEnter={() => setMegaOpen(false)}
                >
                  {link.label}
                </Link>
              ))}
            </nav>

            <div
              className="flex items-center gap-1.5 sm:gap-2 ml-auto lg:ml-0 shrink-0"
              onMouseEnter={() => setMegaOpen(false)}
            >
              <a
                href="tel:+380678872233"
                className="hidden sm:inline-flex items-center gap-2 rounded-full px-3 py-2 text-[12px] font-bold text-[#1A1A2E]/70 hover:bg-white/50 transition-colors no-underline"
              >
                <FontAwesomeIcon icon={faPhone} className="text-[#f97171] text-[11px]" />
                067 887 22 33
              </a>

              <CartButton className="hidden sm:flex" />

              <button
                type="button"
                onClick={openFeedbackModal}
                className="hidden sm:inline-flex btn-primary !rounded-full px-4 py-2 text-[12px]"
              >
                Замовити
              </button>

              <button
                type="button"
                onClick={onMenuToggle}
                className={`${isMobileNavOpen ? "mobile-menu-toggle--open" : ""} mobile-menu-toggle lg:hidden`}
                aria-label={isMobileNavOpen ? "Закрити меню" : "Відкрити меню"}
                aria-expanded={isMobileNavOpen}
              >
                <span className="mobile-menu-toggle__bars" aria-hidden>
                  <span />
                  <span />
                  <span />
                </span>
              </button>
            </div>
          </div>

          {megaOpen && (
            <div
              className="mega-menu-panel hidden lg:block"
              onMouseEnter={openMega}
              onMouseLeave={scheduleCloseMega}
            >
              <ServicesMegaMenu onNavigate={() => setMegaOpen(false)} />
            </div>
          )}
        </div>
      </div>
    </header>
  );
}
