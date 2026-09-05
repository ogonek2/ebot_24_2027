import { Link } from "react-router-dom";
import type { SpaCatalogCategory } from "@/lib/bootstrap";
import { categoryGroupId, childCategories, findCategory } from "@/lib/categories";
import { categoryUrl } from "@/lib/routes";

type SubcategoryNavProps = {
  categories: SpaCatalogCategory[] | undefined;
  currentId: string;
};

export default function SubcategoryNav({ categories, currentId }: SubcategoryNavProps) {
  const current = findCategory(categories, currentId);
  const groupId = categoryGroupId(current, currentId);
  const children = childCategories(categories, groupId);
  const parent = findCategory(categories, groupId);

  if (children.length === 0 || !parent) return null;

  const tabClass = (active: boolean) =>
    `inline-flex items-center rounded-full px-4 py-2 text-[13px] font-semibold no-underline transition-colors ${
      active
        ? "bg-[#f97171] text-white shadow-[0_8px_24px_rgba(249,113,113,0.25)]"
        : "glass text-[#1A1A2E]/70 hover:text-[#f97171]"
    }`;

  return (
    <nav className="flex flex-wrap gap-2 mb-6" aria-label="Підкатегорії">
      <Link to={categoryUrl(parent.id)} className={tabClass(currentId === parent.id)}>
        Усі послуги
      </Link>
      {children.map((sub) => (
        <Link key={sub.id} to={categoryUrl(sub.id)} className={tabClass(currentId === sub.id)}>
          {sub.title}
        </Link>
      ))}
    </nav>
  );
}
