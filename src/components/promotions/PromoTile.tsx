import { Link } from "react-router-dom";
import type { CSSProperties } from "react";
import type { Promotion } from "@/lib/promotions";
import { promotionUrl } from "@/lib/routes";
import { promoCardTheme, splitPromoTitle } from "@/lib/promoTheme";

type Props = {
  promo: Promotion;
  lead?: boolean;
};

export default function PromoTile({ promo, lead = false }: Props) {
  const theme = promoCardTheme(promo);
  const titleLines = splitPromoTitle(promo.name);

  const style = {
    "--promo-bg": theme.bg,
    "--promo-title": theme.title,
    "--promo-discount": theme.discount,
    "--promo-meta": theme.meta,
  } as CSSProperties;

  return (
    <Link
      to={promotionUrl(promo.id)}
      className={`promo-home-tile no-underline group ${lead ? "promo-home-tile--lead" : ""}`}
      style={style}
    >
      {promo.banner && <img src={promo.banner} alt="" className="promo-home-tile__banner" loading="lazy" />}
      <span className="promo-home-tile__pattern" aria-hidden />
      {promo.discountAction && <span className="promo-home-tile__badge">{promo.discountAction}</span>}
      <span className="promo-home-tile__title">
        {titleLines.map((line, i) => (
          <span key={`${line}-${i}`}>
            {line}
            {i < titleLines.length - 1 && <br />}
          </span>
        ))}
      </span>
      {promo.locations && <span className="promo-home-tile__meta">{promo.locations}</span>}
      <span className="promo-home-tile__arrow" aria-hidden>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2">
          <path d="M5 12h14M13 6l6 6-6 6" strokeLinecap="round" strokeLinejoin="round" />
        </svg>
      </span>
    </Link>
  );
}
