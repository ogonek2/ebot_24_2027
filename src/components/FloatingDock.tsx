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
import QuickActionsPanel from "./QuickActionsPanel";
import { useAppNavigate } from "@/lib/navigation";
import { ROUTES } from "@/lib/routes";
import { openFeedbackModal, useFeedback } from "@/context/FeedbackContext";

export default function FloatingDock() {
  const { location, goHome, goServices, goB2b, goLocations, goCheckout, navigate } = useAppNavigate();
  const { isOpen: feedbackOpen } = useFeedback();
  const isHome = location.pathname === ROUTES.home;
  const [menuOpen, setMenuOpen] = useState(false);
  const [entered, setEntered] = useState(false);

  useEffect(() => {
    const t = requestAnimationFrame(() => setEntered(true));
    return () => cancelAnimationFrame(t);
  }, []);

  useEffect(() => {
    setMenuOpen(false);
  }, [location.pathname]);

  useEffect(() => {
    if (!menuOpen) return;
    const onKey = (e: KeyboardEvent) => {
      if (e.key === "Escape") setMenuOpen(false);
    };
    window.addEventListener("keydown", onKey);
    return () => window.removeEventListener("keydown", onKey);
  }, [menuOpen]);

  const goHash = (hash: string) => {
    setMenuOpen(false);
    if (!isHome) {
      goHome();
      setTimeout(() => document.getElementById(hash)?.scrollIntoView({ behavior: "smooth" }), 140);
    } else {
      document.getElementById(hash)?.scrollIntoView({ behavior: "smooth" });
    }
  };

  const run = (fn: () => void) => {
    setMenuOpen(false);
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

  if (feedbackOpen) return null;

  return (
    <>
      {menuOpen && (
        <button
          type="button"
          className="fixed inset-0 z-[45] bg-[#1A1A2E]/20 backdrop-blur-[2px]"
          aria-label="Закрити меню"
          onClick={() => setMenuOpen(false)}
        />
      )}

      <div
        className={`fab-dock fixed z-[48] pointer-events-none ${entered ? "fab-dock--in" : ""}`}
        aria-label="Швидкий доступ"
      >
        {menuOpen && (
          <div className="fab-dock__panel pointer-events-auto">
            <QuickActionsPanel onClose={() => setMenuOpen(false)} quickActions={quickActions} />
          </div>
        )}

        <div className="fab-dock__stack pointer-events-auto">
          <button
            type="button"
            className="fab-dock__phone"
            aria-label="Замовити консультацію"
            onClick={openFeedbackModal}
          >
            <span className="fab-dock__phone-ring" aria-hidden />
            <FontAwesomeIcon icon={faPhone} className="fab-dock__phone-icon" />
            <span className="fab-dock__badge" aria-hidden>
              <span className="fab-dock__badge-ping" />
              <span className="fab-dock__badge-dot" />
            </span>
            <span className="fab-dock__hint">Консультація</span>
          </button>

          <span className="fab-dock__divider" aria-hidden />

          <button
            type="button"
            onClick={() => setMenuOpen((v) => !v)}
            className={`fab-dock__menu ${menuOpen ? "fab-dock__menu--open" : ""}`}
            aria-label={menuOpen ? "Закрити меню" : "Швидкі дії"}
            aria-expanded={menuOpen}
          >
            {menuOpen ? (
              <FontAwesomeIcon icon={faXmark} className="text-[#f97171] text-[18px]" />
            ) : (
              <>
                <RaccoonLogo size={60} />
                <span className="fab-dock__menu-dot" aria-hidden />
              </>
            )}
          </button>
        </div>
      </div>
    </>
  );
}
