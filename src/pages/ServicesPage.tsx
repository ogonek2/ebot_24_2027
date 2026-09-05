import { Link, useNavigate } from "react-router-dom";
import PriceCatalog from "../components/PriceCatalog";
import PageSkeleton from "../components/skeleton/PageSkeleton";
import { useBootstrap, useBootstrapState } from "@/context/BootstrapContext";
import { ROUTES } from "@/lib/routes";
import { openFeedbackModal } from "@/context/FeedbackContext";
import CategoriesSection from "@/components/CategoriesSection";

export default function ServicesPage() {
  const navigate = useNavigate();
  const bootstrap = useBootstrap();
  const { loading } = useBootstrapState();
  const hasCatalog = Boolean(bootstrap.categories?.length);

  if (loading && !hasCatalog) {
    return <PageSkeleton cards={8} columns={2} />;
  }

  return (
    <div className="min-h-screen pt-24 pb-24">
      <div className="site-container">
        <div className="flex items-center gap-2 text-[13px] text-[#1A1A2E]/40 mb-6">
          <Link to={ROUTES.home} className="hover:text-[#f97171] transition-colors no-underline">
            Головна
          </Link>
          <span>/</span>
          <span className="text-[#1A1A2E]">Послуги та ціни</span>
        </div>

        <PriceCatalog variant="page" onCheckout={() => navigate(ROUTES.cart)} />

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
      <CategoriesSection />
    </div>
  );
}