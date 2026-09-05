import { apiUrl } from "@/lib/api";

/**
 * Media files uploaded via Filament (icons library, category_img, banners).
 * DB stores file_path; Laravel serves via /storage/…
 */
export function resolveStorageUrl(url?: string | null): string | null {
  if (!url) return null;

  // Full URL from Laravel url('/storage/…') — use as-is
  if (/^https?:\/\//i.test(url)) {
    return url;
  }

  // Relative /storage/… — prepend API origin when configured (dev / split deploy)
  if (url.startsWith("/storage/")) {
    return apiUrl(url);
  }

  return url;
}
