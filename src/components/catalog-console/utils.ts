import { inferOldPrice, resolveItemPricing, type CatalogItem } from "@/data/catalog";
import { resolveCatalogCleaningDisplay } from "@/lib/cartPrices";
import type {
  CatalogCategoryExt,
  CatalogFilters,
  CatalogItemExt,
  CatalogNode,
  CatalogSubGroup,
  FlatRow,
} from "./types";

const SHOE_LABELS = ["Хімчистка взуття", "Ремонт взуття"];

function isShoeCategory(categoryId: string, categoryTitle: string): boolean {
  const hay = `${categoryId} ${categoryTitle}`.toLowerCase();
  return hay.includes("взут") || hay.includes("vzut") || hay.includes("shoe");
}

function itemPriceForGrouping(item: CatalogItemExt): string {
  return item.price ?? "";
}

export function isOnRequest(price: string): boolean {
  return !price || price.toLowerCase().includes("запитом");
}

export function formatPriceCompact(price: string): string {
  if (isOnRequest(price)) return "—";
  return price.replace(/₴/g, "").trim();
}

export function inferTermDays(categoryId: string, subgroupTitle: string, onRequest: boolean): number {
  if (subgroupTitle.toLowerCase().includes("ремонт")) return 5;
  if (categoryId.includes("взут")) return onRequest ? 5 : 2;
  if (categoryId.includes("верхн")) return 3;
  if (categoryId.includes("текстил")) return 3;
  return 3;
}

export function formatTermLabel(days: number): string {
  if (days <= 1) return "24 год";
  if (days <= 2) return "48 год";
  return `${days} дн`;
}

export function inferSubgroups(categoryId: string, categoryTitle: string, items: CatalogItemExt[]): CatalogSubGroup[] {
  if (items.length === 0) {
    return [{ id: `${categoryId}--all`, title: categoryTitle, items: [] }];
  }

  const priced = items.filter((i) => !isOnRequest(itemPriceForGrouping(i)));
  const onRequest = items.filter((i) => isOnRequest(itemPriceForGrouping(i)));

  if (isShoeCategory(categoryId, categoryTitle) && priced.length > 0 && onRequest.length > 0) {
    return [
      { id: `${categoryId}--clean`, title: SHOE_LABELS[0], items: priced },
      { id: `${categoryId}--repair`, title: SHOE_LABELS[1], items: onRequest },
    ];
  }

  if (priced.length >= 2 && onRequest.length >= 2) {
    return [
      { id: `${categoryId}--priced`, title: "З фіксованою ціною", items: priced },
      { id: `${categoryId}--request`, title: "Індивідуальний розрахунок", items: onRequest },
    ];
  }

  return [{ id: `${categoryId}--all`, title: categoryTitle, items }];
}

export function buildCatalogNodes(categories: CatalogCategoryExt[]): CatalogNode[] {
  return categories.map((cat) => ({
    id: cat.id,
    title: cat.title,
    iconUrl: cat.iconUrl,
    items: cat.items,
    subgroups: inferSubgroups(cat.id, cat.title, cat.items),
  }));
}

export function findNode(nodes: CatalogNode[], nodeId: string): { category: CatalogNode; subgroup?: CatalogSubGroup } | null {
  for (const category of nodes) {
    if (category.id === nodeId) return { category };
    const subgroup = category.subgroups.find((sg) => sg.id === nodeId);
    if (subgroup) return { category, subgroup };
  }
  return null;
}

export function itemMatchesFilters(
  item: CatalogItemExt,
  categoryId: string,
  subgroupTitle: string,
  filters: CatalogFilters,
): boolean {
  const pricing = resolveItemPricing(item);
  const cleaning = resolveCatalogCleaningDisplay(item);
  const onRequest = cleaning.isOnRequest;
  const termDays = inferTermDays(categoryId, subgroupTitle, onRequest);

  if (filters.hasPrice && onRequest) return false;
  if (filters.onRequest && !onRequest) return false;
  if (filters.promo && !pricing.promo) return false;
  if (filters.fastTerm && termDays > 1) return false;
  return true;
}

export function itemMatchesQuery(item: CatalogItemExt, query: string): boolean {
  const q = query.trim().toLowerCase();
  if (!q) return true;
  return (
    item.name.toLowerCase().includes(q) ||
    item.price.toLowerCase().includes(q) ||
    (item.priceBatch?.toLowerCase().includes(q) ?? false) ||
    (item.individualPrice?.toLowerCase().includes(q) ?? false) ||
    (item.marker?.toLowerCase().includes(q) ?? false)
  );
}

export function shouldShowSubgroupHeaders(category: CatalogNode, subgroups: CatalogSubGroup[]): boolean {
  if (subgroups.length <= 1) return false;
  return subgroups.some((sg) => sg.title !== category.title);
}

export function buildFlatRows(
  nodes: CatalogNode[],
  selectionId: string,
  query: string,
  filters: CatalogFilters,
  globalSearch: boolean,
): FlatRow[] {
  const rows: FlatRow[] = [];
  const q = query.trim();

  const categoriesToScan = globalSearch
    ? nodes
    : (() => {
        const found = findNode(nodes, selectionId);
        return found ? [found.category] : nodes.slice(0, 1);
      })();

  for (const category of categoriesToScan) {
    const selection = findNode(nodes, selectionId);
    const subgroups =
      !globalSearch && selection?.subgroup
        ? [selection.subgroup]
        : category.subgroups;

    const showHeaders = shouldShowSubgroupHeaders(category, subgroups);

    for (const subgroup of subgroups) {
      let headerPending = true;
      for (const item of subgroup.items) {
        if (!itemMatchesQuery(item, q)) continue;
        if (!itemMatchesFilters(item, category.id, subgroup.title, filters)) continue;

        rows.push({
          key: `${category.id}:${subgroup.id}:${item.serviceId ?? item.name}:${item.price}`,
          item,
          category,
          subgroup,
          subgroupTitle: subgroup.title,
          showSubgroupHeader: headerPending && (globalSearch ? showHeaders : showHeaders),
        });
        headerPending = false;
      }
    }
  }

  return rows;
}

export function inspectorBullets(item: CatalogItemExt, subgroupTitle: string): string[] {
  const onRequest = isOnRequest(item.price);
  if (subgroupTitle.toLowerCase().includes("ремонт")) {
    return ["Огляд та оцінка", "Підбір матеріалів", "Виконання ремонту", "Контроль якості"];
  }
  if (onRequest) {
    return ["Індивідуальна оцінка", "Підбір технології", "Погодження термінів", "Професійна обробка"];
  }
  return ["Попередня обробка", "Гіпоалергенна чистка", "Контроль якості", "Захисна упаковка"];
}

export { inferOldPrice, resolveItemPricing };
export type { CatalogItem };
