import { useEffect, useState } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faCartShopping,
  faTags,
  faTruck,
  faLocationDot,
  faPhone,
  faXmark,
  faPercent,
  faHandshake,
} from "@fortawesome/free-solid-svg-icons";
import type { IconDefinition } from "@fortawesome/fontawesome-svg-core";
import RaccoonLogo from "./RaccoonLogo";
import { useAppNavigate } from "@/lib/navigation";
import { ROUTES } from "@/lib/routes";

export default function OrbitalSystem() {
  const { location, goHome, goServices, goB2b, goLocations, goCheckout, navigate } = useAppNavigate();
  const isHome = location.pathname === ROUTES.home;
  const [visible, setVisible] = useState(false);
  const [open, setOpen] = useState(false);
  const [reduced, setReduced] = useState(false);

  useEffect(() => {
    const update = () => {
      if (!isHome) {
        setVisible(true);
        return;
      }
      const hero = document.getElementById("hero");
      if (!hero) {
        setVisible(false);
        return;
      }
      const heroBottom = hero.getBoundingClientRect().bottom;
      setVisible(heroBottom < 120);
      if (heroBottom >= 120) setOpen(false);
    };
    update();
    window.addEventListener("scroll", update, { passive: true });
    window.addEventListener("resize", update, { passive: true });
    return () => {
      window.removeEventListener("scroll", update);
      window.removeEventListener("resize", update);
    };
  }, [isHome]);

  useEffect(() => {
    setOpen(false);
  }, [location.pathname]);

  useEffect(() => {
    if (!open) return;
    const onKey = (e: KeyboardEvent) => {
      if (e.key === "Escape") setOpen(false);
    };
    window.addEventListener("keydown", onKey);
    return () => window.removeEventListener("keydown", onKey);
  }, [open]);

  useEffect(() => {
    const mq = window.matchMedia("(max-width: 1023px)");
    const apply = () => setReduced(mq.matches);
    apply();
    mq.addEventListener("change", apply);
    return () => mq.removeEventListener("change", apply);
  }, []);

  const goHash = (hash: string) => {
    setOpen(false);
    if (!isHome) {
      goHome();
      setTimeout(() => document.getElementById(hash)?.scrollIntoView({ behavior: "smooth" }), 140);
    } else {
      document.getElementById(hash)?.scrollIntoView({ behavior: "smooth" });
    }
  };

  const run = (fn: () => void) => {
    setOpen(false);
    fn();
  };

  const quickActions: Array<{
    id: string;
    label: string;
    desc: string;
    accent?: boolean;
    fa: IconDefinition;
    action: () => void;
  }> = [
    {
      id: "order",
      label: "Кошик",
      desc: "Оформити замовлення",
      accent: true,
      fa: faCartShopping,
      action: () => run(() => goCheckout()),
    },
    {
      id: "prices",
      label: "Ціни",
      desc: "Прайс послуг",
      fa: faTags,
      action: () => run(() => (isHome ? goHash("prices") : goServices())),
    },
    {
      id: "delivery",
      label: "Доставка",
      desc: "Кур'єр по Києву",
      fa: faTruck,
      action: () => run(() => navigate(ROUTES.delivery)),
    },
    {
      id: "loc",
      label: "Локації",
      desc: "Пункти прийому",
      fa: faLocationDot,
      action: () => run(() => goLocations()),
    },
    {
      id: "promo",
      label: "Акції",
      desc: "Знижки тижня",
      fa: faPercent,
      action: () => run(() => navigate(ROUTES.promotions)),
    },
    {
      id: "b2b",
      label: "B2B",
      desc: "Для бізнесу",
      fa: faHandshake,
      action: () => run(() => goB2b()),
    },
  ];

  if (!visible) return null;

  const fabSize = 64;

  return (
    <>
      {open && (
        <button
          type="button"
          className="fixed inset-0 z-[45] bg-[#1A1A2E]/25 backdrop-blur-[2px]"
          aria-label="Закрити меню"
          onClick={() => setOpen(false)}
        />
      )}

      {open && (
        <div className="fixed z-[50] right-3 sm:right-5 bottom-[7.5rem] lg:bottom-24 w-[min(calc(100vw-1.5rem),320px)] pointer-events-auto">
          <div
            className="glass-strong rounded-[28px] p-3.5 shadow-[0_20px_56px_rgba(26,26,46,0.2)] border border-white/60 orbital-panel-in"
            role="dialog"
            aria-label="Швидкі дії ЄНОТ 24"
          >
            <div className="flex items-center justify-between gap-2 px-1 mb-3">
              <div className="flex items-center gap-2 min-w-0">
                <RaccoonLogo size={28} />
                <div className="min-w-0">
                  <div className="font-bold text-[13px] text-[#1A1A2E] leading-tight">Швидкі дії</div>
                  <div className="text-[11px] text-[#1A1A2E]/45">ЄНОТ 24</div>
                </div>
              </div>
              <button
                type="button"
                onClick={() => setOpen(false)}
                className="w-8 h-8 rounded-full bg-white/60 flex items-center justify-center text-[#1A1A2E]/55"
                aria-label="Закрити"
              >
                <FontAwesomeIcon icon={faXmark} />
              </button>
            </div>

            <div className="grid grid-cols-2 gap-2 mb-3">
              {quickActions.map((a) => (
                <button
                  key={a.id}
                  type="button"
                  onClick={a.action}
                  className={`text-left rounded-2xl px-3 py-3 transition-all active:scale-[0.98] ${
                    a.accent
                      ? "bg-[#f97171] text-white shadow-md shadow-[#f97171]/30 col-span-2 flex items-center gap-3"
                      : "bg-white/55 hover:bg-white/80 border border-white/50"
                  }`}
                >
                  <span
                    className={`w-9 h-9 rounded-xl flex items-center justify-center shrink-0 ${
                      a.accent ? "bg-white/20" : "bg-white/70 text-[#f97171]"
                    }`}
                  >
                    <FontAwesomeIcon icon={a.fa} className="text-[14px]" />
                  </span>
                  <span className="min-w-0">
                    <span className={`block text-[13px] font-bold ${a.accent ? "" : "text-[#1A1A2E]"}`}>
                      {a.label}
                    </span>
                    <span className={`block text-[11px] mt-0.5 ${a.accent ? "text-white/75" : "text-[#1A1A2E]/45"}`}>
                      {a.desc}
                    </span>
                  </span>
                </button>
              ))}
            </div>

            <a
              href="tel:+380678872233"
              onClick={() => setOpen(false)}
              className="flex items-center justify-center gap-2 w-full rounded-2xl bg-[#1A1A2E] text-white py-3 text-[13px] font-bold"
            >
              <FontAwesomeIcon icon={faPhone} className="text-[12px] text-[#f97171]" />
              067 887 22 33
            </a>
          </div>
        </div>
      )}

      <button
        type="button"
        onClick={() => setOpen((v) => !v)}
        className={`orbital-fab fixed z-[48] rounded-full glass-strong flex items-center justify-center border border-white/70 shadow-[0_12px_36px_rgba(249,113,113,0.3)] transition-transform hover:scale-105 active:scale-95 ${
          open ? "ring-2 ring-[#f97171]/35" : ""
        }`}
        style={{
          width: fabSize,
          height: fabSize,
          right: reduced ? 12 : 20,
          bottom: reduced ? 96 : 24,
        }}
        aria-label={open ? "Закрити" : "Швидкі дії"}
      >
        <div className="absolute inset-0 rounded-full bg-gradient-to-br from-[#f97171]/18 via-transparent to-[#7C6AFF]/10" />
        {open ? (
          <FontAwesomeIcon icon={faXmark} className="relative z-10 text-[#f97171] text-[18px]" />
        ) : (
          <>
            <RaccoonLogo size={32} className="relative z-10" />
            <span className="absolute top-0.5 right-0.5 w-3 h-3 rounded-full bg-[#f97171] border-2 border-white animate-pulse" />
          </>
        )}
      </button>
    </>
  );
}
