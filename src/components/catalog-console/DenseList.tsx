import { forwardRef } from "react";
import { Link } from "react-router-dom";
import { resolveCatalogCleaningDisplay } from "@/lib/cartPrices";
import { serviceUrl } from "@/lib/routes";
import {
  formatPriceCompact,
  resolveItemPricing,
} from "./utils";
import type { DensityMode, FlatRow } from "./types";

type Props = {
  rows: FlatRow[];
  density: DensityMode;
  selectedKey: string | null;
  inCartKeys: Set<string>;
  globalSearch: boolean;
  onHover: (row: FlatRow) => void;
  onSelect: (row: FlatRow) => void;
  onAdd: (row: FlatRow) => void;
};

function formatCleaningPrice(raw: string | null): string {
  if (!raw) return "—";
  return formatPriceCompact(raw);
}

const DenseList = forwardRef<HTMLDivElement, Props>(function DenseList(
  { rows, density, selectedKey, inCartKeys, globalSearch, onHover, onSelect, onAdd },
  ref,
) {
  const rowDensity = density === "compact" ? "cc-dense-row--compact" : "cc-dense-row--comfortable";

  return (
    <div className="cc-list-panel">
      <div className="cc-dense-head-wrap border-b border-white/25 bg-white/10">
        <div className="cc-dense-head px-4 text-[11px] font-bold uppercase tracking-wider text-[#1A1A2E]/38">
          <span>Послуга</span>
          <span className="text-right">Інд</span>
          <span className="text-right">Поток</span>
          <span />
        </div>
      </div>

      <div className="cc-list-flow" ref={ref}>
        {rows.length === 0 && (
          <div className="px-4 py-16 text-center text-[14px] text-[#1A1A2E]/45">Нічого не знайдено</div>
        )}

        {rows.map((row) => {
          const pricing = resolveItemPricing(row.item);
          const cleaning = resolveCatalogCleaningDisplay(row.item);
          const onRequest = cleaning.isOnRequest;
          const selected = selectedKey === row.key;
          const inCart = inCartKeys.has(cartKey(row));

          const servicePage =
            row.item.serviceHref && row.item.categoryHref
              ? serviceUrl(row.item.categoryHref, row.item.serviceHref)
              : null;

          return (
            <div key={row.key}>
              {row.showSubgroupHeader && (
                <div className="cc-subhead px-4 py-2.5 text-[12px] font-bold text-[#1A1A2E]/50 border-b border-white/20 bg-white/15">
                  {globalSearch ? `${row.category.title} · ${row.subgroupTitle}` : row.subgroupTitle}
                </div>
              )}

              <div
                role="button"
                tabIndex={0}
                onMouseEnter={() => onHover(row)}
                onFocus={() => onHover(row)}
                onClick={() => onSelect(row)}
                onKeyDown={(e) => {
                  if (e.key === "Enter" || e.key === " ") {
                    e.preventDefault();
                    onSelect(row);
                  }
                }}
                className={`cc-dense-row ${rowDensity} px-4 ${selected ? "cc-dense-row--selected" : ""} ${inCart ? "cc-dense-row--cart" : ""} ${pricing.promo ? "cc-dense-row--promo" : ""}`}
              >
                <div className="min-w-0 flex items-center gap-2">
                  {servicePage ? (
                    <Link
                      to={servicePage}
                      onClick={(e) => e.stopPropagation()}
                      className="truncate text-[16px] sm:text-[17px] font-medium text-[#1A1A2E] hover:text-[var(--cc-accent)] no-underline"
                    >
                      {row.item.name}
                    </Link>
                  ) : (
                    <span className="truncate text-[16px] sm:text-[17px] font-medium text-[#1A1A2E]">
                      {row.item.name}
                    </span>
                  )}
                  {pricing.promo && <span className="cc-chip cc-chip--promo">акція</span>}
                  {onRequest && <span className="cc-chip">запит</span>}
                </div>

                <span className={`cc-price ${pricing.promo && cleaning.hasIndividual ? "text-[var(--cc-accent)]" : ""}`}>
                  {formatCleaningPrice(cleaning.individualRaw)}
                </span>
                <span className="cc-price cc-price--muted">
                  {formatCleaningPrice(cleaning.streamRaw)}
                </span>

                <button
                  type="button"
                  onClick={(e) => {
                    e.stopPropagation();
                    onAdd(row);
                  }}
                  className={`cc-add-btn ${inCart ? "cc-add-btn--active" : ""}`}
                  aria-label={`Додати ${row.item.name}`}
                >
                  {inCart ? "✓" : "+"}
                </button>
              </div>

              <div
                role="button"
                tabIndex={0}
                onClick={() => onSelect(row)}
                onKeyDown={(e) => {
                  if (e.key === "Enter" || e.key === " ") {
                    e.preventDefault();
                    onSelect(row);
                  }
                }}
                className={`cc-mobile-row ${selected ? "cc-mobile-row--selected" : ""} ${inCart ? "cc-mobile-row--cart" : ""} ${pricing.promo ? "cc-mobile-row--promo" : ""}`}
              >
                <div className="cc-mobile-row__main">
                  <div className="cc-mobile-row__title">
                    {servicePage ? (
                      <Link
                        to={servicePage}
                        onClick={(e) => e.stopPropagation()}
                        className="text-[#1A1A2E] hover:text-[var(--cc-accent)] no-underline"
                      >
                        {row.item.name}
                      </Link>
                    ) : (
                      row.item.name
                    )}
                  </div>
                  <div className="cc-mobile-row__badges">
                    {pricing.promo && <span className="cc-chip cc-chip--promo">акція</span>}
                    {onRequest && <span className="cc-chip">за запитом</span>}
                  </div>
                </div>

                <div
                  className={`cc-mobile-row__prices ${cleaning.hasStream && cleaning.hasIndividual ? "" : "cc-mobile-row__prices--single"}`}
                >
                  {cleaning.hasIndividual && (
                    <div className="cc-mobile-price">
                      <span className="cc-mobile-price__label">Індивідуальна</span>
                      <span className={`cc-mobile-price__value ${pricing.promo ? "text-[var(--cc-accent)]" : ""}`}>
                        {formatCleaningPrice(cleaning.individualRaw)}
                      </span>
                    </div>
                  )}
                  {cleaning.hasStream && (
                    <div className="cc-mobile-price">
                      <span className="cc-mobile-price__label">Потокова</span>
                      <span className="cc-mobile-price__value cc-mobile-price__value--muted">
                        {formatCleaningPrice(cleaning.streamRaw)}
                      </span>
                    </div>
                  )}
                  {onRequest && !cleaning.hasStream && !cleaning.hasIndividual && (
                    <div className="cc-mobile-price">
                      <span className="cc-mobile-price__label">Ціна</span>
                      <span className="cc-mobile-price__value cc-mobile-price__value--muted">За запитом</span>
                    </div>
                  )}
                </div>

                <button
                  type="button"
                  onClick={(e) => {
                    e.stopPropagation();
                    onAdd(row);
                  }}
                  className={`cc-mobile-add ${inCart ? "cc-mobile-add--active" : ""}`}
                  aria-label={`Додати ${row.item.name}`}
                >
                  {inCart ? "✓" : "+"}
                </button>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
});

export default DenseList;

export function cartKey(row: FlatRow): string {
  return `${row.category.id}:${row.item.name}:individual`;
}
