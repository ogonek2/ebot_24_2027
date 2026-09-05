import { Link } from "react-router-dom";
import CategoryIcon from "./CategoryIcon";
import { useBootstrap } from "@/context/BootstrapContext";
import { fetchAllServicesCached } from "@/lib/api";
import { useCachedQuery } from "@/lib/useCachedQuery";
import { ROUTES, categoryUrl } from "@/lib/routes";
import { childCategories, topLevelCategories } from "@/lib/categories";
import { serviceLinks, companyLinks } from "@/lib/siteNav";

type ServicesMegaMenuProps = {
  onNavigate?: () => void;
};

export default function ServicesMegaMenu({ onNavigate }: ServicesMegaMenuProps) {
  const { categories = [] } = useBootstrap();
  const navCategories = topLevelCategories(categories);
  const { data } = useCachedQuery("api:services:all", () => fetchAllServicesCached());
  const allServices = data?.data ?? [];

  return (
    <div className="mega-menu-panel__card glass-strong rounded-[28px] p-5 sm:p-6 shadow-[0_24px_80px_rgba(26,26,46,0.1)] border border-white/70 w-full max-w-full min-w-0 box-border overflow-hidden">
      <div className="grid lg:grid-cols-[minmax(0,1fr)_minmax(0,1.2fr)_200px] gap-6 min-w-0">
        <div>
          <p className="text-[11px] font-bold uppercase tracking-wider text-[#1A1A2E]/40 mb-4">
            Категорії ({navCategories.length})
          </p>
          {navCategories.length > 0 ? (
            <div className="grid sm:grid-cols-2 gap-2 max-h-[320px] overflow-y-auto pr-1">
              {navCategories.map((cat) => {
                const subs = childCategories(categories, cat.id);
                return (
                  <div key={cat.id} className="space-y-1">
                    <Link
                      to={categoryUrl(cat.id)}
                      onClick={onNavigate}
                      className="flex items-center gap-3 rounded-2xl px-3 py-2.5 hover:bg-white/55 transition-colors no-underline group"
                    >
                      <div className="w-9 h-9 rounded-xl bg-white/60 flex items-center justify-center shrink-0 overflow-hidden">
                        <CategoryIcon src={cat.iconUrl} size={24} alt={cat.title} />
                      </div>
                      <span className="text-[13px] font-semibold text-[#1A1A2E]/75 group-hover:text-[#f97171] transition-colors line-clamp-2">
                        {cat.title}
                      </span>
                    </Link>
                    {subs.length > 0 && (
                      <div className="pl-3 space-y-0.5">
                        {subs.map((sub) => (
                          <Link
                            key={sub.id}
                            to={categoryUrl(sub.id)}
                            onClick={onNavigate}
                            className="block rounded-xl px-3 py-1.5 text-[12px] font-medium text-[#1A1A2E]/55 hover:text-[#f97171] hover:bg-white/45 no-underline"
                          >
                            {sub.title}
                          </Link>
                        ))}
                      </div>
                    )}
                  </div>
                );
              })}
            </div>
          ) : (
            <p className="text-[13px] text-[#1A1A2E]/45">Завантаження категорій…</p>
          )}
          <Link
            to={ROUTES.services}
            onClick={onNavigate}
            className="inline-flex items-center gap-1.5 mt-4 text-[13px] font-bold text-[#f97171] no-underline hover:underline"
          >
            Дивитись весь прайс →
          </Link>
        </div>

        <div className="border-t lg:border-t-0 lg:border-l border-[#1A1A2E]/08 pt-4 lg:pt-0 lg:pl-6">
          <p className="text-[11px] font-bold uppercase tracking-wider text-[#1A1A2E]/40 mb-3">
            Послуги ({allServices.length || "…"})
          </p>
          {allServices.length > 0 ? (
            <ul className="space-y-0.5 max-h-[320px] overflow-y-auto pr-1">
              {allServices.map((svc) => (
                <li key={svc.url}>
                  <Link
                    to={svc.url}
                    onClick={onNavigate}
                    className="block rounded-xl px-3 py-2 hover:bg-white/55 transition-colors no-underline group"
                  >
                    <span className="block text-[13px] font-semibold text-[#1A1A2E] group-hover:text-[#f97171] line-clamp-1">
                      {svc.name}
                    </span>
                    <span className="block text-[11px] text-[#1A1A2E]/45">
                      {svc.categoryTitle} · {svc.price}
                    </span>
                  </Link>
                </li>
              ))}
            </ul>
          ) : (
            <p className="text-[13px] text-[#1A1A2E]/45">Завантаження…</p>
          )}
        </div>

        <div className="border-t lg:border-t-0 lg:border-l border-[#1A1A2E]/08 pt-4 lg:pt-0 lg:pl-6">
          <p className="text-[11px] font-bold uppercase tracking-wider text-[#1A1A2E]/40 mb-3">Швидко</p>
          <ul className="space-y-1">
            {serviceLinks.map((link) => (
              <li key={link.href}>
                <Link
                  to={link.href}
                  onClick={onNavigate}
                  className="block rounded-xl px-3 py-2 hover:bg-white/55 transition-colors no-underline group"
                >
                  <span className="block text-[13px] font-semibold text-[#1A1A2E] group-hover:text-[#f97171]">
                    {link.label}
                  </span>
                </Link>
              </li>
            ))}
            {companyLinks.slice(0, 3).map((link) => (
              <li key={link.href}>
                <Link
                  to={link.href}
                  onClick={onNavigate}
                  className="block rounded-xl px-3 py-2 hover:bg-white/55 transition-colors no-underline group"
                >
                  <span className="block text-[13px] font-semibold text-[#1A1A2E] group-hover:text-[#f97171]">
                    {link.label}
                  </span>
                </Link>
              </li>
            ))}
          </ul>
        </div>
      </div>
    </div>
  );
}
