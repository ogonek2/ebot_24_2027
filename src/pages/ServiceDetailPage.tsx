import { Link, useParams } from "react-router-dom";
import BlogRelatedSwiper from "@/components/blog/BlogRelatedSwiper";
import Breadcrumbs from "@/components/Breadcrumbs";
import DetailSkeleton from "@/components/skeleton/DetailSkeleton";
import { useBootstrap } from "@/context/BootstrapContext";
import { useCart } from "@/context/CartContext";
import { fetchServiceCached } from "@/lib/api";
import { parseUah, buildAddToCartTarget, resolveCatalogCleaningDisplay } from "@/lib/cartPrices";
import { buildServiceBreadcrumbItems } from "@/lib/categories";
import { resolveServiceHtml } from "@/lib/serviceContent";
import { useCachedQuery } from "@/lib/useCachedQuery";
import { ROUTES, categoryUrl, serviceUrl } from "@/lib/routes";

type ServiceFaqItem = { question?: string; answer?: string };

type ServiceData = {
  id: number;
  name: string;
  href: string;
  price: string;
  priceBatch?: string;
  individualPrice?: string | null;
  oldPrice?: string | null;
  promo?: boolean;
  marker?: string | null;
  description?: string | null;
  content?: string | null;
  excerpt?: string | null;
  image?: string | null;
  categoryTitle?: string | null;
  categoryHref?: string | null;
  parentCategoryTitle?: string | null;
  parentCategoryHref?: string | null;
  faq?: ServiceFaqItem[];
};

type ServiceResponse = {
  service?: ServiceData | null;
  relatedServices?: Array<{
    id: number;
    name: string;
    href: string;
    categoryHref: string;
    price?: string;
  }>;
};

export default function ServiceDetailPage() {
  const { category = "", service = "" } = useParams();
  if (!category || !service) {
    return (
      <div className="site-container py-24 text-center text-[#1A1A2E]/45">
        Послугу не знайдено
      </div>
    );
  }
  return <ServiceDetailContent category={category} service={service} />;
}

