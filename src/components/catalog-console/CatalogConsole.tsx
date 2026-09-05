import { useCallback, useEffect, useMemo, useRef, useState } from "react";
import { Link } from "react-router-dom";
import { catalog as fallbackCatalog } from "@/data/catalog";
import { useBootstrap } from "@/context/BootstrapContext";
import { useCart } from "@/context/CartContext";
import { findCategory, topLevelCategories } from "@/lib/categories";
import { parseUah, buildAddToCartTarget, resolveCatalogCleaningDisplay } from "@/lib/cartPrices";
import { ROUTES } from "@/lib/routes";
import CategoryRail from "./CategoryRail";
import ConsoleHeader from "./ConsoleHeader";
import DenseList, { cartKey } from "./DenseList";
import InspectorPanel, { MobileInspectorSheet } from "./InspectorPanel";
import MobileCatalogBar from "./MobileCatalogBar";
import type {
  CartLine,
  CatalogCategoryExt,
  CatalogFilters,
  DensityMode,
  FlatRow,
  SelectedRow,
} from "./types";
import { buildCatalogNodes, buildFlatRows, resolveItemPricing } from "./utils";

export type CatalogConsoleProps = {
  variant?: "section" | "page";
  onCheckout?: (cart: CartLine[]) => void;
  suppressHeading?: boolean;
};

const DEFAULT_FILTERS: CatalogFilters = {
  hasPrice: false,
  onRequest: false,
  promo: false,
  fastTerm: false,
};

