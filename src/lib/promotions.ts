export type Promotion = {
  id: number;
  name: string;
  discountAction?: string | null;
  locations?: string | null;
  banner?: string | null;
  color?: string | null;
  textColor?: string | null;
  discountColor?: string | null;
  url?: string;
  terms?: string | null;
};

export type PromoFilterId = "all" | "percent" | "fixed" | "weekly" | "carpets" | "clothing";

export const PROMO_FILTERS: { id: PromoFilterId; label: string }[] = [
  { id: "all", label: "Усі" },
  { id: "percent", label: "Знижка %" },
  { id: "fixed", label: "Фікс. ціна" },
  { id: "weekly", label: "Щотижня" },
  { id: "carpets", label: "Килими" },
  { id: "clothing", label: "Одяг" },
];

function haystack(p: Promotion): string {
  return `${p.name ?? ""} ${p.discountAction ?? ""} ${p.locations ?? ""}`.toLowerCase();
}

function isPercent(p: Promotion): boolean {
  return /%|−|-\d+\s*%/.test(p.discountAction ?? "");
}

function isFixed(p: Promotion): boolean {
  return /₴|\d+\s*грн/i.test(p.discountAction ?? "");
}

export function filterPromotions(list: Promotion[], filter: PromoFilterId): Promotion[] {
  if (filter === "all") return list;

  return list.filter((p) => {
    const text = haystack(p);
    switch (filter) {
      case "percent":
        return isPercent(p);
      case "fixed":
        return isFixed(p);
      case "weekly":
        return text.includes("щотиж") || text.includes("четвер");
      case "carpets":
        return text.includes("килим");
      case "clothing":
        return /одяг|сороч|дитяч|пальт|курт|textil|текстил/.test(text);
      default:
        return true;
    }
  });
}

export function pickFeatured(list: Promotion[]): Promotion | null {
  return list[0] ?? null;
}

export function promoAccentColor(p: Promotion): string | undefined {
  return p.discountColor ?? undefined;
}
