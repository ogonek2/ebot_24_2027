import { Link } from "react-router-dom";
import type { Promotion } from "@/lib/promotions";
import { promotionUrl } from "@/lib/routes";

export function PromoHowItWorks() {
  const steps = [
    { title: "Прийом", desc: "Здайте річ у відділення або викличте кур'єра" },
    { title: "Доставка", desc: "Заберемо та повернемо у зручний час" },
    { title: "Чистка", desc: "Професійна обробка з контролем якості" },
  ];

  return (
    <div className="promo-side-card">
      <div className="promo-side-card__head">
        <SparkleIcon />
        <h3>Як працює</h3>
      </div>
      <ul className="promo-steps">
        {steps.map((s) => (
          <li key={s.title}>
            <span className="promo-steps__dot" />
            <div>
              <div className="font-bold text-[14px] text-[#1A1A2E]">{s.title}</div>
              <div className="text-[12px] text-[#1A1A2E]/55 mt-0.5 leading-relaxed">{s.desc}</div>
            </div>
          </li>
        ))}
      </ul>
    </div>
  );
}

export function PromoOthersList({ items }: { items: Promotion[] }) {
  if (!items.length) return null;

  return (
    <div className="promo-side-card">
      <div className="promo-side-card__head">
        <GiftIcon />
        <h3>Інші акції</h3>
      </div>
      <ul className="promo-others">
        {items.map((p) => (
          <li key={p.id}>
            <Link to={promotionUrl(p.id)} className="promo-others__link no-underline group">
              <div className="min-w-0 flex-1">
                <div className="font-bold text-[14px] text-[#1A1A2E] group-hover:text-[#f97171] transition-colors">
                  {p.name}
                </div>
                {p.discountAction && (
                  <div
                    className="text-[12px] text-[#f97171] font-semibold mt-0.5 line-clamp-2"
                    style={p.discountColor ? { color: p.discountColor } : undefined}
                  >
                    {p.discountAction}
                  </div>
                )}
              </div>
              <ChevronRight />
            </Link>
          </li>
        ))}
      </ul>
    </div>
  );
}

function SparkleIcon() {
  return (
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#9B8AFB" strokeWidth="2" aria-hidden>
      <path d="M12 3l1.5 5.5L19 10l-5.5 1.5L12 17l-1.5-5.5L5 10l5.5-1.5L12 3z" strokeLinejoin="round" />
    </svg>
  );
}

function GiftIcon() {
  return (
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#9B8AFB" strokeWidth="2" aria-hidden>
      <rect x="3" y="10" width="18" height="11" rx="2" />
      <path d="M12 10V21M3 10h18M12 10c-3-2-5-4-5-6a3 3 0 0 1 5 2 3 3 0 0 1 5-2c0 2-2 4-5 6z" />
    </svg>
  );
}

function ChevronRight() {
  return (
    <svg className="shrink-0 text-[#1A1A2E]/30 group-hover:text-[#f97171]" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" aria-hidden>
      <path d="M9 6l6 6-6 6" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}
