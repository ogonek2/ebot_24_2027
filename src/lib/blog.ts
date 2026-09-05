export type BlogPostListItem = {
  slug: string;
  title: string;
  publishedAt?: string | null;
  image?: string | null;
  url?: string;
  excerpt?: string | null;
};

export type BlogListResponse = {
  data?: BlogPostListItem[];
  meta?: {
    current_page?: number;
    last_page?: number;
    per_page?: number;
    total?: number;
  };
};

const UA_MONTHS = [
  "січня",
  "лютого",
  "березня",
  "квітня",
  "травня",
  "червня",
  "липня",
  "серпня",
  "вересня",
  "жовтня",
  "листопада",
  "грудня",
];

function parseApiDate(dateStr: string): { day: number; month: number; year: number } | null {
  const parts = dateStr.split(".");
  if (parts.length !== 3) return null;
  const day = Number(parts[0]);
  const month = Number(parts[1]);
  const year = Number(parts[2]);
  if (!day || !month || !year) return null;
  return { day, month, year };
}

/** «6 серпня 2026» */
export function formatBlogDateLong(dateStr?: string | null): string {
  if (!dateStr) return "";
  const parsed = parseApiDate(dateStr);
  if (!parsed) return dateStr;
  return `${parsed.day} ${UA_MONTHS[parsed.month - 1] ?? ""} ${parsed.year}`.trim();
}

/** «6 серпня» */
export function formatBlogDateShort(dateStr?: string | null): string {
  if (!dateStr) return "";
  const parsed = parseApiDate(dateStr);
  if (!parsed) return dateStr;
  return `${parsed.day} ${UA_MONTHS[parsed.month - 1] ?? ""}`.trim();
}

/** «06.08» for compact list rows */
export function formatBlogDateCompact(dateStr?: string | null): string {
  if (!dateStr) return "";
  const parsed = parseApiDate(dateStr);
  if (!parsed) return dateStr.slice(0, 5);
  return `${String(parsed.day).padStart(2, "0")}.${String(parsed.month).padStart(2, "0")}`;
}
