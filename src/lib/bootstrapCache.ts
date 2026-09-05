import type { SpaBootstrap } from "./bootstrap";
import { getCached, setCached } from "./cache";

export const CATALOG_CACHE_KEY = "bootstrap:catalog:v4";

export function bootstrapCacheKey(pathname: string): string {
  if (pathname === "/") return "bootstrap:home:v4";
  if (pathname.startsWith("/poslugi-ta-cini")) return CATALOG_CACHE_KEY;
  if (pathname === "/lokatsii") return "bootstrap:locations:v3";
  if (pathname.startsWith("/aktsii")) return "bootstrap:promotions:v2";
  if (pathname.startsWith("/blog")) return "bootstrap:blog-shell:v2";
  return `bootstrap:${pathname}`;
}

export function mergeBootstrap(base: SpaBootstrap, patch: SpaBootstrap): SpaBootstrap {
  return {
    ...base,
    ...patch,
    categories: patch.categories ?? base.categories,
    discounts: patch.discounts ?? base.discounts,
    branches: patch.branches ?? base.branches,
    locationCities: patch.locationCities ?? base.locationCities,
    blogPosts: patch.blogPosts ?? base.blogPosts,
    ctaHeaders: patch.ctaHeaders ?? base.ctaHeaders,
    assets: { ...base.assets, ...patch.assets },
  };
}

export function seedBootstrapCache(data: SpaBootstrap, pathname: string): void {
  const key = bootstrapCacheKey(pathname);
  setCached(key, data);

  if (data.categories?.length) {
    const home = getCached<SpaBootstrap>("bootstrap:home:v4");
    setCached(CATALOG_CACHE_KEY, {
      route: "/poslugi-ta-cini",
      categories: data.categories,
      assets: data.assets,
      ctaHeaders: data.ctaHeaders ?? home?.ctaHeaders,
    } satisfies SpaBootstrap);
  }

  if (pathname === "/" && (data.categories?.length || data.ctaHeaders?.length)) {
    setCached("bootstrap:home:v4", data);
  }
}

export function getBootstrapFromCache(pathname: string): SpaBootstrap | null {
  const direct = getCached<SpaBootstrap>(bootstrapCacheKey(pathname));
  if (direct) return direct;

  const home = getCached<SpaBootstrap>("bootstrap:home:v4");
  const catalog = getCached<SpaBootstrap>(CATALOG_CACHE_KEY);

  if (pathname.startsWith("/poslugi-ta-cini") && catalog) return catalog;
  if (pathname.startsWith("/poslugi-ta-cini") && home?.categories?.length) {
    return mergeBootstrap(home, { route: pathname });
  }

  if (catalog && pathname !== "/") {
    return mergeBootstrap(catalog, { route: pathname });
  }

  return home;
}

export function hasBootstrapData(data: SpaBootstrap): boolean {
  return Boolean(
    data.categories?.length ||
      data.discounts?.length ||
      data.branches?.length ||
      data.locationCities?.length ||
      data.blogPosts?.length ||
      data.ctaHeaders?.length,
  );
}
