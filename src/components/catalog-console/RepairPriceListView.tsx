import { Link } from "react-router-dom";
import type { SpaRepairPriceItem, SpaRepairPriceList } from "@/lib/bootstrap";
import { categoryUrl, ROUTES } from "@/lib/routes";
import { openFeedbackModal } from "@/context/FeedbackContext";
import { useCartOptional } from "@/context/CartContext";
import { buildRepairAddToCartTarget } from "@/lib/cartPrices";

type Props = {
  list: SpaRepairPriceList;
  /** Full page layout vs compact catalog panel */
  variant?: "page" | "panel";
  categoryHref?: string;
};

export default function RepairPriceListView({ list, variant = "page", categoryHref }: Props) {
  const href = categoryHref || list.categoryHref || "remont-vzuttya";
  const cart = useCartOptional();
  const isPage = variant === "page";

  const handleAdd = (item: SpaRepairPriceItem) => {
    const target = buildRepairAddToCartTarget(item);
    if (!target || !cart) return;
    cart.openAddModal(target);
  };

  return (
    <div className={`repair-price ${isPage ? "repair-price--page" : "repair-price--panel"}`}>
      <header className="repair-price__head">
        <div>
          <p className="repair-price__eyebrow">Ремонт взуття</p>
          <h2 className="repair-price__title">{list.title}</h2>
          <p className="repair-price__lead">
            {isPage
              ? "Ціни орієнтовні («від»). Додайте позиції в кошик — майстер підтвердить вартість після огляду."
              : "Орієнтовні ціни «від». Точну вартість підтверджуємо після огляду майстром."}
          </p>
        </div>
        {isPage ? (
          <div className="repair-price__head-actions">
            <Link to={ROUTES.cart} className="repair-price__cta repair-price__cta--ghost no-underline">
              До кошика
            </Link>
            <button type="button" className="repair-price__cta" onClick={() => openFeedbackModal()}>
              Записатись
            </button>
          </div>
        ) : (
          <Link to={categoryUrl(href)} className="repair-price__cta no-underline">
            Повний прайс →
          </Link>
        )}
      </header>

      {isPage && (
        <nav className="repair-price__toc" aria-label="Секції прайсу">
          {list.sections.map((section) => (
            <a key={section.id} href={`#repair-section-${section.id}`} className="repair-price__toc-link">
              {section.title}
            </a>
          ))}
        </nav>
      )}

      <div className={`repair-price__grid ${!isPage ? "repair-price__grid--compact" : ""}`}>
        {(isPage ? list.sections : list.sections.slice(0, 4)).map((section) => (
          <section
            key={section.id}
            id={isPage ? `repair-section-${section.id}` : undefined}
            className="repair-price__section"
          >
            <h3 className="repair-price__section-title">{section.title}</h3>
            <ul className="repair-price__list">
              {(isPage ? section.items : section.items.slice(0, 4)).map((item) => {
                const canAdd = item.price > 0;
                return (
                  <li key={item.id} className="repair-price__row">
                    <div className="repair-price__name">
                      <span>{item.name}</span>
                      {(item.note || item.unit) && (
                        <span className="repair-price__note">
                          {[item.note, item.unit].filter(Boolean).join(" · ")}
                        </span>
                      )}
                    </div>
                    <div className="repair-price__aside">
                      <span className="repair-price__cost">{item.priceLabel}</span>
                      {isPage && (
                        <button
                          type="button"
                          className="repair-price__add"
                          disabled={!canAdd || !cart}
                          onClick={() => handleAdd(item)}
                          aria-label={`Додати «${item.name}» до кошика`}
                        >
                          У кошик
                        </button>
                      )}
                    </div>
                  </li>
                );
              })}
            </ul>
          </section>
        ))}
      </div>

      {!isPage && list.sections.length > 4 && (
        <div className="repair-price__more">
          <Link to={categoryUrl(href)} className="no-underline">
            Усі {list.sections.length} секцій прайсу →
          </Link>
        </div>
      )}
    </div>
  );
}
