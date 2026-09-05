import { cachedFetch, BLOG_CACHE_TTL_MS } from "./cache";
import type { BlogListResponse } from "./blog";
import type { SpaBranch, SpaLocationCity, SpaServiceDetail } from "@/lib/bootstrap";

const API_BASE = (import.meta.env.VITE_API_URL ?? "").replace(/\/$/, "");

export function apiUrl(path: string): string {
  const normalized = path.startsWith("/") ? path : `/${path}`;
  return `${API_BASE}${normalized}`;
}

/** Plain CSRF from /api/csrf-token (works cross-subdomain; cookie XSRF often does not). */
let csrfTokenMemory: string | null = null;
let csrfReady: Promise<string> | null = null;

export function resetCsrf(): void {
  csrfTokenMemory = null;
  csrfReady = null;
}

function xsrfCookieToken(): string {
  const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);
  return match ? decodeURIComponent(match[1]) : "";
}

/**
 * Warm Sanctum cookies + load plain CSRF into memory.
 * Safe to call on app boot and before any mutating request.
 */
export function ensureCsrf(force = false): Promise<string> {
  if (!force && csrfTokenMemory) {
    return Promise.resolve(csrfTokenMemory);
  }
  if (!force && csrfReady) {
    return csrfReady;
  }

  csrfReady = (async () => {
    const cookieRes = await fetch(apiUrl("/sanctum/csrf-cookie"), {
      credentials: "include",
      headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
    });
    if (!cookieRes.ok) {
      throw new Error(`CSRF cookie failed: ${cookieRes.status}`);
    }

    const tokenRes = await fetch(apiUrl("/api/csrf-token"), {
      credentials: "include",
      headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
    });
    if (!tokenRes.ok) {
      throw new Error(`CSRF token failed: ${tokenRes.status}`);
    }

    const data = (await tokenRes.json()) as { token?: string };
    const token = data.token?.trim() || xsrfCookieToken();
    if (!token) {
      throw new Error("CSRF token missing");
    }

    csrfTokenMemory = token;
    return token;
  })().catch((err) => {
    resetCsrf();
    throw err;
  });

  return csrfReady;
}

function applyCsrfHeaders(headers: Headers, token: string) {
  headers.set("X-CSRF-TOKEN", token);
  const xsrf = xsrfCookieToken();
  if (xsrf) {
    headers.set("X-XSRF-TOKEN", xsrf);
  }
}

async function apiFetch(path: string, init: RequestInit = {}, retried = false): Promise<Response> {
  const method = (init.method ?? "GET").toUpperCase();
  const needsCsrf = method !== "GET" && method !== "HEAD";

  const headers = new Headers(init.headers);
  headers.set("Accept", "application/json");
  headers.set("X-Requested-With", "XMLHttpRequest");
  if (!headers.has("Content-Type") && init.body) {
    headers.set("Content-Type", "application/json");
  }

  if (needsCsrf) {
    const token = await ensureCsrf();
    applyCsrfHeaders(headers, token);
  }

  const res = await fetch(apiUrl(path), {
    ...init,
    headers,
    credentials: "include",
  });

  if (res.status === 419 && needsCsrf && !retried) {
    resetCsrf();
    await ensureCsrf(true);
    return apiFetch(path, init, true);
  }

  if (!res.ok) {
    throw new Error(`API ${path} failed: ${res.status}`);
  }

  return res;
}

/** Like apiFetch but returns JSON even on 4xx (forms validation). Retries CSRF once on 419. */
async function apiJson<T>(path: string, init: RequestInit = {}, retried = false): Promise<T> {
  const method = (init.method ?? "GET").toUpperCase();
  const needsCsrf = method !== "GET" && method !== "HEAD";

  const headers = new Headers(init.headers);
  headers.set("Accept", "application/json");
  headers.set("X-Requested-With", "XMLHttpRequest");
  if (!headers.has("Content-Type") && init.body) {
    headers.set("Content-Type", "application/json");
  }

  if (needsCsrf) {
    const token = await ensureCsrf();
    applyCsrfHeaders(headers, token);
  }

  const res = await fetch(apiUrl(path), {
    ...init,
    headers,
    credentials: "include",
  });

  if (res.status === 419 && needsCsrf && !retried) {
    resetCsrf();
    await ensureCsrf(true);
    return apiJson<T>(path, init, true);
  }

  return res.json() as Promise<T>;
}


export async function fetchBootstrap(route: string) {
  const res = await apiFetch(`/api/v1/bootstrap?route=${encodeURIComponent(route)}`);
  return res.json();
}

export function fetchPromotionsCached() {
  return cachedFetch("api:promotions", () =>
    apiFetch("/api/v1/promotions").then((res) => res.json()),
  );
}

export function fetchBlogCached(page = 1) {
  return cachedFetch(`api:blog:${page}`, () =>
    apiFetch(`/api/v1/blog?page=${page}`).then((res) => res.json()),
  );
}

