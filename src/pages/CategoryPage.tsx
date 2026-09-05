import { Link, useNavigate, useParams } from "react-router-dom";
import { useMemo } from "react";
import PriceCatalog from "../components/PriceCatalog";
import PageSkeleton from "../components/skeleton/PageSkeleton";
import SubcategoryNav from "../components/SubcategoryNav";
import Breadcrumbs from "@/components/Breadcrumbs";
import { useBootstrap, useBootstrapState } from "@/context/BootstrapContext";
import { openFeedbackModal } from "@/context/FeedbackContext";
import { buildCategoryBreadcrumbItems, findCategory } from "@/lib/categories";
import { fetchCategoryCached } from "@/lib/api";
import { useCachedQuery } from "@/lib/useCachedQuery";
import { ROUTES } from "@/lib/routes";
import type { SpaCatalogCategory } from "@/lib/bootstrap";

export default function CategoryPage() {
  const { category = "" } = useParams();
  const navigate = useNavigate();
  const bootstrap = useBootstrap();
  const { loading, isRefreshing } = useBootstrapState();

  const { data: apiCategory, loading: apiLoading, error: apiError } = useCachedQuery(
    `api:category:${category}`,
    () => fetchCategoryCached(category),
  );

  const mergedCategories = useMemo(() => {
    const base = [...(bootstrap.categories ?? [])];
    const extras = [
      apiCategory?.category,
      apiCategory?.parent ?? undefined,
      ...(apiCategory?.subcategories ?? []),
    ].filter(Boolean) as SpaCatalogCategory[];

    for (const extra of extras) {
      if (!base.some((c) => c.id === extra.id)) {
        base.push(extra);
      }
    }
    return base;
  }, [bootstrap.categories, apiCategory]);

  const categoryData = findCategory(mergedCategories, category) ?? apiCategory?.category ?? null;
  const categoryTitle = categoryData?.title ?? category;
  const pending = (loading || isRefreshing || apiLoading) && !categoryData;

  if (pending) {
    return <PageSkeleton cards={8} columns={2} />;
  }

  if (!categoryData) {
    return (
      <div className="site-container py-24 text-center">
        <h1 className="text-section text-[#1A1A2E] mb-4">Категорію не знайдено</h1>
        <p className="text-[14px] text-[#1A1A2E]/45 mb-4">{apiError ?? "Перевірте посилання або поверніться до каталогу."}</p>
        <Link to={ROUTES.services} className="text-[#f97171] font-semibold no-underline">
          До всіх послуг →
        </Link>
      </div>
    );
  }

  return (
    <div className="min-h-screen pt-24 pb-24">
      <div className="site-container">
        <Breadcrumbs items={buildCategoryBreadcrumbItems(mergedCategories, category)} />

        <div className="mb-6">
          <div className="tag-badge mb-3 w-fit">Категорія</div>
          <h1 className="text-section text-[#1A1A2E] mb-2">{categoryTitle}</h1>
          <p className="text-[15px] text-[#1A1A2E]/55 max-w-2xl">
            Актуальні ціни та послуги категорії «{categoryTitle}». Додайте потрібне в кошик і оформіть
            замовлення онлайн.
          </p>
        </div>

        <SubcategoryNav categories={mergedCategories} currentId={category} />

        <PriceCatalog variant="page" suppressHeading onCheckout={() => navigate(ROUTES.cart)} />

        <div className="mt-10 text-center">
          <button type="button" onClick={openFeedbackModal} className="btn-primary px-8 py-4 text-[15px]">
            Замовити хімчистку
          </button>
          <p className="text-[13px] text-[#1A1A2E]/45 mt-4">
            Питання? Телефонуйте{" "}
            <a href="tel:+380678872233" className="text-[#f97171] font-semibold">
              067 887 22 33
            </a>
          </p>
        </div>
      </div>
    </div>
  );
}
