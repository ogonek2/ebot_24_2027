import { Link, useParams } from "react-router-dom";
import Breadcrumbs, { homeCrumb } from "@/components/Breadcrumbs";
import DetailSkeleton from "@/components/skeleton/DetailSkeleton";
import { fetchB2bItemCached } from "@/lib/api";
import { useCachedQuery } from "@/lib/useCachedQuery";
import { ROUTES } from "@/lib/routes";
import { openFeedbackModal } from "@/context/FeedbackContext";

type B2bItem = {
  title: string;
  name?: string;
  description?: string | null;
  banner?: string | null;
};

type B2bItemResponse = {
  item?: B2bItem | null;
};

export default function B2BDetailPage() {
  const { page = "" } = useParams();
  const { data, loading, error } = useCachedQuery<B2bItemResponse>(
    `api:b2b:${page}`,
    () => fetchB2bItemCached(page),
  );

  const item = data?.item ?? null;

  if (loading && !item) {
    return <DetailSkeleton />;
  }

  if (!item) {
    return (
      <div className="site-container py-24 text-center text-[#1A1A2E]/45">
        {error ?? "Сторінку не знайдено"}
      </div>
    );
  }

  return (
    <div className="min-h-screen pt-24 pb-20">
      <div className="site-container">
        <Breadcrumbs
          items={[homeCrumb(), { name: "Для бізнесу", url: ROUTES.b2b }, { name: item.title }]}
        />
      </div>

      <div className="gradient-cta rounded-b-[40px] px-4 py-16 mb-12">
        <div className="site-container grid lg:grid-cols-2 gap-10 items-center">
          <div className="text-white">
            <h1 className="text-section mb-4">{item.title}</h1>
            <p className="text-white/80 text-[16px] mb-8 leading-relaxed">
              {item.description ?? "Професійна послуга для вашого бізнесу з кур'єрською доставкою."}
            </p>
            <button
              type="button"
              onClick={openFeedbackModal}
              className="bg-white text-[#f97171] px-8 py-3.5 rounded-2xl font-bold"
            >
              Замовити зараз
            </button>
          </div>
          {item.banner && (
            <img src={item.banner} alt="" className="w-full h-80 object-cover rounded-[28px] shadow-2xl" />
          )}
        </div>
      </div>

      <div className="site-container grid md:grid-cols-2 lg:grid-cols-4 gap-4">
        {[
          { title: "Гарантія якості", desc: "100% гарантія на всі послуги" },
          { title: "Швидке виконання", desc: "Терміни від 24 годин" },
          { title: "Кур'єрська доставка", desc: "Заберемо та повернемо самі" },
          { title: "Корпоративні ціни", desc: "Знижки від обсягу" },
        ].map((f) => (
          <div key={f.title} className="glass-card p-6">
            <h3 className="font-bold text-[15px] mb-2">{f.title}</h3>
            <p className="text-[13px] text-[#1A1A2E]/55">{f.desc}</p>
          </div>
        ))}
      </div>

      <div className="site-container mt-10 text-center">
        <Link to={ROUTES.b2b} className="text-[#f97171] font-semibold no-underline hover:underline">
          ← Всі B2B послуги
        </Link>
      </div>
    </div>
  );
}