async function fetchBlogAllPages(): Promise<BlogListResponse> {
  const perPage = 100;
  const first = (await apiFetch(`/api/v1/blog?page=1&per_page=${perPage}`).then((res) =>
    res.json(),
  )) as BlogListResponse;

  let all = [...(first.data ?? [])];
  const lastPage = first.meta?.last_page ?? 1;

  if (lastPage > 1) {
    const pages = await Promise.all(
      Array.from({ length: lastPage - 1 }, (_, i) =>
        apiFetch(`/api/v1/blog?page=${i + 2}&per_page=${perPage}`).then((res) => res.json()),
      ),
    );
    for (const page of pages as BlogListResponse[]) {
      all = all.concat(page.data ?? []);
    }
  }

  return {
    data: all,
    meta: {
      total: all.length,
      last_page: 1,
      current_page: 1,
      per_page: all.length,
    },
  };
}

export function fetchBlogAllCached() {
  return cachedFetch("api:blog:all", () => fetchBlogAllPages(), BLOG_CACHE_TTL_MS);
}

export function fetchBlogPostCached(slug: string) {
  return cachedFetch(
    `api:blog:post:${slug}`,
    () => apiFetch(`/api/v1/blog/${slug}`).then((res) => res.json()),
    BLOG_CACHE_TTL_MS,
  );
}

export function fetchPromotionCached(id: number) {
  return cachedFetch(`api:promotions:${id}`, () =>
    apiFetch(`/api/v1/promotions/${id}`).then((res) => res.json()),
  );
}

export async function fetchCategory(href: string) {
  const res = await apiFetch(`/api/v1/categories/${encodeURIComponent(href)}`);
  return res.json() as Promise<{
    category: import("@/lib/bootstrap").SpaCatalogCategory;
    parent?: import("@/lib/bootstrap").SpaCatalogCategory | null;
    subcategories?: import("@/lib/bootstrap").SpaCatalogCategory[];
  }>;
}

export function fetchCategoryCached(href: string) {
  return cachedFetch(`api:category:${href}`, () => fetchCategory(href));
}

export async function fetchService(category: string, service: string) {
  const res = await apiFetch(`/api/v1/services/${category}/${service}`);
  return res.json() as Promise<{
    service: SpaServiceDetail;
    relatedServices: Array<{
      id: number;
      name: string;
      href: string;
      categoryHref: string;
      price?: string;
    }>;
  }>;
}

export function fetchServiceCached(category: string, service: string) {
  return cachedFetch(`api:service:${category}:${service}`, () => fetchService(category, service));
}

export async function fetchAllServices() {
  const res = await apiFetch("/api/v1/services");
  return res.json() as Promise<{
    data: Array<{
      id: number;
      name: string;
      href: string;
      categoryHref: string;
      categoryTitle: string;
      price: string;
      url: string;
    }>;
    meta: { total: number };
  }>;
}

export function fetchAllServicesCached() {
  return cachedFetch("api:services:all", () => fetchAllServices());
}

export async function addToCart(
  serviceId: number,
  cleaningType: "individual" | "stream",
  quantity = 1,
) {
  const res = await apiFetch("/api/cart/add", {
    method: "POST",
    body: JSON.stringify({
      service_id: serviceId,
      quantity,
      cleaning_type: cleaningType,
    }),
  });
  const data = (await res.json()) as { success: boolean; message?: string; cart_count?: number };
  if (!data.success) {
    throw new Error(data.message ?? "Не вдалося додати до кошика");
  }
  return data;
}

export async function submitConsultation(name: string, phone: string, message?: string) {
  return apiJson("/api/order/consultation", {
    method: "POST",
    body: JSON.stringify({ name, phone, message }),
  });
}

export async function submitContact(name: string, phone: string, message?: string) {
  return apiJson<{
    success?: boolean;
    message?: string;
    errors?: Record<string, string[]>;
  }>("/api/contact", {
    method: "POST",
    body: JSON.stringify({ name, phone, message: message ?? null }),
  });
}

export type CourierOrderPayload = {
  name: string;
  phone: string;
  type: "courier" | "pickup";
  address?: string;
  date?: string;
  time?: string;
  comment?: string;
};

export async function submitCourierOrder(payload: CourierOrderPayload) {
  return apiJson<{
    success?: boolean;
    message?: string;
    errors?: Record<string, string[]>;
  }>("/api/courier/request", {
    method: "POST",
    body: JSON.stringify({
      name: payload.name,
      phone: payload.phone,
      type: payload.type,
      address: payload.address?.trim() || null,
      date: payload.date?.trim() || null,
      time: payload.time?.trim() || null,
      comment: payload.comment?.trim() || null,
    }),
  });
}

