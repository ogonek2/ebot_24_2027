import type { CatalogCategory, CatalogItem } from "@/data/catalog";
import type { IconName } from "@/storage/icons";

export type CatalogItemExt = CatalogItem & {
  serviceId?: number;
  categoryHref?: string;
  serviceHref?: string;
  marker?: string | null;
  seoDescription?: string | null;
  oldPrice?: string | null;
  individualOldPrice?: string | null;
  discountPercent?: number | null;
  individualDiscountPercent?: number | null;
};

export type CatalogCategoryExt = Omit<CatalogCategory, "icon"> & {
  icon?: IconName;
  iconUrl?: string | null;
  items: CatalogItemExt[];
  repairPriceList?: import("@/lib/bootstrap").SpaRepairPriceList | null;
};

export type CatalogSubGroup = {
  id: string;
  title: string;
  items: CatalogItemExt[];
};

export type CatalogNode = {
  id: string;
  title: string;
  icon?: IconName;
  iconUrl?: string | null;
  items: CatalogItemExt[];
  subgroups: CatalogSubGroup[];
  repairPriceList?: import("@/lib/bootstrap").SpaRepairPriceList | null;
};

export type FlatRow = {
  key: string;
  item: CatalogItemExt;
  category: CatalogNode;
  subgroup: CatalogSubGroup;
  subgroupTitle: string;
  showSubgroupHeader: boolean;
};

export type DensityMode = "compact" | "comfortable";

export type CatalogFilters = {
  hasPrice: boolean;
  onRequest: boolean;
  promo: boolean;
  fastTerm: boolean;
};

export type SelectedRow = {
  item: CatalogItemExt;
  category: CatalogNode;
  subgroup: CatalogSubGroup;
};

export type CartLine = {
  id: string;
  name: string;
  mode: "individual" | "batch";
  price: string;
  categoryId: string;
};
