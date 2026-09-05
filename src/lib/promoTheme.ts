import type { Promotion } from "@/lib/promotions";

export type PromoCardTheme = {
  bg: string;
  title: string;
  discount: string;
  meta: string;
  ctaBg: string;
  ctaText: string;
  isLight: boolean;
};

function normalizeHex(color: string): string | null {
  let hex = color.replace("#", "").trim();
  if (/^[0-9a-f]{3}$/i.test(hex)) {
    hex = hex
      .split("")
      .map((c) => c + c)
      .join("");
  }
  return /^[0-9a-f]{6}$/i.test(hex) ? hex : null;
}

export function isColorLight(color?: string | null): boolean {
  if (!color) return true;

  const trimmed = color.trim();
  const rgb = trimmed.match(/^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i);
  if (rgb) {
    const brightness = (Number(rgb[1]) * 299 + Number(rgb[2]) * 587 + Number(rgb[3]) * 114) / 1000;
    return brightness > 128;
  }

  const hex = normalizeHex(trimmed);
  if (!hex) return true;

  const r = parseInt(hex.slice(0, 2), 16);
  const g = parseInt(hex.slice(2, 4), 16);
  const b = parseInt(hex.slice(4, 6), 16);
  return (r * 299 + g * 587 + b * 114) / 1000 > 128;
}

export function promoCardTheme(promo: Promotion): PromoCardTheme {
  const bg = promo.color?.trim() || "#ffffff";
  const isLight = isColorLight(bg);
  const title = promo.textColor?.trim() || (isLight ? "#1a1a2e" : "#ffffff");
  const discount = promo.discountColor?.trim() || (isLight ? "#f97171" : "#ffb4a8");
  const meta = promo.textColor?.trim() || (isLight ? "rgba(26, 26, 46, 0.62)" : "rgba(255, 255, 255, 0.78)");

  return {
    bg,
    title,
    discount,
    meta,
    ctaBg: discount,
    ctaText: title,
    isLight,
  };
}

export type PromoLayout = "hero" | "wide" | "default";

export function promoLayoutForIndex(index: number): PromoLayout {
  if (index === 0) return "hero";
  if (index === 1) return "wide";
  return "default";
}

export function splitPromoTitle(name: string): string[] {
  return name.split(/\n|<br\s*\/?>/i).map((part) => part.trim()).filter(Boolean);
}