export default function CatalogConsole({
  variant = "section",
  onCheckout,
  suppressHeading = false,
}: CatalogConsoleProps) {
  const bootstrap = useBootstrap();
  const { openAddModal } = useCart();
  const listRef = useRef<HTMLDivElement>(null);
  const catalog = useMemo((): CatalogCategoryExt[] => {
    const all = bootstrap.categories ?? [];
    const activeSub = bootstrap.activeCategory
      ? findCategory(all, bootstrap.activeCategory)
      : undefined;
    const source =
      activeSub?.parentId != null ? [activeSub] : topLevelCategories(all);

    if (source.length) {
      return source.map((c) => ({
        id: c.id,
        title: c.title,
        iconUrl: c.iconUrl,
        items: c.items.map((item) => ({
          name: item.name,
          price: item.price,
          priceBatch: item.priceBatch ?? item.price,
          individualPrice: item.individualPrice ?? null,
          oldPrice: item.oldPrice ?? undefined,
          promo: item.promo,
          marker: item.marker,
          serviceId: item.id,
          categoryHref: item.categoryHref,
          serviceHref: item.href,
        })),
      }));
    }
    return fallbackCatalog as CatalogCategoryExt[];
  }, [bootstrap.categories, bootstrap.activeCategory]);

  const nodes = useMemo(() => buildCatalogNodes(catalog), [catalog]);
  const [selectionId, setSelectionId] = useState(nodes[0]?.id ?? "");
  const [query, setQuery] = useState("");
  const [filters, setFilters] = useState<CatalogFilters>(DEFAULT_FILTERS);
  const [density, setDensity] = useState<DensityMode>("comfortable");
  const [railCollapsed, setRailCollapsed] = useState(false);
  const [inspectorCollapsed, setInspectorCollapsed] = useState(false);
  const [selectedRow, setSelectedRow] = useState<FlatRow | null>(null);
  const [mobileSheetOpen, setMobileSheetOpen] = useState(false);
  const [cart, setCart] = useState<CartLine[]>([]);
  const [toast, setToast] = useState<string | null>(null);

  useEffect(() => {
    if (nodes.length && !nodes.some((n) => n.id === selectionId || n.subgroups.some((sg) => sg.id === selectionId))) {
      setSelectionId(nodes[0].id);
    }
  }, [nodes, selectionId]);

  useEffect(() => {
    const active = bootstrap.activeCategory;
    if (!active || !nodes.length) return;
    const match = nodes.find((n) => n.id === active);
    if (match) setSelectionId(match.id);
  }, [bootstrap.activeCategory, nodes]);

  useEffect(() => {
    if (!toast) return;
    const t = setTimeout(() => setToast(null), 1600);
    return () => clearTimeout(t);
  }, [toast]);

  const globalSearch = query.trim().length > 0;
  const rows = useMemo(
    () => buildFlatRows(nodes, selectionId, query, filters, globalSearch),
    [nodes, selectionId, query, filters, globalSearch],
  );

  const inCartKeys = useMemo(() => new Set(cart.map((l) => l.id)), [cart]);

  const selected: SelectedRow | null = selectedRow
    ? { item: selectedRow.item, category: selectedRow.category, subgroup: selectedRow.subgroup }
    : null;

  const selectedInCart = selectedRow ? inCartKeys.has(cartKey(selectedRow)) : false;

  useEffect(() => {
    if (rows.length && !rows.some((r) => r.key === selectedRow?.key)) {
      setSelectedRow(rows[0] ?? null);
    }
  }, [rows, selectedRow?.key]);

  const scrollToListTop = useCallback(() => {
    const el = listRef.current;
    if (!el) return;
    const stickyTop = parseFloat(
      getComputedStyle(document.querySelector(".catalog-console") ?? document.documentElement).getPropertyValue("--cc-sticky-top"),
    ) || 72;
    const toolbar = document.querySelector(".cc-page-toolbar");
    const toolbarH = toolbar?.getBoundingClientRect().height ?? 52;
    const y = el.getBoundingClientRect().top + window.scrollY - stickyTop - toolbarH - 8;
    window.scrollTo({ top: Math.max(0, y), behavior: "smooth" });
  }, []);

  const selectCategory = useCallback(
    (id: string) => {
      setSelectionId(id);
      setQuery("");
      requestAnimationFrame(() => scrollToListTop());
    },
    [scrollToListTop],
  );

  const addRow = (row: FlatRow, qty = 1) => {
    const id = cartKey(row);

    if (row.item.serviceId) {
      const cleaning = resolveCatalogCleaningDisplay(row.item);
      const streamPrice = cleaning.hasStream && cleaning.streamRaw ? parseUah(cleaning.streamRaw) : 0;
      const individualPrice =
        cleaning.hasIndividual && cleaning.individualRaw ? parseUah(cleaning.individualRaw) : null;
      const target = buildAddToCartTarget({
        serviceId: row.item.serviceId,
        serviceName: row.item.name,
        streamPrice,
        individualPrice,
        initialQuantity: qty,
      });
      if (!target) {
        setToast("Ціна за запитом — зв'яжіться з нами");
        return;
      }
      openAddModal(target);
      return;
    }

    const pricing = resolveItemPricing(row.item);
    const price = pricing.individual ?? pricing.batch ?? row.item.price;
    setCart((prev) => {
      if (prev.some((l) => l.id === id)) return prev;
      return [...prev, { id, name: row.item.name, mode: "individual", price, categoryId: row.category.id }];
    });
    setToast(`Додано: ${row.item.name}${qty > 1 ? ` ×${qty}` : ""}`);
  };

  const handleSelectRow = (row: FlatRow) => {
    setSelectedRow(row);
    if (window.matchMedia("(max-width: 1023px)").matches) {
      setMobileSheetOpen(true);
    }
  };

  const toggleFilter = (key: keyof CatalogFilters) => {
    setFilters((prev) => ({ ...prev, [key]: !prev[key] }));
  };

  return (
    <div className={variant === "page" ? "cc-root cc-root--page" : "cc-root"}>
      <div className={`flex flex-col lg:flex-row lg:items-end lg:justify-between gap-3 ${variant === "section" ? "mb-5" : suppressHeading ? "mb-4" : "mb-6"}`}>
        {!suppressHeading && (
        <div>
          {variant === "section" && <div className="tag-badge mb-3 w-fit">Ціни</div>}
          <h2 className="text-section text-[#1A1A2E]">Послуги та ціни</h2>
          <p className="text-[14px] xl:text-[15px] text-[#1A1A2E]/55 mt-2 max-w-2xl">
            Оберіть категорію, перегляньте ціни та додайте послуги в один клік.
          </p>
        </div>
        )}
        {variant === "section" && (
          <Link to={ROUTES.services} className="btn-outline px-4 py-2.5 text-[13px] no-underline self-start">
            Повний прайс →
          </Link>
        )}
      </div>

      <div className="catalog-console cc-page">
        <div className="cc-page-toolbar">
          <div className="md:hidden">
            <MobileCatalogBar
              nodes={nodes}
              selectionId={selectionId}
              onSelectCategory={selectCategory}
              query={query}
              onQueryChange={setQuery}
              filters={filters}
              onToggleFilter={toggleFilter}
              resultCount={rows.length}
              cartCount={cart.length}
              onCheckout={() => onCheckout?.(cart)}
            />
          </div>
          <div className="hidden md:block">
            <ConsoleHeader
              nodes={nodes}
              selectionId={selectionId}
              onSelectCategory={selectCategory}
              query={query}
              onQueryChange={setQuery}
              filters={filters}
              onToggleFilter={toggleFilter}
              density={density}
              onDensityChange={setDensity}
              resultCount={rows.length}
              cartCount={cart.length}
              onCheckout={() => onCheckout?.(cart)}
            />
          </div>
        </div>

        <div
          className={`cc-page-body ${railCollapsed ? "is-rail-collapsed" : ""} ${inspectorCollapsed ? "is-insp-collapsed" : ""}`}
        >
          <CategoryRail
            nodes={nodes}
            selectionId={selectionId}
            onSelect={selectCategory}
            collapsed={railCollapsed}
            onToggleCollapse={() => setRailCollapsed((v) => !v)}
          />

          <DenseList
            ref={listRef}
            rows={rows}
            density={density}
            selectedKey={selectedRow?.key ?? null}
            inCartKeys={inCartKeys}
            globalSearch={globalSearch}
            onHover={setSelectedRow}
            onSelect={handleSelectRow}
            onAdd={addRow}
          />

          <InspectorPanel
            selected={selected}
            collapsed={inspectorCollapsed}
            onToggleCollapse={() => setInspectorCollapsed((v) => !v)}
            onAdd={(sel, qty) => {
              const row = rows.find((r) => r.item.name === sel.item.name && r.category.id === sel.category.id);
              if (row) void addRow(row, qty);
            }}
            inCart={selectedInCart}
          />
        </div>
      </div>

      <MobileInspectorSheet
        open={mobileSheetOpen}
        selected={selected}
        onClose={() => setMobileSheetOpen(false)}
        onAdd={(sel, qty) => {
          const row = rows.find((r) => r.item.name === sel.item.name && r.category.id === sel.category.id);
          if (row) void addRow(row, qty);
        }}
        inCart={selectedInCart}
      />

      {toast && (
        <div className="fixed bottom-24 lg:bottom-8 left-1/2 -translate-x-1/2 z-[60] glass-strong px-4 py-2.5 rounded-full text-[13px] font-semibold text-[#1A1A2E] shadow-lg anim-slide-down">
          {toast}
        </div>
      )}
    </div>
  );
}
