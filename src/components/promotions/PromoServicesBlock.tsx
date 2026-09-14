import { Link } from "react-router-dom";
import type { SpaCatalogItem } from "@/lib/bootstrap";
import { buildAddToCartTarget, parseUah } from "@/lib/cartPrices";
import { useCart } from "@/context/CartContext";
import { serviceUrl } from "@/lib/routes";

type Props = {
  services: SpaCatalogItem[];
};

export default function PromoServicesBlock({ services }: Props) {
  const { openAddModal } = useCart();

  if (!services.length) return null;

  return (
    <section className="promo-services">
      <h2 className="promo-services__title">Послуги в акції</h2>
      <ul className="promo-services__list">
        {services.map((item) => {
          const href =
            item.categoryHref && item.href ? serviceUrl(item.categoryHref, item.href) : null;
          const target = buildAddToCartTarget({
            serviceId: item.id,
            serviceName: item.name,
            streamPrice: parseUah(item.price),
            individualPrice: item.individualPrice ? parseUah(item.individualPrice) : null,
          });

          return (
            <li key={item.id} className="promo-services__row">
              <div className="min-w-0">
                {href ? (
                  <Link to={href} className="promo-services__name">
                    {item.name}
                  </Link>
                ) : (
                  <span className="promo-services__name">{item.name}</span>
                )}
                {item.discountPercent ? (
                  <div className="promo-services__pct">−{item.discountPercent}%</div>
                ) : null}
              </div>

              <div className="promo-services__prices">
                <span className="promo-services__price">{item.price}</span>
                {item.oldPrice && <span className="promo-services__old">{item.oldPrice}</span>}
              </div>

              <div className="promo-services__actions">
                {target && (
                  <button
                    type="button"
                    className="promo-cta promo-cta--primary"
                    onClick={() => openAddModal(target)}
                  >
                    Додати
                  </button>
                )}
                {href && (
                  <Link to={href} className="promo-cta promo-cta--ghost no-underline">
                    Детальніше
                  </Link>
                )}
              </div>
            </li>
          );
        })}
      </ul>
    </section>
  );
}
