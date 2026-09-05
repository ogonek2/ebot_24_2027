import { useEffect, useState } from "react";
import { Link, useLocation } from "react-router-dom";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faAddressBook,
  faCartShopping,
  faChevronDown,
  faHandshake,
  faLocationDot,
  faNewspaper,
  faPercent,
  faPhone,
  faShirt,
  faTruck,
  faUserTie,
  faXmark,
} from "@fortawesome/free-solid-svg-icons";
import type { IconDefinition } from "@fortawesome/fontawesome-svg-core";
import { fetchAllServicesCached } from "@/lib/api";
import { useCachedQuery } from "@/lib/useCachedQuery";
import { pathIsServices } from "@/lib/navigation";
import { ROUTES, categoryUrl } from "@/lib/routes";
import { topLevelCategories } from "@/lib/categories";
import { legalLinks, serviceLinks } from "@/lib/siteNav";
import { openFeedbackModal } from "@/context/FeedbackContext";
import CartButton from "@/components/cart/CartButton";
import RaccoonLogo from "@/components/RaccoonLogo";
import { useBootstrap } from "@/context/BootstrapContext";
import { useCartOptional } from "@/context/CartContext";

interface MobileNavProps {
  isOpen: boolean;
  onClose: () => void;
}

type NavItem = {
  label: string;
  href: string;
  icon: IconDefinition;
  match?: (path: string) => boolean;
};

const mainNav: NavItem[] = [
  {
    label: "Послуги та ціни",
    href: ROUTES.services,
    icon: faShirt,
    match: pathIsServices,
  },
  { label: "B2B", href: ROUTES.b2b, icon: faHandshake },
  { label: "Акції", href: ROUTES.promotions, icon: faPercent },
  { label: "Доставка", href: ROUTES.delivery, icon: faTruck },
  { label: "Локації", href: ROUTES.locations, icon: faLocationDot },
  { label: "Блог", href: ROUTES.blog, icon: faNewspaper },
  { label: "Контакти", href: ROUTES.contacts, icon: faAddressBook },
  { label: "Викликати кур'єра", href: ROUTES.courier, icon: faUserTie },
];

