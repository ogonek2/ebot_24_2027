import type { SpaCatalogCategory } from "@/lib/bootstrap";
import { categoryUrl, ROUTES } from "@/lib/routes";
import { homeCrumb, type BreadcrumbItem } from "@/components/Breadcrumbs";

export function findCategory(
  categories: SpaCatalogCategory[] | undefined,
  id: string,
): SpaCatalogCategory | undefined {
  return categories?.find((c) => c.id === id);
}

export function topLevelCategories(categories: SpaCatalogCategory[] | undefined): SpaCatalogCategory[] {
  return (categories ?? []).filter((c) => !c.parentId);
}

/** Subcategories of a parent category slug. */
export function childCategories(
  categories: SpaCatalogCategory[] | undefined,
  parentId: string,
): SpaCatalogCategory[] {
  return (categories ?? []).filter((c) => c.parentId === parentId);
}

export function categoryGroupId(category: SpaCatalogCategory | undefined, fallbackId: string): string {
  return category?.parentId ?? category?.id ?? fallbackId;
}
export function buildCategoryBreadcrumbItems(
  categories: SpaCatalogCategory[] | undefined,
  categoryId: string,
  options?: { currentIsLink?: boolean },
): BreadcrumbItem[] {
  const cat = findCategory(categories, categoryId);
  const items: BreadcrumbItem[] = [
    homeCrumb(),
    { name: "Послуги та ціни", url: ROUTES.services },
  ];

  if (cat?.parentId) {
    const parent = findCategory(categories, cat.parentId);
    if (parent) {
      items.push({ name: parent.title, url: categoryUrl(parent.id) });
    }
  }

  if (cat) {
    items.push(
      options?.currentIsLink
        ? { name: cat.title, url: categoryUrl(cat.id) }
        : { name: cat.title },
    );
  }

  return items;
}

export function buildServiceBreadcrumbItems(
  categories: SpaCatalogCategory[] | undefined,
  service: {
    name: string;
    categoryHref?: string | null;
    categoryTitle?: string | null;
    parentCategoryHref?: string | null;
    parentCategoryTitle?: string | null;
  },
  fallbackCategoryHref: string,
): BreadcrumbItem[] {
  const categoryHref = service.categoryHref ?? fallbackCategoryHref;
  const cat = findCategory(categories, categoryHref);

  const parentHref = service.parentCategoryHref ?? cat?.parentId ?? null;
  const parentTitle =
    service.parentCategoryTitle ??
    (parentHref ? findCategory(categories, parentHref)?.title : undefined) ??
    null;

  const items: BreadcrumbItem[] = [
    homeCrumb(),
    { name: "Послуги та ціни", url: ROUTES.services },
  ];

  if (parentHref && parentTitle) {
    items.push({ name: parentTitle, url: categoryUrl(parentHref) });
  }

  items.push({
    name: service.categoryTitle ?? cat?.title ?? "Послуги",
    url: categoryUrl(categoryHref),
  });

  items.push({ name: service.name });

  return items;
}
