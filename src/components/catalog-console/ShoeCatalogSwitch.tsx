import { Link } from "react-router-dom";
import type { SpaCatalogCategory, SpaRepairPriceList } from "@/lib/bootstrap";
import { categoryUrl } from "@/lib/routes";

export function isShoeLikeCategory(id: string, title: string): boolean {
  const hay = `${id} ${title}`.toLowerCase();
  return /взут|vzut|shoe|remont/.test(hay);
}

export function countRepairItems(list: SpaRepairPriceList | null | undefined): number {
  if (!list) return 0;
  return list.sections.reduce((sum, s) => sum + s.items.length, 0);
}

export function resolveShoeCatalogModes(categories: SpaCatalogCategory[] | undefined) {
  const all = categories ?? [];
  const repair =
    all.find((c) => Boolean(c.repairPriceList)) ??
    all.find((c) => isShoeLikeCategory(c.id, c.title) && /ремонт|remont/.test(`${c.id} ${c.title}`.toLowerCase()));

  const clean =
    all.find(
      (c) =>
        c.id !== repair?.id &&
        isShoeLikeCategory(c.id, c.title) &&
        !c.repairPriceList &&
        (c.items?.length ?? 0) > 0,
    ) ??
    all.find(
      (c) =>
        c.id !== repair?.id &&
        isShoeLikeCategory(c.id, c.title) &&
        !/ремонт|remont/.test(`${c.id} ${c.title}`.toLowerCase()),
    );

  return { repair: repair ?? null, clean: clean ?? null };
}

type Props = {
  categories: SpaCatalogCategory[] | undefined;
  activeId: string;
  /** In-catalog selection without full navigation */
  onSelectId?: (id: string) => void;
  className?: string;
};

export default function ShoeCatalogSwitch({ categories, activeId, onSelectId, className = "" }: Props) {
  const { repair, clean } = resolveShoeCatalogModes(categories);
  if (!repair && !clean) return null;

  const modes = [
    clean ? { id: clean.id, label: "Хімчистка взуття" } : null,
    repair ? { id: repair.id, label: "Ремонт взуття" } : null,
  ].filter(Boolean) as Array<{ id: string; label: string }>;

  if (modes.length < 2) return null;

  const isActive = (id: string) => {
    if (activeId === id) return true;
    // Subgroup ids like `foo--repair`
    if (activeId.startsWith(`${id}--`)) return true;
    return false;
  };

  const tabClass = (active: boolean) =>
    `shoe-switch__tab ${active ? "shoe-switch__tab--active" : ""}`;

  return (
    <nav className={`shoe-switch ${className}`} aria-label="Режим каталогу взуття">
      {modes.map((mode) => {
        const isRepair = Boolean(repair && mode.id === repair.id);
        // Repair always opens its dedicated page — never the shared catalog table.
        if (onSelectId && !isRepair) {
          return (
            <button
              key={mode.id}
              type="button"
              className={tabClass(isActive(mode.id))}
              onClick={() => onSelectId(mode.id)}
              aria-pressed={isActive(mode.id)}
            >
              {mode.label}
            </button>
          );
        }

        return (
          <Link
            key={mode.id}
            to={categoryUrl(mode.id)}
            className={`${tabClass(isActive(mode.id))} no-underline`}
            aria-current={isActive(mode.id) ? "page" : undefined}
          >
            {mode.label}
          </Link>
        );
      })}
    </nav>
  );
}
