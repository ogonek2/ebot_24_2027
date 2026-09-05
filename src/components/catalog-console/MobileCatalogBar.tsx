import { useEffect, useId, useRef, useState } from "react";
import { createPortal } from "react-dom";
import CategoryNavList, { buildCategoryOptions, resolveSelectionLabel } from "./CategoryNavList";
import type { CatalogFilters, CatalogNode } from "./types";

const FILTER_LABELS: { key: keyof CatalogFilters; label: string }[] = [
  { key: "hasPrice", label: "Є ціна" },
  { key: "onRequest", label: "За запитом" },
  { key: "promo", label: "Акція" },
  { key: "fastTerm", label: "До 24 год" },
];

type Props = {
  nodes: CatalogNode[];
  selectionId: string;
  onSelectCategory: (id: string) => void;
  query: string;
  onQueryChange: (q: string) => void;
  filters: CatalogFilters;
  onToggleFilter: (key: keyof CatalogFilters) => void;
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
  filters,
  onToggleFilter,
  resultCount,
  cartCount,
  onCheckout,
}: Props) {
  const [drawerOpen, setDrawerOpen] = useState(false);
  const [searchOpen, setSearchOpen] = useState(false);
  const searchRef = useRef<HTMLInputElement>(null);
  const selectId = useId();
  const options = buildCategoryOptions(nodes);
  const selectionLabel = resolveSelectionLabel(nodes, selectionId);

  useEffect(() => {
    if (searchOpen) searchRef.current?.focus();
  }, [searchOpen]);

  useEffect(() => {
    if (!drawerOpen) return;
    const onKey = (e: KeyboardEvent) => {
      if (e.key === "Escape") setDrawerOpen(false);
    };
    window.addEventListener("keydown", onKey);
    return () => window.removeEventListener("keydown", onKey);
  }, [drawerOpen]);

  useEffect(() => {
    if (!drawerOpen) return;
    const prev = document.body.style.overflow;
    document.body.style.overflow = "hidden";
    return () => {
      document.body.style.overflow = prev;
    };
  }, [drawerOpen]);

  const closeSearch = () => {
    setSearchOpen(false);
    onQueryChange("");
  };

  const pickCategory = (id: string) => {
    onSelectCategory(id);
    setDrawerOpen(false);
  };

  return (
    <>
      <div className={`cc-mobile-bar ${searchOpen ? "is-search-open" : ""}`}>
        {searchOpen ? (
          <div className="cc-mobile-bar__search">
            <SearchIcon />
            <input
              ref={searchRef}
              value={query}
              onChange={(e) => onQueryChange(e.target.value)}
              onBlur={() => {
                if (!query.trim()) setSearchOpen(false);
              }}
              placeholder="Пошук по каталогу…"
              className="cc-mobile-bar__search-input"
              aria-label="Пошук"
            />
            <button type="button" className="cc-mobile-bar__icon-btn" onClick={closeSearch} aria-label="Закрити пошук">
              <CloseIcon />
            </button>
          </div>
        ) : (
          <div className="cc-mobile-bar__actions">
            <button
              type="button"
              className="cc-mobile-bar__icon-btn"
              onClick={() => setDrawerOpen(true)}
              aria-label="Категорії"
            >
              <MenuIcon />
            </button>

            <label htmlFor={selectId} className="sr-only">
              Категорія
            </label>
            <select
              id={selectId}
              value={options.some((o) => o.id === selectionId) ? selectionId : options[0]?.id ?? ""}
              onChange={(e) => onSelectCategory(e.target.value)}
              className="cc-mobile-bar__select"
              aria-label={selectionLabel}
            >
              {options.map((opt) => (
                <option key={opt.id} value={opt.id}>
                  {opt.label}
                </option>
              ))}
            </select>

            <button
              type="button"
              className="cc-mobile-bar__icon-btn"
              onClick={() => setSearchOpen(true)}
              aria-label="Пошук"
            >
              <SearchIcon />
            </button>

            {cartCount > 0 && (
              <button type="button" onClick={onCheckout} className="cc-mobile-bar__cart" aria-label="Кошик">
                <CartIcon />
                <span>{cartCount}</span>
              </button>
            )}
          </div>
        )}
      </div>

      {!searchOpen && (
        <div className="cc-mobile-bar__meta">
          {resultCount} позицій
          {query.trim() ? " · пошук" : ""}
        </div>
      )}

      {drawerOpen &&
        createPortal(
          <div className="cc-cat-drawer" role="dialog" aria-modal="true" aria-label="Категорії каталогу">
            <button type="button" className="cc-cat-drawer__backdrop" onClick={() => setDrawerOpen(false)} aria-label="Закрити" />
            <aside className="cc-cat-drawer__panel">
              <div className="cc-cat-drawer__head">
                <span className="font-bold text-[15px] text-[#1A1A2E]">Категорії</span>
                <button type="button" className="cc-icon-btn" onClick={() => setDrawerOpen(false)} aria-label="Закрити">
                  <CloseIcon />
                </button>
              </div>

              <div className="cc-cat-drawer__body cc-scroll">
                <CategoryNavList nodes={nodes} selectionId={selectionId} onSelect={pickCategory} compact />
              </div>

              <div className="cc-cat-drawer__foot">
                <div className="text-[10px] font-bold uppercase tracking-wider text-[#1A1A2E]/40 mb-2 px-1">Фільтри</div>
                <div className="flex flex-wrap gap-1.5">
                  {FILTER_LABELS.map(({ key, label }) => (
                    <button
                      key={key}
                      type="button"
                      onClick={() => onToggleFilter(key)}
                      className={`cc-filter-chip ${filters[key] ? "cc-filter-chip--active" : ""}`}
                    >
                      {label}
                    </button>
                  ))}
                </div>
              </div>
            </aside>
          </div>,
          document.body,
        )}
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
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" aria-hidden>
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
