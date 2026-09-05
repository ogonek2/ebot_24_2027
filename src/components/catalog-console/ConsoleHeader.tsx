import { useId } from "react";
import { buildCategoryOptions } from "./CategoryNavList";
import type { CatalogFilters, CatalogNode, DensityMode } from "./types";

type Props = {
  nodes: CatalogNode[];
  selectionId: string;
  onSelectCategory: (id: string) => void;
  query: string;
  onQueryChange: (q: string) => void;
  filters: CatalogFilters;
  onToggleFilter: (key: keyof CatalogFilters) => void;
  density: DensityMode;
  onDensityChange: (d: DensityMode) => void;
  resultCount: number;
  cartCount: number;
  onCheckout?: () => void;
};

const FILTER_LABELS: { key: keyof CatalogFilters; label: string }[] = [
  { key: "hasPrice", label: "Є ціна" },
  { key: "onRequest", label: "За запитом" },
  { key: "promo", label: "Акція" },
  { key: "fastTerm", label: "До 24 год" },
];

export default function ConsoleHeader({
  nodes,
  selectionId,
  onSelectCategory,
  query,
  onQueryChange,
  filters,
  onToggleFilter,
  density,
  onDensityChange,
  resultCount,
  cartCount,
  onCheckout,
}: Props) {
  const selectId = useId();
  const options = buildCategoryOptions(nodes);
  const selectValue = options.some((o) => o.id === selectionId) ? selectionId : options[0]?.id ?? "";

  return (
    <div className="cc-header border-b border-white/30">
      <div className="flex items-center gap-3">
        <label htmlFor={selectId} className="sr-only">
          Категорія
        </label>
        <select
          id={selectId}
          value={selectValue}
          onChange={(e) => onSelectCategory(e.target.value)}
          className="cc-header-select shrink-0 hidden md:block"
        >
          {options.map((opt) => (
            <option key={opt.id} value={opt.id}>
              {opt.label}
            </option>
          ))}
        </select>

        <div className="relative flex-1 min-w-0">
          <SearchIcon />
          <input
            value={query}
            onChange={(e) => onQueryChange(e.target.value)}
            placeholder="Пошук по всьому каталогу…"
            className="cc-search w-full"
          />
        </div>
        {cartCount > 0 && (
          <button type="button" onClick={onCheckout} className="cc-cart-btn shrink-0">
            <CartIcon />
            <span>{cartCount}</span>
          </button>
        )}
      </div>

      <div className="flex flex-wrap items-center gap-2 mt-2.5">
        <div className="cc-filter-scroll flex gap-1.5 flex-1 min-w-0 overflow-x-auto cc-scroll-x pb-0.5">
          {FILTER_LABELS.map(({ key, label }) => (
            <button
              key={key}
              type="button"
              onClick={() => onToggleFilter(key)}
              className={`cc-filter-chip shrink-0 ${filters[key] ? "cc-filter-chip--active" : ""}`}
            >
              {label}
            </button>
          ))}
        </div>

        <div className="cc-density-seg shrink-0 hidden sm:inline-flex">
          <button
            type="button"
            onClick={() => onDensityChange("compact")}
            className={density === "compact" ? "cc-density-seg--active" : ""}
          >
            Щільний
          </button>
          <button
            type="button"
            onClick={() => onDensityChange("comfortable")}
            className={density === "comfortable" ? "cc-density-seg--active" : ""}
          >
            Комфорт
          </button>
        </div>
      </div>

      <div className="text-[11px] text-[#1A1A2E]/40 font-medium mt-2">
        {resultCount} позицій{query.trim() ? " · пошук по каталогу" : ""}
      </div>
    </div>
  );
}

function SearchIcon() {
  return (
    <svg className="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[#1A1A2E]/35" width="15" height="15" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24" aria-hidden>
      <circle cx="11" cy="11" r="7" />
      <path d="M20 20l-3-3" strokeLinecap="round" />
    </svg>
  );
}

function CartIcon() {
  return (
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
      <circle cx="9" cy="20" r="1.5" fill="currentColor" stroke="none" />
      <circle cx="17" cy="20" r="1.5" fill="currentColor" stroke="none" />
      <path d="M3 4h2l2.2 11h10.3l1.8-7H7" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}
