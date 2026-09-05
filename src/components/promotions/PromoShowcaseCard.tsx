import { Link } from "react-router-dom";
import type { CSSProperties } from "react";
import type { Promotion } from "@/lib/promotions";
import { promotionUrl } from "@/lib/routes";
import { promoCardTheme, promoLayoutForIndex, splitPromoTitle, type PromoLayout } from "@/lib/promoTheme";

type Props = {
  promo: Promotion;
  index?: number;
  layout?: PromoLayout;
};

export default function PromoShowcaseCard({ promo, index = 0, layout }: Props) {
  const cardLayout = layout ?? promoLayoutForIndex(index);
  const theme = promoCardTheme(promo);
  const titleLines = splitPromoTitle(promo.name);

  const style = {
    "--promo-bg": theme.bg,
    "--promo-title": theme.title,
    "--promo-discount": theme.discount,
    "--promo-meta": theme.meta,
    "--promo-cta-bg": theme.ctaBg,
    "--promo-cta-text": theme.ctaText,
  } as CSSProperties;

  return (
    <Link
      to={promotionUrl(promo.id)}
      className={`promo-showcase promo-showcase--${cardLayout} no-underline group`}
      style={style}
    >
      {promo.banner && (
        <img src={promo.banner} alt="" className="promo-showcase__banner" loading="lazy" />
      )}
      <span className="promo-showcase__shine" aria-hidden />
      <span className="promo-showcase__pattern" aria-hidden />

      <div className="promo-showcase__body">
        <div className="promo-showcase__top">
          <h2 className="promo-showcase__title">
            {titleLines.map((line, i) => (
              <span key={`${line}-${i}`}>
                {line}
                {i < titleLines.length - 1 && <br />}
              </span>
            ))}
          </h2>
          {promo.discountAction && (
            <div className="promo-showcase__discount">{promo.discountAction}</div>
          )}
        </div>

        <div className="promo-showcase__foot">
          {promo.locations && <span className="promo-showcase__loc">{promo.locations}</span>}
          <span className="promo-showcase__cta">
            Детальніше
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.4" aria-hidden>
              <path d="M5 12h14M13 6l6 6-6 6" strokeLinecap="round" strokeLinejoin="round" />
            </svg>
          </span>
        </div>
      </div>
    </Link>
  );
}
