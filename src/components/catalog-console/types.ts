import type { CatalogCategory, CatalogItem } from "@/data/catalog";
import type { IconName } from "@/storage/icons";

export type CatalogItemExt = CatalogItem & {
  serviceId?: number;
  categoryHref?: string;
  serviceHref?: string;
  marker?: string | null;
};

export type CatalogCategoryExt = Omit<CatalogCategory, "icon"> & {
  icon?: IconName;
  iconUrl?: string | null;
  items: CatalogItemExt[];
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
