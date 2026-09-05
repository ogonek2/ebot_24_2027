import { useState } from "react";
import { Link } from "react-router-dom";
import { serviceUrl } from "@/lib/routes";
import { resolveCatalogCleaningDisplay } from "@/lib/cartPrices";
import {
  formatPriceCompact,
  inspectorBullets,
  resolveItemPricing,
} from "./utils";
import type { SelectedRow } from "./types";

type Props = {
  selected: SelectedRow | null;
  collapsed: boolean;
  onToggleCollapse: () => void;
  onAdd: (selected: SelectedRow, qty: number) => void;
  inCart: boolean;
};

export default function InspectorPanel({ selected, collapsed, onToggleCollapse, onAdd, inCart }: Props) {
  const [qty, setQty] = useState(1);

  if (collapsed) {
    return (
      <div className="cc-inspector cc-inspector--collapsed hidden lg:flex flex-col items-center py-3 px-1 border-l border-white/30">
        <button type="button" onClick={onToggleCollapse} className="cc-icon-btn" title="Розгорнути інспектор">
          <ChevronIcon dir="left" />
        </button>
      </div>
    );
  }

  return (
    <aside className="cc-inspector hidden lg:flex flex-col">
      <div className="flex items-center justify-between px-4 py-3 border-b border-white/20 shrink-0">
        <span className="text-[11px] font-bold uppercase tracking-[0.1em] text-[#1A1A2E]/45">Деталі</span>
        <button type="button" onClick={onToggleCollapse} className="cc-icon-btn" title="Згорнути">
          <ChevronIcon dir="right" />
        </button>
      </div>

      {!selected ? (
        <div className="flex-1 flex items-center justify-center p-6 text-center text-[14px] text-[#1A1A2E]/45 leading-relaxed">
          Оберіть послугу зі списку
        </div>
      ) : (
        <InspectorBody selected={selected} qty={qty} setQty={setQty} onAdd={onAdd} inCart={inCart} />
      )}
    </aside>
  );
}

export function MobileInspectorSheet({
  open,
  selected,
  onClose,
  onAdd,
  inCart,
}: {
  open: boolean;
  selected: SelectedRow | null;
  onClose: () => void;
  onAdd: (selected: SelectedRow, qty: number) => void;
  inCart: boolean;
}) {
  const [qty, setQty] = useState(1);
  if (!open || !selected) return null;

  return (
    <div className="lg:hidden fixed inset-0 z-[70]">
      <button type="button" className="absolute inset-0 bg-[#1A1A2E]/25 backdrop-blur-sm" onClick={onClose} aria-label="Закрити" />
      <div className="absolute inset-x-0 bottom-0 cc-sheet anim-slide-up max-h-[78vh] overflow-hidden flex flex-col">
        <div className="flex items-center justify-between px-4 py-3 border-b border-white/30">
          <span className="font-bold text-[15px]">{selected.item.name}</span>
          <button type="button" onClick={onClose} className="cc-icon-btn">✕</button>
        </div>
        <InspectorBody selected={selected} qty={qty} setQty={setQty} onAdd={onAdd} inCart={inCart} compact />
      </div>
    </div>
  );
}

function InspectorBody({
  selected,
  qty,
  setQty,
  onAdd,
  inCart,
  compact,
}: {
  selected: SelectedRow;
  qty: number;
  setQty: (n: number) => void;
  onAdd: (selected: SelectedRow, qty: number) => void;
  inCart: boolean;
  compact?: boolean;
}) {
  const { item, category, subgroup } = selected;
  const pricing = resolveItemPricing(item);
  const cleaning = resolveCatalogCleaningDisplay(item);
  const onRequest = cleaning.isOnRequest;
  const bullets = inspectorBullets(item, subgroup.title);
  const priceTileCount = Number(cleaning.hasIndividual) + Number(cleaning.hasStream);

  return (
    <div className={`flex flex-col ${compact ? "p-4" : ""}`}>
      <div className={`${compact ? "" : "p-4 space-y-4"}`}>
      <div>
        <div className="text-[11px] text-[#1A1A2E]/45 mb-1">
          {category.title} / {subgroup.title}
        </div>
        <h3 className="font-bold text-[20px] text-[#1A1A2E] leading-tight">{item.name}</h3>
      </div>

      {onRequest && (
        <div className="cc-info-banner text-[12px] leading-relaxed">
          Ціна залежить від матеріалів та стану речі — уточнюємо після огляду.
        </div>
      )}

      {priceTileCount > 0 && (
        <div className={`grid gap-2 ${priceTileCount > 1 ? "grid-cols-2" : "grid-cols-1"}`}>
          {cleaning.hasIndividual && (
            <div className="cc-price-tile">
              <div className="text-[10px] font-bold uppercase tracking-wide text-[#1A1A2E]/40 mb-1">
                Індивідуальна
              </div>
              <div className="text-[18px] font-black tabular-nums text-[var(--cc-accent)]">
                {formatPriceCompact(cleaning.individualRaw!)}
              </div>
            </div>
          )}
          {cleaning.hasStream && (
            <div className="cc-price-tile">
              <div className="text-[10px] font-bold uppercase tracking-wide text-[#1A1A2E]/40 mb-1">
                Потокова
              </div>
              <div className="text-[18px] font-black tabular-nums text-[#1A1A2E]">
                {formatPriceCompact(cleaning.streamRaw!)}
              </div>
            </div>
          )}
        </div>
      )}

      <ul className="space-y-1.5 text-[13px] text-[#1A1A2E]/65">
        {bullets.map((b) => (
          <li key={b} className="flex gap-2">
            <span className="text-[var(--cc-accent)]">·</span>
            <span>{b}</span>
          </li>
        ))}
      </ul>

      <div className="flex flex-wrap gap-1.5">
        {pricing.promo && <span className="cc-tag cc-tag--accent">Акція</span>}
        {item.marker && <span className="cc-tag">{item.marker}</span>}
      </div>

      {item.serviceHref && item.categoryHref && (
        <Link
          to={serviceUrl(item.categoryHref, item.serviceHref)}
          className="block text-center text-[12px] text-[#1A1A2E]/45 hover:text-[var(--cc-accent)] no-underline"
        >
          Детальніше про послугу →
        </Link>
      )}
      </div>

      <div className={`border-t border-white/25 bg-white/15 ${compact ? "p-4 mt-4" : "p-4 mt-4 -mx-0"}`}>
        <div className="flex items-center gap-3">
          <div className="cc-stepper">
            <button type="button" onClick={() => setQty(Math.max(1, qty - 1))} className="cc-stepper-btn">−</button>
            <span className="cc-mono-count text-[14px] font-bold w-6 text-center">{qty}</span>
            <button type="button" onClick={() => setQty(qty + 1)} className="cc-stepper-btn">+</button>
          </div>
          <button type="button" onClick={() => onAdd(selected, qty)} className="cc-cta flex-1">
            {inCart ? "Додано ✓" : "Додати"}
          </button>
        </div>
      </div>
    </div>
  );
}

function ChevronIcon({ dir }: { dir: "left" | "right" }) {
  return (
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2">
      {dir === "left" ? (
        <path d="M15 6l-6 6 6 6" strokeLinecap="round" strokeLinejoin="round" />
      ) : (
        <path d="M9 6l6 6-6 6" strokeLinecap="round" strokeLinejoin="round" />
      )}
    </svg>
  );
}
