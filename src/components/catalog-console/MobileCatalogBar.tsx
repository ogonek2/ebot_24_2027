import { useState } from "react";
import { resolveSelectionLabel } from "./CategoryNavList";
import CategoryPickerModal from "./CategoryPickerModal";
import type { CatalogNode } from "./types";

type Props = {
  nodes: CatalogNode[];
  selectionId: string;
  onSelectCategory: (id: string) => void;
  query: string;
  onQueryChange: (q: string) => void;
  resultCount: number;
  cartCount: number;
  onCheckout?: () => void;
};

export default function MobileCatalogBar({
  nodes,
  selectionId,
  onSelectCategory,
  query,
  onQueryChange,
  resultCount,
  cartCount,
  onCheckout,
}: Props) {
  const [pickerOpen, setPickerOpen] = useState(false);
  const selectionLabel = resolveSelectionLabel(nodes, selectionId);

  return (
    <>
      <div className="cc-mobile-bar">
        <div className="cc-mobile-bar__actions">
          <button
            type="button"
            className="cc-mobile-bar__icon-btn"
            onClick={() => setPickerOpen(true)}
            aria-label="Категорії"
            aria-haspopup="dialog"
            aria-expanded={pickerOpen}
          >
            <MenuIcon />
          </button>

          <button
            type="button"
            className="cc-mobile-bar__select"
            onClick={() => setPickerOpen(true)}
            aria-label={selectionLabel}
          >
            <span className="truncate">{selectionLabel}</span>
          </button>

          {cartCount > 0 && (
            <button type="button" onClick={onCheckout} className="cc-mobile-bar__cart" aria-label="Кошик">
              <CartIcon />
              <span>{cartCount}</span>
            </button>
          )}
        </div>

        <div className="cc-mobile-bar__search mt-2">
          <SearchIcon />
          <input
            value={query}
            onChange={(e) => onQueryChange(e.target.value)}
            placeholder="Пошук по каталогу…"
            className="cc-mobile-bar__search-input cc-search"
            aria-label="Пошук"
          />
          {query.trim() && (
            <button
              type="button"
              className="cc-mobile-bar__icon-btn"
              onClick={() => onQueryChange("")}
              aria-label="Очистити пошук"
            >
              <CloseIcon />
            </button>
          )}
        </div>
      </div>

      <div className="cc-mobile-bar__meta">
        {resultCount} позицій
        {query.trim() ? " · пошук" : ""}
      </div>

      <CategoryPickerModal
        open={pickerOpen}
        onClose={() => setPickerOpen(false)}
        nodes={nodes}
        selectionId={selectionId}
        onSelectCategory={onSelectCategory}
      />
    </>
  );
}

function MenuIcon() {
  return (
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
      <path d="M4 7h16M4 12h16M4 17h16" strokeLinecap="round" />
    </svg>
  );
}

function SearchIcon() {
  return (
    <svg
      className="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[#1A1A2E]/45"
      width="18"
      height="18"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="2"
      aria-hidden
    >
      <circle cx="11" cy="11" r="7" />
      <path d="M20 20l-3-3" strokeLinecap="round" />
    </svg>
  );
}

function CloseIcon() {
  return (
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" aria-hidden>
      <path d="M6 6l12 12M18 6L6 18" strokeLinecap="round" />
    </svg>
  );
}

function CartIcon() {
  return (
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" aria-hidden>
      <circle cx="9" cy="20" r="1.5" fill="currentColor" stroke="none" />
      <circle cx="17" cy="20" r="1.5" fill="currentColor" stroke="none" />
      <path d="M3 4h2l2.2 11h10.3l1.8-7H7" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}
