import CategoryNavList from "./CategoryNavList";
import type { CatalogNode } from "./types";

type Props = {
  nodes: CatalogNode[];
  selectionId: string;
  onSelect: (id: string) => void;
  collapsed: boolean;
  onToggleCollapse: () => void;
};

export default function CategoryRail({ nodes, selectionId, onSelect, collapsed, onToggleCollapse }: Props) {
  if (collapsed) {
    return (
      <div className="cc-rail cc-rail--collapsed hidden md:flex flex-col items-center py-3 px-1 border-r border-white/30">
        <button type="button" onClick={onToggleCollapse} className="cc-icon-btn" title="Розгорнути навігатор">
          <ChevronIcon dir="right" />
        </button>
      </div>
    );
  }

  return (
    <aside className="cc-rail hidden md:flex flex-col">
      <div className="flex items-center justify-between px-3 py-2.5 border-b border-white/25">
        <span className="text-[10px] font-bold uppercase tracking-[0.12em] text-[#1A1A2E]/40">Категорії</span>
        <button type="button" onClick={onToggleCollapse} className="cc-icon-btn" title="Згорнути">
          <ChevronIcon dir="left" />
        </button>
      </div>
      <CategoryNavList nodes={nodes} selectionId={selectionId} onSelect={onSelect} />
    </aside>
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
