import { useEffect, useMemo, useState } from "react";
import { Link } from "react-router-dom";
import CategoryIcon from "./CategoryIcon";
import { useBootstrap } from "@/context/BootstrapContext";
import { ROUTES, categoryUrl } from "@/lib/routes";
import { childCategories, topLevelCategories } from "@/lib/categories";
import type { SpaCatalogCategory } from "@/lib/bootstrap";

type ServicesMegaMenuProps = {
  onNavigate?: () => void;
};

const QUICK = [
  { label: "Прайс", href: ROUTES.services },
  { label: "Кур'єр", href: ROUTES.courier },
  { label: "Доставка", href: ROUTES.delivery },
  { label: "Локації", href: ROUTES.locations },
  { label: "Акції", href: ROUTES.promotions },
] as const;

export default function ServicesMegaMenu({ onNavigate }: ServicesMegaMenuProps) {
  const { categories = [] } = useBootstrap();
  const navCategories = useMemo(() => topLevelCategories(categories), [categories]);
  const [activeId, setActiveId] = useState<string | null>(null);

  useEffect(() => {
    if (!navCategories.length) {
      setActiveId(null);
      return;
    }
    if (!activeId || !navCategories.some((c) => c.id === activeId)) {
      setActiveId(navCategories[0]!.id);
    }
  }, [navCategories, activeId]);

  const active: SpaCatalogCategory | undefined = navCategories.find((c) => c.id === activeId);
  const subs = active ? childCategories(categories, active.id) : [];
  const serviceCount = active?.serviceCount ?? active?.items?.length ?? 0;

  return (
    <div className="mega-menu">
      <div className="mega-menu__glow" aria-hidden />

      <div className="mega-menu__layout">
        <aside className="mega-menu__index" aria-label="Категорії послуг">
          <div className="mega-menu__kicker">
            <span>Каталог</span>
            <span className="mega-menu__count">{navCategories.length}</span>
          </div>

          {navCategories.length > 0 ? (
            <ul className="mega-menu__cats">
              {navCategories.map((cat, i) => {
                const isActive = cat.id === activeId;
                return (
                  <li key={cat.id}>
                    <Link
                      to={categoryUrl(cat.id)}
                      onClick={onNavigate}
                      onMouseEnter={() => setActiveId(cat.id)}
                      onFocus={() => setActiveId(cat.id)}
                      className={`mega-menu__cat ${isActive ? "is-active" : ""}`}
                    >
                      <span className="mega-menu__cat-num">{String(i + 1).padStart(2, "0")}</span>
                      <span className="mega-menu__cat-icon" aria-hidden>
                        <CategoryIcon src={cat.iconUrl} size={22} alt="" />
                      </span>
                      <span className="mega-menu__cat-title">{cat.title}</span>
                    </Link>
                  </li>
                );
              })}
            </ul>
          ) : (
            <p className="mega-menu__empty">Завантаження…</p>
          )}
        </aside>

        <section className="mega-menu__stage" aria-live="polite">
          {active ? (
            <>
              <div className="mega-menu__stage-head">
                <div className="mega-menu__stage-mark" aria-hidden>
                  <CategoryIcon src={active.iconUrl} size={36} alt="" />
                </div>
                <div className="min-w-0">
                  <p className="mega-menu__stage-label">Обрана категорія</p>
                  <h3 className="mega-menu__stage-title">{active.title}</h3>
                  <p className="mega-menu__stage-meta">
                    {subs.length > 0
                      ? `${subs.length} напрямів`
                      : serviceCount > 0
                        ? `${serviceCount} послуг`
                        : "Перейти до прайсу"}
                  </p>
                </div>
              </div>

              {subs.length > 0 ? (
                <ul className="mega-menu__subs">
                  {subs.map((sub) => (
                    <li key={sub.id}>
                      <Link
                        to={categoryUrl(sub.id)}
                        onClick={onNavigate}
                        className="mega-menu__sub"
                      >
                        <span>{sub.title}</span>
                        <span className="mega-menu__sub-arrow" aria-hidden>
                          →
                        </span>
                      </Link>
                    </li>
                  ))}
                </ul>
              ) : (
                <p className="mega-menu__hint">
                  Відкрийте категорію, щоб побачити послуги та ціни в каталозі.
                </p>
              )}

              <div className="mega-menu__actions">
                <Link
                  to={categoryUrl(active.id)}
                  onClick={onNavigate}
                  className="mega-menu__cta"
                >
                  Відкрити категорію
                  <span aria-hidden>→</span>
                </Link>
                <Link to={ROUTES.services} onClick={onNavigate} className="mega-menu__ghost">
                  Весь прайс
                </Link>
              </div>
            </>
          ) : (
            <p className="mega-menu__empty">Оберіть категорію зліва</p>
          )}
        </section>
      </div>

      <footer className="mega-menu__foot">
        <nav className="mega-menu__quick" aria-label="Швидкі посилання">
          {QUICK.map((link) => (
            <Link key={link.href} to={link.href} onClick={onNavigate} className="mega-menu__quick-link">
              {link.label}
            </Link>
          ))}
        </nav>
        <a href="tel:+380678872233" className="mega-menu__phone">
          067 887 22 33
        </a>
      </footer>
    </div>
  );
}
