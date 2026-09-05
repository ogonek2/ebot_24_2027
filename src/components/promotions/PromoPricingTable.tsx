import type { Promotion } from "@/lib/promotions";
import { openFeedbackModal } from "@/context/FeedbackContext";

type Props = {
  promo: Promotion;
};

export default function PromoDetailCard({ promo }: Props) {
  return (
    <div className="promo-detail-card">
      <div className="promo-detail-card__hero">
        {promo.discountAction && (
          <div
            className="promo-detail-card__discount"
            style={promo.discountColor ? { color: promo.discountColor } : undefined}
          >
            {promo.discountAction}
          </div>
        )}
        <div className="min-w-0 flex-1">
          <h1 className="promo-detail-card__title">{promo.name}</h1>
          {promo.locations && <p className="promo-detail-card__desc">{promo.locations}</p>}
        </div>
      </div>

      {promo.terms && (
        <div className="promo-detail-terms">
          <h2>Умови акції</h2>
          <div className="promo-detail-terms__body font-reading" dangerouslySetInnerHTML={{ __html: promo.terms }} />
        </div>
      )}

      <div className="promo-detail-actions">
        <button type="button" onClick={openFeedbackModal} className="promo-cta promo-cta--primary promo-cta--wide">
          Додати до замовлення
        </button>
        <button type="button" onClick={openFeedbackModal} className="promo-cta promo-cta--ghost">
          <TruckIcon />
          Замовити кур&apos;єра
        </button>
      </div>
    </div>
  );
}

function TruckIcon() {
  return (
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" aria-hidden>
      <path d="M3 17h13v-6H3zM16 11h3l2 3v3h-5z" strokeLinejoin="round" />
      <circle cx="7" cy="17" r="2" />
      <circle cx="18" cy="17" r="2" />
    </svg>
  );
}
