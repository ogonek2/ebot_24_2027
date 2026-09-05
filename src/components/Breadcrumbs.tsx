import { Link } from "react-router-dom";
import { ROUTES } from "@/lib/routes";

export type BreadcrumbItem = {
  name: string;
  url?: string;
};

type BreadcrumbsProps = {
  items: BreadcrumbItem[];
};

export default function Breadcrumbs({ items }: BreadcrumbsProps) {
  return (
    <nav className="flex items-center gap-2 text-[13px] text-[#1A1A2E]/40 mb-6 flex-wrap" aria-label="Breadcrumb">
      {items.map((item, index) => (
        <span key={`${item.name}-${index}`} className="flex items-center gap-2">
          {index > 0 && <span>/</span>}
          {item.url ? (
            <Link to={item.url} className="hover:text-[#f97171] transition-colors no-underline">
              {item.name}
            </Link>
          ) : (
            <span className="text-[#1A1A2E]">{item.name}</span>
          )}
        </span>
      ))}
    </nav>
  );
}

export function homeCrumb(): BreadcrumbItem {
  return { name: "Головна", url: ROUTES.home };
}
