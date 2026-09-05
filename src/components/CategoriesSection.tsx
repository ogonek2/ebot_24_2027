import { Link } from "react-router-dom";
import Reveal from "./Reveal";
import CategoryIcon from "./CategoryIcon";
import { useBootstrap } from "@/context/BootstrapContext";
import { categoryUrl } from "@/lib/routes";
import { topLevelCategories } from "@/lib/categories";
import { isInternalHref } from "@/lib/siteNav";

const tones = ["bg-[#f3eeeb]", "bg-[#FFE4EE]", "bg-[#E8F9C8]", "bg-[#DDF4FF]"];

export default function CategoriesSection() {
  const { categories = [], ctaHeaders = [] } = useBootstrap();
  const navCategories = topLevelCategories(categories).filter(
    (cat) => (cat.serviceCount ?? cat.items.length) > 0,
  );

  return (
    <section className="py-14 sm:py-16 relative">
      <div className="site-container">
        {ctaHeaders.length > 0 && (
          <Reveal>
            <div className="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-12 sm:mb-14">
              {ctaHeaders.map((item, i) => {
                const inner = (
                  <>
                    <div
                      className={`w-12 h-12 sm:w-14 sm:h-14 rounded-full flex items-center justify-center mx-auto mb-3 overflow-hidden`}
                    >
                      <CategoryIcon src={item.iconUrl} size={50} alt={item.title} />
                    </div>
                    <div className="font-bold text-[12px] sm:text-[14px] text-[#1A1A2E] leading-snug">
                      {item.title}
                    </div>
                  </>
                );
                const cardClass =
                  "glass-strong rounded-[24px] px-3 py-5 sm:p-5 text-center transition-transform duration-400 hover:-translate-y-1 no-underline block";

                const key = item.id ?? `${item.title}-${i}`;

                return (
                  <Reveal key={key}>
                    {isInternalHref(item.url) ? (
                      <Link to={item.url} className={cardClass}>
                        {inner}
                      </Link>
                    ) : (
                      <a href={item.url} target="_blank" rel="noopener noreferrer" className={cardClass}>
                        {inner}
                      </a>
                    )}
                  </Reveal>
                );
              })}
            </div>
          </Reveal>
        )}

        {navCategories.length > 0 && (
          <>
            <Reveal delay={40}>
              <div className="text-center mb-8">
                <div className="tag-badge mb-4 mx-auto w-fit">Категорії</div>
                <h2 className="text-section text-[#1A1A2E]">Що ми чистимо?</h2>
              </div>
            </Reveal>

            <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3">
              {navCategories.map((cat, i) => (
                <Reveal key={cat.id} delay={i * 35}>
                  <Link
                    to={categoryUrl(cat.id)}
                    className="glass-card group w-full text-center p-4 sm:p-5 h-full no-underline block"
                  >
                    <div className="w-14 h-14 flex items-center justify-center mx-auto mb-3 transition-transform duration-500 group-hover:scale-110 overflow-hidden">
                      <CategoryIcon src={cat.iconUrl} size={50} alt={cat.title} />
                    </div>
                    <div className="font-bold text-[13px] xl:text-[14px] text-[#1A1A2E] mb-1 leading-snug">
                      {cat.title}
                    </div>
                    <div className="text-[11px] font-bold text-[#f97171]">
                      {cat.serviceCount ?? cat.items.length} послуг
                    </div>
                  </Link>
                </Reveal>
              ))}
            </div>
          </>
        )}
      </div>
    </section>
  );
}
