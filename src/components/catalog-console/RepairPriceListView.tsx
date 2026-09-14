import { Link } from "react-router-dom";
import type { SpaRepairPriceList } from "@/lib/bootstrap";
import { categoryUrl } from "@/lib/routes";
import { openFeedbackModal } from "@/context/FeedbackContext";

type Props = {
  list: SpaRepairPriceList;
  /** Full page layout vs compact catalog panel */
  variant?: "page" | "panel";
  categoryHref?: string;
};

export default function RepairPriceListView({ list, variant = "page", categoryHref }: Props) {
  const href = categoryHref || list.categoryHref || "remont-vzuttya";

  return (
    <div className={`repair-price ${variant === "panel" ? "repair-price--panel" : ""}`}>
      <header className="repair-price__head">
        <div>
          <p className="repair-price__eyebrow">Ремонт взуття</p>
          <h2 className="repair-price__title">{list.title}</h2>
          <p className="repair-price__lead">
            Орієнтовні ціни «від». Точну вартість підтверджуємо після огляду майстром.
          </p>
        </div>
        {variant === "panel" ? (
          <Link to={categoryUrl(href)} className="repair-price__cta no-underline">
            Повний прайс →
          </Link>
        ) : (
          <button type="button" className="repair-price__cta" onClick={() => openFeedbackModal()}>
            Записатись на ремонт
          </button>
        )}
      </header>

      <div className={`repair-price__grid ${variant === "panel" ? "repair-price__grid--compact" : ""}`}>
        {(variant === "panel" ? list.sections.slice(0, 4) : list.sections).map((section) => (
          <section key={section.id} className="repair-price__section">
            <h3 className="repair-price__section-title">{section.title}</h3>
            <ul className="repair-price__list">
              {(variant === "panel" ? section.items.slice(0, 4) : section.items).map((item) => (
                <li key={item.id} className="repair-price__row">
                  <div className="repair-price__name">
                    <span>{item.name}</span>
                    {(item.note || item.unit) && (
                      <span className="repair-price__note">
                        {[item.note, item.unit].filter(Boolean).join(" · ")}
                      </span>
                    )}
                  </div>
                  <span className="repair-price__cost">{item.priceLabel}</span>
                </li>
              ))}
            </ul>
          </section>
        ))}
      </div>

      {variant === "panel" && list.sections.length > 4 && (
        <div className="repair-price__more">
          <Link to={categoryUrl(href)} className="no-underline">
            Усі {list.sections.length} секцій прайсу →
          </Link>
        </div>
      )}
    </div>
  );
}
