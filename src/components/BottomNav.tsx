import { useEffect, useId, useState } from "react";
import { Link, useLocation } from "react-router-dom";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faHouse,
  faShirt,
  faCartShopping,
  faLocationDot,
  faUser,
} from "@fortawesome/free-solid-svg-icons";
import type { IconDefinition } from "@fortawesome/fontawesome-svg-core";
import { ROUTES } from "@/lib/routes";
import { pathIsServices } from "@/lib/navigation";
import { useCartOptional } from "@/context/CartContext";

type TabId = "home" | "services" | "order" | "locations" | "profile";

const tabs: Array<{ id: TabId; href: string; icon: IconDefinition; label: string }> = [
  { id: "home", href: ROUTES.home, icon: faHouse, label: "Головна" },
  { id: "services", href: ROUTES.services, icon: faShirt, label: "Послуги" },
  { id: "order", href: ROUTES.cart, icon: faCartShopping, label: "Кошик" },
  { id: "locations", href: ROUTES.locations, icon: faLocationDot, label: "Локації" },
  { id: "profile", href: "/home", icon: faUser, label: "Кабінет" },
];

function resolveActiveTab(pathname: string): TabId {
  if (pathname === ROUTES.home) return "home";
  if (pathIsServices(pathname) || pathname === ROUTES.b2b) return "services";
  if (pathname === ROUTES.courier || pathname === ROUTES.cart || pathname === ROUTES.checkout) return "order";
  if (pathname === ROUTES.locations) return "locations";
  if (pathname === "/home") return "profile";
  return "home";
}

/** SVG path бар з «краплею»-вирізом під активним табом (viewBox 0 0 360 72) */
function barPathWithNotch(activeIndex: number, count: number): string {
  const w = 360;
  const h = 72;
  const r = 28;
  const cx = ((activeIndex + 0.5) / count) * w;
  const notchR = 34;
  const notchDepth = 26;

  const left = Math.max(r + 8, cx - notchR);
  const right = Math.min(w - r - 8, cx + notchR);

  return [
    `M ${r} 0`,
    `L ${left} 0`,
    `C ${left + 10} 0 ${cx - 22} ${notchDepth} ${cx} ${notchDepth}`,
    `C ${cx + 22} ${notchDepth} ${right - 10} 0 ${right} 0`,
    `L ${w - r} 0`,
    `Q ${w} 0 ${w} ${r}`,
    `L ${w} ${h - r}`,
    `Q ${w} ${h} ${w - r} ${h}`,
    `L ${r} ${h}`,
    `Q 0 ${h} 0 ${h - r}`,
    `L 0 ${r}`,
    `Q 0 0 ${r} 0`,
    `Z`,
  ].join(" ");
}

export default function BottomNav() {
  const [floating, setFloating] = useState(false);
  const gradId = useId().replace(/:/g, "");
  const location = useLocation();
  const cart = useCartOptional();
  const cartCount = cart?.count ?? 0;

  const active = resolveActiveTab(location.pathname);
  const activeIndex = Math.max(
    0,
    tabs.findIndex((t) => t.id === active),
  );

  useEffect(() => {
    const update = () => {
      const threshold =
        location.pathname === ROUTES.home ? Math.min(window.innerHeight * 0.55, 420) : 48;
      setFloating(window.scrollY > threshold);
    };
    update();
    window.addEventListener("scroll", update, { passive: true });
    window.addEventListener("resize", update, { passive: true });
    return () => {
      window.removeEventListener("scroll", update);
      window.removeEventListener("resize", update);
    };
  }, [location.pathname]);

  return (
    <nav
      className={`bottom-nav-dock lg:hidden pointer-events-none ${
        floating ? "is-float" : "is-stock"
      }`}
      aria-label="Мобільна навігація"
    >
      {!floating ? (
        <div className="pointer-events-auto bottom-nav-shell is-stock">
          <div className="flex items-center justify-between gap-0.5">
            {tabs.map((tab) => {
              const isActive = active === tab.id;
              return (
                <Link
                  key={tab.id}
                  to={tab.href}
                  className={`flex-1 flex flex-col items-center gap-0.5 py-2 rounded-2xl no-underline transition-all duration-400 relative ${
                    isActive
                      ? "bg-[#f97171]/12 text-[#f97171]"
                      : "text-[#1A1A2E]/40 active:scale-95"
                  }`}
                  aria-current={isActive ? "page" : undefined}
                >
                  <FontAwesomeIcon
                    icon={tab.icon}
                    className={`text-[18px] ${isActive ? "scale-110" : ""}`}
                  />
                  {tab.id === "order" && cartCount > 0 && (
                    <span className="bottom-nav-cart-badge">{cartCount > 99 ? "99+" : cartCount}</span>
                  )}
                  <span className="text-[9px] font-bold tracking-wide">{tab.label}</span>
                </Link>
              );
            })}
          </div>
        </div>
      ) : (
        <div className="pointer-events-auto bottom-nav-float-wrap">
          <svg
            className="bottom-nav-float-svg"
            viewBox="0 0 360 72"
            preserveAspectRatio="none"
            aria-hidden
          >
            <defs>
              <linearGradient id={`bn-fill-${gradId}`} x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stopColor="rgba(255,255,255,0.94)" />
                <stop offset="100%" stopColor="rgba(255,255,255,0.82)" />
              </linearGradient>
            </defs>
            <path
              d={barPathWithNotch(activeIndex, tabs.length)}
              fill={`url(#bn-fill-${gradId})`}
              stroke="rgba(255,255,255,0.75)"
              strokeWidth="1"
            />
          </svg>

          <div className="bottom-nav-float-tabs">
            {tabs.map((tab, i) => {
              const isActive = active === tab.id;
              if (isActive) {
                return (
                  <Link
                    key={tab.id}
                    to={tab.href}
                    className="bottom-nav-float-tab bottom-nav-float-tab--active no-underline"
                    aria-current="page"
                    style={{ animationDelay: `${i * 40}ms` }}
                  >
                    <span className="bottom-nav-fab">
                      <FontAwesomeIcon icon={tab.icon} className="text-[20px]" />
                      {tab.id === "order" && cartCount > 0 && (
                        <span className="bottom-nav-cart-badge bottom-nav-cart-badge--fab">
                          {cartCount > 99 ? "99+" : cartCount}
                        </span>
                      )}
                    </span>
                    <span className="bottom-nav-float-tab__label">{tab.label}</span>
                  </Link>
                );
              }
              return (
                <Link
                  key={tab.id}
                  to={tab.href}
                  className="bottom-nav-float-tab no-underline relative"
                >
                  <FontAwesomeIcon icon={tab.icon} className="text-[18px]" />
                  {tab.id === "order" && cartCount > 0 && (
                    <span className="bottom-nav-cart-badge bottom-nav-cart-badge--float">
                      {cartCount > 99 ? "99+" : cartCount}
                    </span>
                  )}
                  <span className="bottom-nav-float-tab__label bottom-nav-float-tab__label--muted">
                    {tab.label}
                  </span>
                </Link>
              );
            })}
          </div>
        </div>
      )}
    </nav>
  );
}
