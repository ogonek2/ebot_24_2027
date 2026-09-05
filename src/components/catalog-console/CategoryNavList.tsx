import CategoryIcon from "../CategoryIcon";
import type { CatalogNode } from "./types";

type Props = {
  nodes: CatalogNode[];
  selectionId: string;
  onSelect: (id: string) => void;
  compact?: boolean;
};

export default function CategoryNavList({ nodes, selectionId, onSelect, compact = false }: Props) {
  return (
    <nav className={compact ? "py-1" : "py-1 cc-scroll"}>
      {nodes.map((node) => {
        const isCategoryActive = selectionId === node.id;
        const activeSubgroup = node.subgroups.find((sg) => sg.id === selectionId);
        const hasSubgroups = node.subgroups.length > 1;

        return (
          <div key={node.id} className="px-1.5">
            <button
              type="button"
              onClick={() => onSelect(node.id)}
              className={`cc-rail-item w-full ${isCategoryActive && !activeSubgroup ? "cc-rail-item--active" : ""}`}
            >
              <CategoryIcon src={node.iconUrl} size={16} alt={node.title} />
              <span className="flex-1 text-left truncate">{node.title}</span>
              <span className="cc-mono-count">{node.items.length}</span>
            </button>

            {hasSubgroups && (
              <div className="ml-3 mb-1 border-l border-white/25 pl-2">
                {node.subgroups.map((sg) => (
                  <button
                    key={sg.id}
                    type="button"
                    onClick={() => onSelect(sg.id)}
                    className={`cc-rail-subitem w-full ${activeSubgroup?.id === sg.id ? "cc-rail-subitem--active" : ""}`}
                  >
                    <span className="truncate">{sg.title}</span>
                    <span className="cc-mono-count">{sg.items.length}</span>
                  </button>
                ))}
              </div>
            )}
          </div>
        );
      })}
    </nav>
  );
}

export function buildCategoryOptions(nodes: CatalogNode[]) {
  return nodes.flatMap((node) => {
    const opts = [{ id: node.id, label: node.title }];
    if (node.subgroups.length > 1) {
      node.subgroups.forEach((sg) => {
        opts.push({ id: sg.id, label: `${node.title} · ${sg.title}` });
      });
    }
    return opts;
  });
}

export function resolveSelectionLabel(nodes: CatalogNode[], selectionId: string): string {
  for (const node of nodes) {
    if (node.id === selectionId) return node.title;
    const sg = node.subgroups.find((s) => s.id === selectionId);
    if (sg) return node.subgroups.length > 1 ? `${node.title} · ${sg.title}` : node.title;
  }
  return "Категорія";
}
