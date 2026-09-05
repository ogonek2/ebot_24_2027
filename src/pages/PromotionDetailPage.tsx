import { useParams } from "react-router-dom";
import Breadcrumbs, { homeCrumb } from "@/components/Breadcrumbs";
import DetailSkeleton from "@/components/skeleton/DetailSkeleton";
import PromoDetailCard from "@/components/promotions/PromoPricingTable";
import { PromoHowItWorks, PromoOthersList } from "@/components/promotions/PromoSidebar";
import { fetchPromotionCached } from "@/lib/api";
import type { Promotion } from "@/lib/promotions";
import { useCachedQuery } from "@/lib/useCachedQuery";
import { ROUTES } from "@/lib/routes";

type PromotionResponse = {
  promotion?: Promotion | null;
  otherPromotions?: Promotion[];
};

export default function PromotionDetailPage() {
  const { id = "" } = useParams();
  const promotionId = Number(id);
  const { data, loading, error } = useCachedQuery<PromotionResponse>(
    `api:promotions:${promotionId}`,
    () => fetchPromotionCached(promotionId),
  );

  const promotion = data?.promotion ?? null;
  const others = data?.otherPromotions ?? [];

  if (loading && !promotion) return <DetailSkeleton />;
  if (!promotion) {
    return <div className="site-container py-24 text-center text-[#1A1A2E]/45">{error ?? "Акцію не знайдено"}</div>;
  }

  return (
    <div className="min-h-screen pt-24 pb-20 promo-page">
      <div className="site-container">
        <Breadcrumbs items={[homeCrumb(), { name: "Акції", url: ROUTES.promotions }, { name: promotion.name }]} />

        <div className="promo-detail-layout">
          <div className="promo-detail-main">
            {promotion.banner && (
              <img src={promotion.banner} alt="" className="w-full rounded-[24px] max-h-[280px] object-cover mb-5" />
            )}
            <PromoDetailCard promo={promotion} />
          </div>
          <aside className="promo-detail-aside">
            <PromoHowItWorks />
            <PromoOthersList items={others} />
          </aside>
        </div>
      </div>
    </div>
  );
}
