import { PROMO_FILTERS } from "@/lib/promotions";

export function PromoFiltersBar({
  active,
  onChange,
  count,
}: {
  active: string;
  onChange: (id: string) => void;
  count: number;
}) {
  return (
    <div className="promo-filters">
      {PROMO_FILTERS.map((f) => (
        <button
          key={f.id}
          type="button"
          onClick={() => onChange(f.id)}
          className={`promo-filter-chip ${active === f.id ? "is-active" : ""}`}
        >
          {f.label}
        </button>
      ))}
      <span className="promo-filters__count ml-auto hidden sm:inline">{count} активних</span>
    </div>
  );
}