export type B2bProposalPayload = {
  company: string;
  name: string;
  phone: string;
  email: string;
  volume: string;
  comment?: string;
};

export async function submitB2bProposal(payload: B2bProposalPayload) {
  return apiJson<{
    success?: boolean;
    message?: string;
    errors?: Record<string, string[]>;
  }>("/api/b2b/proposal", {
    method: "POST",
    body: JSON.stringify({
      company: payload.company,
      name: payload.name,
      phone: payload.phone,
      email: payload.email,
      volume: payload.volume,
      comment: payload.comment?.trim() || null,
    }),
  });
}

export async function submitScheduledPopupContact(name: string, phone: string, popupModalId: number) {
  return apiJson<{
    success?: boolean;
    message?: string;
    errors?: Record<string, string[]>;
  }>("/api/contact", {
    method: "POST",
    body: JSON.stringify({
      name,
      phone,
      source: "scheduled_popup_modal",
      popup_modal_id: popupModalId,
    }),
  });
}

export async function fetchScheduledPopups() {
  const res = await apiFetch("/api/scheduled-popup-modals");
  return res.json() as Promise<import("@/lib/scheduledPopups").ScheduledPopupResponse>;
}

export type CartItem = {
  key: string;
  service_id: number;
  service_name: string;
  category_name: string;
  quantity: number;
  cleaning_type: string;
  price: number;
  total: number;
};

export async function getCart() {
  const res = await apiFetch("/api/cart");
  return res.json() as Promise<{ items: CartItem[]; total: number; count: number }>;
}

export async function removeFromCart(key: string) {
  const res = await apiFetch(`/api/cart/${key}`, { method: "DELETE" });
  return res.json() as Promise<{ items: CartItem[]; total: number; count: number }>;
}

export async function updateCart(key: string, quantity: number) {
  const res = await apiFetch(`/api/cart/${key}`, {
    method: "PUT",
    body: JSON.stringify({ quantity }),
  });
  return res.json() as Promise<{ items: CartItem[]; total: number; count: number }>;
}

export async function clearCart() {
  const res = await apiFetch("/api/cart/clear", { method: "POST" });
  return res.json() as Promise<{ success: boolean; message?: string }>;
}

export async function getPickupLocations() {
  const res = await apiFetch("/api/pickup-locations");
  return res.json() as Promise<{
    locations: Array<{ id: number; street: string; city: string; working_hours: string }>;
  }>;
}

export function fetchPickupLocationsCached() {
  return cachedFetch("api:pickup-locations", () => getPickupLocations());
}

export async function submitOrder(data: Record<string, unknown>) {
  const res = await apiFetch("/api/order/submit", {
    method: "POST",
    body: JSON.stringify(data),
  });
  return res.json() as Promise<{ success: boolean; order_id?: string; message?: string }>;
}

export type LastOrderItem = {
  service_id: number;
  service_name: string;
  category_name: string;
  quantity: number;
  cleaning_type: string;
  price: number;
  total: number;
};

export type LastOrder = {
  id: string;
  name: string;
  phone: string;
  delivery_method: "self" | "courier";
  pickup_location?: {
    street: string;
    city: string;
    working_hours: string;
  } | null;
  delivery_address?: string | null;
  items: LastOrderItem[];
  total: number;
  created_at: string;
};

export async function getLastOrder(orderId?: string) {
  const qs = orderId ? `?order_id=${encodeURIComponent(orderId)}` : "";
  const res = await fetch(apiUrl(`/api/order/last${qs}`), {
    credentials: "include",
    headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
  });
  if (res.status === 404) return null;
  if (!res.ok) throw new Error(`API /api/order/last failed: ${res.status}`);
  const data = (await res.json()) as { order: LastOrder };
  return data.order;
}

export function invoiceDownloadUrl(orderId: string) {
  return apiUrl(`/invoice/${encodeURIComponent(orderId)}/download`);
}

export async function fetchB2bItem(page: string) {
  const res = await apiFetch(`/api/v1/b2b/${page}`);
  return res.json();
}

export async function fetchB2bItems() {
  const res = await apiFetch("/api/v1/b2b");
  return res.json() as Promise<{
    data: Array<{
      id: number;
      name: string;
      title: string;
      href: string;
      banner?: string | null;
      url: string;
    }>;
  }>;
}

export function fetchB2bItemsCached() {
  return cachedFetch("api:b2b", () => fetchB2bItems());
}

export function fetchB2bItemCached(page: string) {
  return cachedFetch(`api:b2b:${page}`, () => fetchB2bItem(page));
}

export type LocationsResponse = {
  cities: SpaLocationCity[];
  branches: SpaBranch[];
};

export async function fetchLocations() {
  const res = await apiFetch("/api/v1/locations");
  return res.json() as Promise<LocationsResponse>;
}

export function fetchLocationsCached() {
  return cachedFetch("api:locations:v1", () => fetchLocations());
}
