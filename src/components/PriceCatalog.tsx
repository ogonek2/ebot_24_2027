import type { CatalogItem, CatalogCategory } from "@/data/catalog";
import CatalogConsole, { type CatalogConsoleProps } from "./catalog-console/CatalogConsole";
import type { CartLine } from "./catalog-console/types";

export type { CartLine };

interface PriceCatalogProps extends CatalogConsoleProps {
  onOpenFull?: () => void;
  onOpenItem?: (item: CatalogItem, category: CatalogCategory) => void;
}

/** @deprecated use CatalogConsole — kept for existing imports */
export default function PriceCatalog(props: PriceCatalogProps) {
  const { onOpenFull: _onOpenFull, onOpenItem: _onOpenItem, ...rest } = props;
  return <CatalogConsole {...rest} />;
}
