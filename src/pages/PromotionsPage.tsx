import { useMemo, useState } from "react";
import { Link } from "react-router-dom";
import Breadcrumbs, { homeCrumb } from "@/components/Breadcrumbs";
import PageSkeleton from "@/components/skeleton/PageSkeleton";
import { PromoFiltersBar } from "@/components/promotions/PromoCards";
import PromoShowcaseCard from "@/components/promotions/PromoShowcaseCard";
import { useBootstrap } from "@/context/BootstrapContext";
import { fetchPromotionsCached } from "@/lib/api";
import { filterPromotions, type PromoFilterId, type Promotion } from "@/lib/promotions";
import { useCachedQuery } from "@/lib/useCachedQuery";
import { ROUTES } from "@/lib/routes";
import CategoriesSection from "@/components/CategoriesSection";
import type { SpaDiscount } from "@/lib/bootstrap";

type PromotionsResponse = {
  data?: Promotion[];
};

function mapBootstrapDiscounts(discounts: SpaDiscount[]): Promotion[] {
  return discounts.map((d) => ({
    id: d.id,
    name: d.name,
    discountAction: d.discountAction,
    locations: d.locations,
    banner: d.banner,
    color: d.color,
    textColor: d.textColor,
    discountColor: d.discountColor,
    url: d.url,
  }));
}

export default function PromotionsPage() {
  const bootstrap = useBootstrap();
  const [filter, setFilter] = useState<PromoFilterId>("all");
  const { data, loading } = useCachedQuery<PromotionsResponse>("api:promotions", () => fetchPromotionsCached());

  const promotions = useMemo<Promotion[]>(
    () => data?.data ?? mapBootstrapDiscounts(bootstrap.discounts ?? []),
    [data?.data, bootstrap.discounts],
  );

  const filtered = useMemo(() => filterPromotions(promotions, filter), [promotions, filter]);

  const showSkeleton = loading && promotions.length === 0;

  if (showSkeleton) {
    return <PageSkeleton cards={6} />;
  }

  const countLabel =
    promotions.length === 1
      ? "Одна активна пропозиція"
      : promotions.length >= 2 && promotions.length <= 4
        ? `${["Дві", "Три", "Чотири"][promotions.length - 2]} активні пропозиції`
        : `${promotions.length} активних пропозицій`;

  return (
    <div className="min-h-screen pt-24 pb-20 promo-page">
      <div className="site-container">
        <Breadcrumbs items={[homeCrumb(), { name: "Акції" }]} />

        <header className="promo-page__header">
          <div>
            <div className="tag-badge mb-3 w-fit">Акції</div>
            <h1 className="text-section text-[#1A1A2E] mb-2">Спеціальні пропозиції</h1>
            <p className="text-[15px] text-[#1A1A2E]/55">{countLabel}</p>
          </div>
        </header>

        <PromoFiltersBar active={filter} onChange={(id) => setFilter(id as PromoFilterId)} count={filtered.length} />

        {promotions.length === 0 ? (
          <div className="glass-strong rounded-[28px] p-12 text-center mt-8">
            <h3 className="font-bold text-[20px] mb-3">Наразі акцій немає</h3>
            <p className="text-[#1A1A2E]/55 mb-6">Запрошуємо переглянути наші послуги та ціни</p>
            <Link to={ROUTES.services} className="btn-primary px-8 py-3.5 no-underline inline-block">
              Переглянути послуги
            </Link>
          </div>
        ) : filtered.length === 0 ? (
          <div className="promo-empty-filter glass-strong rounded-[22px]">За обраним фільтром акцій не знайдено.</div>
        ) : (
          <div className="promo-showcase-grid">
            {filtered.map((promo, index) => (
              <PromoShowcaseCard key={promo.id} promo={promo} index={index} />
            ))}
          </div>
        )}
      </div>
      <CategoriesSection />
    </div>
  );
}