function ServiceDetailContent({ category, service }: { category: string; service: string }) {
  const bootstrap = useBootstrap();
  const { openAddModal } = useCart();
  const { data, loading, error } = useCachedQuery<ServiceResponse>(
    `api:service:${category}:${service}`,
    () => fetchServiceCached(category, service),
  );

  const detail = data?.service ?? null;
  const relatedServices = data?.relatedServices ?? [];

  if (loading && !detail) return <DetailSkeleton />;
  if (!detail) {
    return (
      <div className="site-container py-24 text-center text-[#1A1A2E]/45">
        {error ?? "Послугу не знайдено"}
      </div>
    );
  }

  const categoryHref = detail.categoryHref ?? category;
  const categoryTitle = detail.categoryTitle ?? "Послуги";
  const html = resolveServiceHtml(detail);
  const cleaning = resolveCatalogCleaningDisplay(detail);
  const canAddToCart = cleaning.hasStream || cleaning.hasIndividual;
  const faqItems = (detail.faq ?? []).filter((item) => item.question?.trim() && item.answer?.trim());

  const handleAddToCart = () => {
    if (!detail.id || !canAddToCart) return;
    const streamPrice = cleaning.hasStream && cleaning.streamRaw ? parseUah(cleaning.streamRaw) : 0;
    const individualPrice =
      cleaning.hasIndividual && cleaning.individualRaw ? parseUah(cleaning.individualRaw) : null;
    const target = buildAddToCartTarget({
      serviceId: detail.id,
      serviceName: detail.name,
      streamPrice,
      individualPrice,
    });
    if (target) openAddModal(target);
  };

  return (
    <div className="min-h-screen pt-24 pb-20 blog-post-page">
      <div className="site-container">
        <Breadcrumbs
          items={buildServiceBreadcrumbItems(bootstrap.categories, detail, category)}
        />

        <div className="blog-post-layout flex flex-col lg:flex-row gap-8">
          <article className="blog-article font-reading min-w-0">
            <p className="blog-article__eyebrow">«{categoryTitle}»</p>
            <h1 className="blog-article__title">{detail.name}</h1>
            {detail.excerpt && <p className="blog-article__lead">{detail.excerpt}</p>}
            <div>
              {cleaning.isOnRequest ? (
                <p className="blog-article__price blog-article__price--request">Ціна за запитом</p>
              ) : (
                <div
                  className={`blog-article__price-grid ${cleaning.hasStream && cleaning.hasIndividual ? "" : "blog-article__price-grid--single"}`}
                >
                  {cleaning.hasIndividual && (
                    <div className="blog-article__price-tile">
                      <span className="blog-article__price-label">Індивідуальна</span>
                      <span className="blog-article__price-value">{cleaning.individualRaw}</span>
                    </div>
                  )}
                  {cleaning.hasStream && (
                    <div className="blog-article__price-tile">
                      <span className="blog-article__price-label">Потокова</span>
                      <span className="blog-article__price-value blog-article__price-value--batch">
                        {cleaning.streamRaw}
                      </span>
                      {detail.oldPrice && (
                        <span className="blog-article__price-old">{detail.oldPrice}</span>
                      )}
                    </div>
                  )}
                </div>
              )}

              <div className="blog-article__actions">
                {canAddToCart && (
                  <button type="button" onClick={handleAddToCart} className="btn-primary px-5 py-2.5 text-sm w-full">
                    Додати в кошик
                  </button>
                )}
                <Link to={ROUTES.cart} className="btn-outline px-5 py-2.5 text-sm no-underline w-full text-center">
                  Оформити
                </Link>
                <Link to={ROUTES.courier} className="blog-article__action-link no-underline text-center w-full">
                  Викликати кур&apos;єра
                </Link>
              </div>
            </div>
            {detail.image && (
              <figure className="blog-article__figure">
                <img src={detail.image} alt={detail.name} className="blog-article__image" />
              </figure>
            )}

            <div
              className="blog-article__content rich-text-content"
              dangerouslySetInnerHTML={{ __html: html }}
            />

            {faqItems.length > 0 && (
              <div className="blog-article__faq">
                <h2 className="blog-article__faq-title">Поширені питання</h2>
                <div className="space-y-3">
                  {faqItems.map((item) => (
                    <details key={item.question} className="group glass rounded-2xl border border-[#f3eeeb] overflow-hidden">
                      <summary className="flex items-center justify-between px-6 py-4 cursor-pointer font-semibold text-[14px] text-[#1A1A2E] list-none">
                        {item.question}
                        <span className="text-[#f97171] group-open:rotate-45 transition-transform">+</span>
                      </summary>
                      <div
                        className="px-6 pb-4 text-[14px] text-[#1A1A2E]/60 rich-text-content"
                        dangerouslySetInnerHTML={{ __html: resolveServiceHtml({ content: item.answer }) }}
                      />
                    </details>
                  ))}
                </div>
              </div>
            )}
          </article>

          <aside className="blog-related min-w-0">
            {relatedServices.length > 0 && (
              <>
                <h2 className="blog-related__title">Інші послуги</h2>
                <ul className="blog-related__list">
                  {relatedServices.map((item) => (
                    <li key={`${item.categoryHref}-${item.href}`}>
                      <Link
                        to={serviceUrl(item.categoryHref, item.href)}
                        className="blog-related__link no-underline group"
                      >
                        {item.name}
                      </Link>
                      {item.price && <span className="blog-related__meta">{item.price}</span>}
                    </li>
                  ))}
                </ul>
              </>
            )}

            <Link to={categoryUrl(categoryHref)} className="blog-related__more no-underline">
              Усі в «{categoryTitle}» →
            </Link>

            <BlogRelatedSwiper />
          </aside>
        </div>
      </div>
    </div>
  );
}
