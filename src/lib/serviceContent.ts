/** HTML body for service pages — API stores rich text in `description`. */
export function resolveServiceHtml(service: {
  content?: string | null;
  description?: string | null;
}): string {
  const candidates = [service.description, service.content].filter(Boolean) as string[];

  for (const raw of candidates) {
    const decoded = decodeHtmlEntities(raw.trim());
    if (decoded && looksLikeHtml(decoded)) return decoded;
  }

  for (const raw of candidates) {
    const decoded = decodeHtmlEntities(raw.trim());
    if (decoded) return decoded;
  }

  return "";
}

function looksLikeHtml(value: string): boolean {
  return /<\/?[a-z][\s\S]*>/i.test(value);
}

function decodeHtmlEntities(value: string): string {
  let current = value;
  for (let i = 0; i < 3; i += 1) {
    if (!/&lt;|&gt;|&amp;|&#/.test(current)) break;
    const next = decodeHtmlEntitiesOnce(current);
    if (next === current) break;
    current = next;
  }
  return current;
}

function decodeHtmlEntitiesOnce(value: string): string {
  if (typeof document !== "undefined") {
    const textarea = document.createElement("textarea");
    textarea.innerHTML = value;
    return textarea.value;
  }
  return value
    .replace(/&lt;/g, "<")
    .replace(/&gt;/g, ">")
    .replace(/&quot;/g, '"')
    .replace(/&#39;/g, "'")
    .replace(/&amp;/g, "&");
}
export function isOnRequestPrice(price?: string | null): boolean {
  if (!price) return true;
  return /запитом|—/i.test(price) || !/\d/.test(price);
}

export function resolveServicePricing(service: {
  price: string;
  priceBatch?: string;
  individualPrice?: string | null;
  oldPrice?: string | null;
  promo?: boolean;
}) {
  const individual = service.individualPrice ?? null;
  const batch = service.priceBatch ?? service.price;

  return {
    individual,
    batch,
    oldPrice: service.oldPrice ?? undefined,
    promo: Boolean(service.promo || service.oldPrice),
  };
}