import { apiUrl } from "./api";
import { cachedFetch } from "./cache";

export type SeoMetaTag = {
  name?: string;
  property?: string;
  content: string;
  itemprop?: string;
  httpEquiv?: string;
};

export type SeoLink = {
  rel: string;
  href: string;
  type?: string;
  title?: string;
};

export type SeoData = {
  title: string;
  meta: SeoMetaTag[];
  links: SeoLink[];
  jsonLd: Record<string, unknown>[];
};

const SEO_CACHE_TTL_MS = 5 * 60 * 1000;

export async function fetchSeo(path: string, search = ""): Promise<SeoData> {
  const params = new URLSearchParams({ path });
  const pageQuery = new URLSearchParams(search.startsWith("?") ? search.slice(1) : search);
  const page = pageQuery.get("page");
  const perPage = pageQuery.get("per_page");
  if (page) params.set("page", page);
  if (perPage) params.set("per_page", perPage);

  const url = `${apiUrl("/api/v1/seo")}?${params.toString()}`;
  const cacheKey = `seo:${path}${search}`;

  return cachedFetch<SeoData>(cacheKey, () => fetch(url).then((r) => {
    if (!r.ok) throw new Error(`SEO fetch failed: ${r.status}`);
    return r.json();
  }), SEO_CACHE_TTL_MS);
}

export const FALLBACK_SEO: SeoData = {
  title: "ЄНОТ-24",
  meta: [
    { name: "description", content: "Хімчистка та прання одягу в Києві від ЄНОТ-24." },
    { name: "robots", content: "index, follow" },
  ],
  links: [],
  jsonLd: [],
};
