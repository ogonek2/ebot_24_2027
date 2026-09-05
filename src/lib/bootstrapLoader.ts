import {
  bootstrapCacheKey,
  CATALOG_CACHE_KEY,
  getBootstrapFromCache,
  hasBootstrapData,
  mergeBootstrap,
  seedBootstrapCache,
} from "@/lib/bootstrapCache";
import { cachedFetch, getCached, prefetch, setCached } from "@/lib/cache";
import { fetchBootstrap, fetchBlogAllCached, fetchLocationsCached, fetchPromotionsCached, fetchB2bItemsCached, fetchAllServicesCached } from "@/lib/api";
import { emptyBootstrap, getBootstrap, type SpaBootstrap } from "@/lib/bootstrap";

function seedRelatedApiCache(data: SpaBootstrap): void {
  if (data.discounts?.length) {
    setCached("api:promotions", {
      data: data.discounts.map((d) => ({
        id: d.id,
        name: d.name,
        discountAction: d.discountAction,
        locations: d.locations,
        banner: d.banner,
        color: d.color,
        textColor: d.textColor,
        discountColor: d.discountColor,
        url: d.url,
      })),
    });
  }
}

export function hydrateBootstrapCache(): SpaBootstrap {
  const inline = getBootstrap();
  if (hasBootstrapData(inline)) {
    seedBootstrapCache(inline, inline.route ?? "/");
    seedRelatedApiCache(inline);
    return inline;
  }
  return getBootstrapFromCache("/") ?? emptyBootstrap;
}

export async function loadBootstrap(pathname: string): Promise<SpaBootstrap> {
  const key = bootstrapCacheKey(pathname);
  return cachedFetch(key, async () => {
    const fresh = await fetchBootstrap(pathname);
    seedBootstrapCache(fresh, pathname);
    seedRelatedApiCache(fresh);
    return fresh;
  });
}

export function peekBootstrap(pathname: string): SpaBootstrap | null {
  return getBootstrapFromCache(pathname) ?? getCached<SpaBootstrap>(bootstrapCacheKey(pathname));
}

export function prefetchBootstrapRoutes(): void {
  prefetch(CATALOG_CACHE_KEY, () => loadBootstrap("/poslugi-ta-cini"));
  prefetch("bootstrap:locations:v3", () => loadBootstrap("/lokatsii"));
  prefetch("api:locations:v1", () => fetchLocationsCached());
  prefetch("api:promotions", () => fetchPromotionsCached());
  prefetch("api:blog:all", () => fetchBlogAllCached());
  prefetch("api:b2b", () => fetchB2bItemsCached());
  prefetch("api:services:all", () => fetchAllServicesCached());
}

export { mergeBootstrap, hasBootstrapData, bootstrapCacheKey, seedBootstrapCache, CATALOG_CACHE_KEY } from "@/lib/bootstrapCache";
