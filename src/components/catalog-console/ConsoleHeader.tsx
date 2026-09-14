import { useId, useState } from "react";
import { resolveSelectionLabel } from "./CategoryNavList";
import CategoryPickerModal from "./CategoryPickerModal";
import type { CatalogNode, DensityMode } from "./types";

type Props = {
  nodes: CatalogNode[];
  selectionId: string;
  onSelectCategory: (id: string) => void;
  query: string;
  onQueryChange: (q: string) => void;
  density: DensityMode;
  onDensityChange: (d: DensityMode) => void;
  resultCount: number;
  cartCount: number;
  onCheckout?: () => void;
};

export default function ConsoleHeader({
  nodes,
  selectionId,
  onSelectCategory,
  query,
  onQueryChange,
  density,
  onDensityChange,
  resultCount,
  cartCount,
  onCheckout,
}: Props) {
  const selectId = useId();
  const [pickerOpen, setPickerOpen] = useState(false);
  const label = resolveSelectionLabel(nodes, selectionId);

  return (
    <div className="cc-header border-b border-white/30">
      <div className="flex items-center gap-3">
        <label htmlFor={selectId} className="sr-only">
          Категорія
        </label>
        <button
          type="button"
          id={selectId}
          className="cc-header-select shrink-0 hidden md:inline-flex"
          aria-label="Виберіть категорію"
          aria-haspopup="dialog"
          aria-expanded={pickerOpen}
          onClick={() => setPickerOpen(true)}
        >
          <span className="cc-header-select__label">{label}</span>
        </button>

        <CategoryPickerModal
          open={pickerOpen}
          onClose={() => setPickerOpen(false)}
          nodes={nodes}
          selectionId={selectionId}
          onSelectCategory={onSelectCategory}
        />

        <div className="relative flex-1 min-w-0">
          <div className="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[#1A1A2E]/45">
            <i className="fa-solid fa-magnifying-glass" aria-hidden />
          </div>
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

      <div className="flex flex-wrap items-center justify-between gap-2 mt-2.5">
        <div className="text-[11px] text-[#1A1A2E]/40 font-medium">
          {resultCount} позицій{query.trim() ? " · пошук по каталогу" : ""}
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
    </div>
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
