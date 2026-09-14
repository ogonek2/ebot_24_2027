import { useEffect, useState } from "react";
import CategoryIcon from "../CategoryIcon";
import type { CatalogNode } from "./types";

type Props = {
  nodes: CatalogNode[];
  selectionId: string;
  onSelect: (id: string) => void;
  compact?: boolean;
};

export default function CategoryNavList({ nodes, selectionId, onSelect, compact = false }: Props) {
  const activeParentId = resolveActiveParentId(nodes, selectionId);
  const [expandedId, setExpandedId] = useState<string | null>(activeParentId);

  useEffect(() => {
    if (activeParentId) setExpandedId(activeParentId);
  }, [activeParentId]);

  return (
    <nav className={`cc-rail-nav ${compact ? "cc-rail-nav--compact" : "cc-scroll"}`} aria-label="Категорії">
      {nodes.map((node) => {
        const hasSubgroups = node.subgroups.length > 1;
        const expanded = hasSubgroups && expandedId === node.id;
        const isCategoryActive = selectionId === node.id;
        const activeSubgroup = node.subgroups.find((sg) => sg.id === selectionId);
        const selected = isCategoryActive || Boolean(activeSubgroup);

        return (
          <div
            key={node.id}
            className={`cc-rail-block ${selected ? "is-selected" : ""} ${expanded ? "is-expanded" : ""}`}
          >
            <div className="cc-rail-row">
              <button
                type="button"
                onClick={() => onSelect(node.id)}
                className={`cc-rail-item ${isCategoryActive && !activeSubgroup ? "cc-rail-item--active" : ""} ${selected ? "is-current" : ""}`}
              >
                <span className="cc-rail-item__icon">
                  <CategoryIcon src={node.iconUrl} size={compact ? 16 : 18} alt="" />
                </span>
                <span className="cc-rail-item__text">
                  <span className="cc-rail-item__title">{node.title}</span>
                </span>
                <span className="cc-mono-count cc-rail-item__count">{node.items.length}</span>
              </button>

              {hasSubgroups && (
                <button
                  type="button"
                  className={`cc-rail-expand ${expanded ? "is-open" : ""}`}
                  aria-expanded={expanded}
                  aria-label={expanded ? "Згорнути підкатегорії" : "Показати підкатегорії"}
                  onClick={() => setExpandedId(expanded ? null : node.id)}
                >
                  <span aria-hidden>{expanded ? "−" : "+"}</span>
                </button>
              )}
            </div>

            {expanded && (
              <div className="cc-rail-subs" role="group" aria-label={node.title}>
                {node.subgroups.map((sg) => (
                  <button
                    key={sg.id}
                    type="button"
                    onClick={() => onSelect(sg.id)}
                    className={`cc-rail-subitem ${activeSubgroup?.id === sg.id ? "cc-rail-subitem--active" : ""}`}
                  >
                    <span className="cc-rail-subitem__title">{sg.title}</span>
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

function resolveActiveParentId(nodes: CatalogNode[], selectionId: string): string | null {
  for (const node of nodes) {
    if (node.id === selectionId) return node.subgroups.length > 1 ? node.id : null;
    if (node.subgroups.some((sg) => sg.id === selectionId)) return node.id;
  }
  return null;
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
