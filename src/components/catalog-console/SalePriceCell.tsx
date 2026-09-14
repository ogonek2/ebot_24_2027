import { formatPriceCompact } from "./utils";

type Props = {
  current: string | null;
  oldPrice?: string | null;
  discountPercent?: number | null;
  muted?: boolean;
  accent?: boolean;
  className?: string;
};

/** Compact sale price: current + optional struck old + −N% */
export default function SalePriceCell({
  current,
  oldPrice,
  discountPercent,
  muted,
  accent,
  className = "",
}: Props) {
  if (!current) {
    return <span className={`cc-price ${muted ? "cc-price--muted" : ""} ${className}`}>—</span>;
  }

  const hasSale = Boolean(oldPrice && discountPercent && discountPercent > 0);

  return (
    <span
      className={`cc-price cc-price--stack ${muted ? "cc-price--muted" : ""} ${accent ? "text-[var(--cc-accent)]" : ""} ${className}`}
    >
      <span className="cc-price__now">{formatPriceCompact(current)}</span>
      {hasSale && (
        <span className="cc-price__sale-meta">
          <span className="cc-price__old">{formatPriceCompact(oldPrice!)}</span>
          <span className="cc-price__pct">−{discountPercent}%</span>
        </span>
      )}
    </span>
  );
}