export default function MobileNav({ isOpen, onClose }: MobileNavProps) {
  const location = useLocation();
  const { categories = [] } = useBootstrap();
  const navCategories = topLevelCategories(categories);
  const cart = useCartOptional();
  const cartCount = cart?.count ?? 0;
  const { data: servicesData } = useCachedQuery("api:services:all", () => fetchAllServicesCached());
  const allServices = servicesData?.data ?? [];

  const [servicesOpen, setServicesOpen] = useState(false);
  const [legalOpen, setLegalOpen] = useState(false);
  const [allServicesOpen, setAllServicesOpen] = useState(false);

  useEffect(() => {
    if (!isOpen) return;
    const prev = document.body.style.overflow;
    document.body.style.overflow = "hidden";
    const onKey = (e: KeyboardEvent) => {
      if (e.key === "Escape") onClose();
    };
    window.addEventListener("keydown", onKey);
    return () => {
      document.body.style.overflow = prev;
      window.removeEventListener("keydown", onKey);
    };
  }, [isOpen, onClose]);

  useEffect(() => {
    if (isOpen) {
      setServicesOpen(false);
      setLegalOpen(false);
      setAllServicesOpen(false);
    }
  }, [location.pathname, isOpen]);

  if (!isOpen) return null;

  const isActive = (href: string, match?: (path: string) => boolean) =>
    match ? match(location.pathname) : location.pathname === href;

  const openConsultation = () => {
    onClose();
    openFeedbackModal();
  };

  return (
    <div className="mobile-menu lg:hidden" role="dialog" aria-modal="true" aria-label="Мобільне меню">
      <button type="button" className="mobile-menu__backdrop" aria-label="Закрити меню" onClick={onClose} />

      <div className="mobile-menu__wrap">
        <div className="mobile-menu__panel">
          <div className="mobile-menu__head">
            <div className="mobile-menu__brand">
              <RaccoonLogo size={32} />
              <span className="font-display font-black text-[15px] text-[#1A1A2E]">ЄНОТ 24</span>
            </div>
            <button type="button" className="mobile-menu__close" onClick={onClose} aria-label="Закрити">
              <FontAwesomeIcon icon={faXmark} />
            </button>
          </div>

          <div className="mobile-menu__quick">
            <a href="tel:+380678872233" className="mobile-menu__quick-btn mobile-menu__quick-btn--phone">
              <FontAwesomeIcon icon={faPhone} />
              <span>Дзвінок</span>
            </a>
            <Link to={ROUTES.cart} onClick={onClose} className="mobile-menu__quick-btn">
              <FontAwesomeIcon icon={faCartShopping} />
              <span>Кошик{cartCount > 0 ? ` · ${cartCount}` : ""}</span>
            </Link>
            <button type="button" onClick={openConsultation} className="mobile-menu__quick-btn mobile-menu__quick-btn--cta">
              <span>Консультація</span>
            </button>
          </div>

          <div className="mobile-menu__scroll">
            <nav className="mobile-menu__nav">
              {mainNav.map((item, i) => {
                const active = isActive(item.href, item.match);
                return (
                  <Link
                    key={item.href}
                    to={item.href}
                    onClick={onClose}
                    className={`mobile-menu__link ${active ? "mobile-menu__link--active" : ""}`}
                    style={{ animationDelay: `${0.04 + i * 0.035}s` }}
                  >
                    <span className="mobile-menu__link-icon">
                      <FontAwesomeIcon icon={item.icon} />
                    </span>
                    <span className="mobile-menu__link-label">{item.label}</span>
                  </Link>
                );
              })}
            </nav>

            <div className="mobile-menu__section">
              <button
                type="button"
                className={`mobile-menu__section-toggle ${servicesOpen ? "is-open" : ""}`}
                onClick={() => setServicesOpen((v) => !v)}
                aria-expanded={servicesOpen}
              >
                <span>Каталог послуг</span>
                <FontAwesomeIcon icon={faChevronDown} className="mobile-menu__chevron" />
              </button>

              {servicesOpen && (
                <div className="mobile-menu__section-body">
                  <div className="mobile-menu__pills">
                    {navCategories.map((cat) => (
                      <Link
                        key={cat.id}
                        to={categoryUrl(cat.id)}
                        onClick={onClose}
                        className="mobile-menu__pill"
                      >
                        {cat.title}
                      </Link>
                    ))}
                  </div>

                  <div className="mobile-menu__sublist">
                    {serviceLinks.map((link) => (
                      <Link key={link.href} to={link.href} onClick={onClose} className="mobile-menu__sublink">
                        {link.label}
                      </Link>
                    ))}
                  </div>

                  {allServices.length > 0 && (
                    <>
                      <button
                        type="button"
                        className={`mobile-menu__all-toggle ${allServicesOpen ? "is-open" : ""}`}
                        onClick={() => setAllServicesOpen((v) => !v)}
                      >
                        Усі послуги ({allServices.length})
                        <FontAwesomeIcon icon={faChevronDown} className="mobile-menu__chevron mobile-menu__chevron--sm" />
                      </button>
                      {allServicesOpen && (
                        <div className="mobile-menu__services-list">
                          {allServices.map((svc) => (
                            <Link key={svc.url} to={svc.url} onClick={onClose} className="mobile-menu__service-item">
                              {svc.name}
                            </Link>
                          ))}
                        </div>
                      )}
                    </>
                  )}
                </div>
              )}
            </div>

            <div className="mobile-menu__section mobile-menu__section--legal">
              <button
                type="button"
                className={`mobile-menu__section-toggle ${legalOpen ? "is-open" : ""}`}
                onClick={() => setLegalOpen((v) => !v)}
                aria-expanded={legalOpen}
              >
                <span>Юридична інформація</span>
                <FontAwesomeIcon icon={faChevronDown} className="mobile-menu__chevron" />
              </button>
              {legalOpen && (
                <div className="mobile-menu__section-body mobile-menu__section-body--legal">
                  {legalLinks.map((link) => (
                    <Link key={link.href} to={link.href} onClick={onClose} className="mobile-menu__sublink">
                      {link.label}
                    </Link>
                  ))}
                </div>
              )}
            </div>
          </div>

          <div className="mobile-menu__foot">
            <CartButton showLabel className="mobile-menu__cart-row" onClick={onClose} />
            <button type="button" onClick={openConsultation} className="btn-primary mobile-menu__cta w-full">
              Замовити консультацію
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
