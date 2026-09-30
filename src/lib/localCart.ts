/**
 * Browser-side cart (localStorage).
 * Avoids cross-site Laravel session cookies (SameSite) breaking the cart.
 * Server never trusts display prices — checkout reprices from DB.
 */

import type { CartItem } from "@/lib/api";

const STORAGE_KEY = "enot24:cart:v2";
const LAST_ORDER_KEY = "enot24:last-order:v1";

export type LocalCartLine = {
  key: string;
  type: "service" | "repair";
  service_id: number | null;
  repair_item_id: number | null;
  cleaning_type: "individual" | "stream" | "repair";
  quantity: number;
  /** Display snapshot only — server recalculates on checkout */
  service_name: string;
  category_name: string;
  price: number;
  price_from?: boolean;
};

function readRaw(): LocalCartLine[] {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (!raw) return [];
    const parsed = JSON.parse(raw) as LocalCartLine[];
    return Array.isArray(parsed) ? parsed : [];
  } catch {
    return [];
  }
}

function writeRaw(lines: LocalCartLine[]) {
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(lines));
  } catch {
    // quota / private mode
  }
}

export function loadLocalCart(): LocalCartLine[] {
  return readRaw().filter((l) => l && l.quantity > 0 && l.key);
}

export function saveLocalCart(lines: LocalCartLine[]) {
  writeRaw(lines.filter((l) => l.quantity > 0));
}

export function clearLocalCart() {
  try {
    localStorage.removeItem(STORAGE_KEY);
  } catch {
    // ignore
  }
}

export function cartLineToItem(line: LocalCartLine): CartItem {
  return {
    key: line.key,
    service_id: line.service_id,
    repair_item_id: line.repair_item_id,
    service_name: line.service_name,
    category_name: line.category_name,
    quantity: line.quantity,
    cleaning_type: line.cleaning_type,
    price: line.price,
    price_from: line.price_from,
    total: line.price * line.quantity,
  };
}

export function cartTotals(lines: LocalCartLine[]) {
  const items = lines.map(cartLineToItem);
  const total = items.reduce((sum, i) => sum + i.total, 0);
  const count = items.reduce((sum, i) => sum + i.quantity, 0);
  return { items, total, count };
}

export function makeServiceKey(serviceId: number, cleaningType: string) {
  return `${serviceId}_${cleaningType}`;
}

export function makeRepairKey(repairItemId: number) {
  return `repair_${repairItemId}`;
}

/** Checkout payload — IDs + qty only (safe; prices ignored by API). */
export function toCheckoutItems(lines: LocalCartLine[]) {
  return lines.map((l) => ({
    service_id: l.type === "service" ? l.service_id : null,
    repair_item_id: l.type === "repair" ? l.repair_item_id : null,
    cleaning_type: l.cleaning_type,
    quantity: l.quantity,
  }));
}

export function saveLastOrderSnapshot(order: unknown) {
  try {
    sessionStorage.setItem(LAST_ORDER_KEY, JSON.stringify(order));
  } catch {
    // ignore
  }
}

export function loadLastOrderSnapshot<T = unknown>(orderId?: string): T | null {
  try {
    const raw = sessionStorage.getItem(LAST_ORDER_KEY);
    if (!raw) return null;
    const parsed = JSON.parse(raw) as T & { id?: string };
    if (orderId && parsed && typeof parsed === "object" && parsed.id && parsed.id !== orderId) {
      return null;
    }
    return parsed;
  } catch {
    return null;
  }
}

export function clearLastOrderSnapshot() {
  try {
    sessionStorage.removeItem(LAST_ORDER_KEY);
  } catch {
    // ignore
  }
}
