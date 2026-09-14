import { useEffect, useId, useMemo, useState } from "react";
import { createPortal } from "react-dom";
import { Link } from "react-router-dom";
import CategoryIcon from "../CategoryIcon";
import { serviceUrl } from "@/lib/routes";
import type { CatalogNode } from "./types";

type Props = {
  open: boolean;
  onClose: () => void;
  nodes: CatalogNode[];
  selectionId: string;
  onSelectCategory: (id: string) => void;
};

export default function CategoryPickerModal({
  open,
  onClose,
  nodes,
  selectionId,
  onSelectCategory,
}: Props) {
  const titleId = useId();
  const initialOpenId = useMemo(() => {
    for (const node of nodes) {
      if (node.id === selectionId) return node.id;
      if (node.subgroups.some((sg) => sg.id === selectionId)) return node.id;
    }
    return nodes[0]?.id ?? null;
  }, [nodes, selectionId]);

  const [expandedId, setExpandedId] = useState<string | null>(initialOpenId);

  useEffect(() => {
    if (open) setExpandedId(initialOpenId);
  }, [open, initialOpenId]);

  useEffect(() => {
    if (!open) return;
    const prev = document.body.style.overflow;
    document.body.style.overflow = "hidden";
    const onKey = (e: KeyboardEvent) => {
      if (e.key === "Escape") onClose();
    };
    window.addEventListener("keydown", onKey);
    return () => {
      document.body.style.overflow = prev;
      window.removeEventListener("keydown", onKey);
    };
  }, [open, onClose]);

  if (!open) return null;

  const pick = (id: string) => {
    onSelectCategory(id);
    onClose();
  };

  return createPortal(
    <div className="cc-cat-modal" role="presentation" onClick={onClose}>
      <div
        className="cc-cat-modal__dialog glass-strong"
        role="dialog"
        aria-modal="true"
        aria-labelledby={titleId}
        onClick={(e) => e.stopPropagation()}
      >
        <header className="cc-cat-modal__head">
          <div>
            <p className="cc-cat-modal__eyebrow">Каталог</p>
            <h2 id={titleId} className="cc-cat-modal__title">
              Категорії та послуги
            </h2>
          </div>
          <button type="button" className="cc-cat-modal__close" onClick={onClose} aria-label="Закрити">
            ×
          </button>
        </header>

        <div className="cc-cat-modal__body cc-scroll">
          {nodes.length === 0 ? (
            <p className="cc-cat-modal__empty">Категорії завантажуються…</p>
          ) : (
            <ul className="cc-cat-modal__list">
              {nodes.map((node) => {
                const expanded = expandedId === node.id;
                const selected =
                  selectionId === node.id || node.subgroups.some((sg) => sg.id === selectionId);
                const hasSubs = node.subgroups.length > 1;
                const services = hasSubs
                  ? node.subgroups.flatMap((sg) =>
                      sg.items.map((item) => ({ item, subgroupId: sg.id, subgroupTitle: sg.title })),
                    )
                  : node.items.map((item) => ({
                      item,
                      subgroupId: node.subgroups[0]?.id ?? node.id,
                      subgroupTitle: node.title,
                    }));

                return (
                  <li key={node.id} className={`cc-cat-modal__item ${selected ? "is-selected" : ""}`}>
                    <div className="cc-cat-modal__row">
                      <button
                        type="button"
                        className="cc-cat-modal__main"
                        onClick={() => pick(node.id)}
                      >
                        <span className="cc-cat-modal__icon">
                          <CategoryIcon src={node.iconUrl} size={28} alt="" />
                        </span>
                        <span className="cc-cat-modal__meta">
                          <span className="cc-cat-modal__name">{node.title}</span>
                          <span className="cc-cat-modal__count">{node.items.length} послуг</span>
                        </span>
                      </button>

                      <button
                        type="button"
                        className={`cc-cat-modal__toggle ${expanded ? "is-open" : ""}`}
                        aria-expanded={expanded}
                        aria-label={expanded ? "Згорнути послуги" : "Показати послуги"}
                        onClick={() => setExpandedId(expanded ? null : node.id)}
                      >
                        <i className="fa-solid fa-chevron-down" aria-hidden />
                      </button>
                    </div>

                    {expanded && (
                      <div className="cc-cat-modal__panel">
                        {hasSubs && (
                          <div className="cc-cat-modal__subs">
                            {node.subgroups.map((sg) => (
                              <button
                                key={sg.id}
                                type="button"
                                className={`cc-cat-modal__sub ${selectionId === sg.id ? "is-active" : ""}`}
                                onClick={() => pick(sg.id)}
                              >
                                <span>{sg.title}</span>
                                <span className="cc-cat-modal__sub-count">{sg.items.length}</span>
                              </button>
                            ))}
                          </div>
                        )}

                        {services.length > 0 ? (
                          <ul className="cc-cat-modal__services">
                            {services.map(({ item, subgroupId, subgroupTitle }) => {
                              const href =
                                item.serviceHref && item.categoryHref
                                  ? serviceUrl(item.categoryHref, item.serviceHref)
                                  : null;
                              const content = (
                                <>
                                  <span className="cc-cat-modal__svc-name">{item.name}</span>
                                  {hasSubs && (
                                    <span className="cc-cat-modal__svc-sub">{subgroupTitle}</span>
                                  )}
                                  {item.price && (
                                    <span className="cc-cat-modal__svc-price">{item.price}</span>
                                  )}
                                </>
                              );

                              return (
                                <li key={`${subgroupId}:${item.name}:${item.serviceId ?? ""}`}>
                                  {href ? (
                                    <Link to={href} className="cc-cat-modal__svc" onClick={onClose}>
                                      {content}
                                    </Link>
                                  ) : (
                                    <button
                                      type="button"
                                      className="cc-cat-modal__svc"
                                      onClick={() => pick(subgroupId)}
                                    >
                                      {content}
                                    </button>
                                  )}
                                </li>
                              );
                            })}
                          </ul>
                        ) : (
                          <p className="cc-cat-modal__empty">У цій категорії ще немає послуг</p>
                        )}
                      </div>
                    )}
                  </li>
                );
              })}
            </ul>
          )}
        </div>
      </div>
    </div>,
    document.body,
  );
}
