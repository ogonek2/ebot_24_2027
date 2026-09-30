import HeroSection from "../components/HeroSection";
import CategoriesSection from "../components/CategoriesSection";
import ServicesSection from "../components/ServicesSection";
import PromoSection from "../components/PromoSection";
import ConsultationSection from "../components/ConsultationSection";
import BlogSection from "../components/BlogSection";
import DeliveryPricingSection from "../components/DeliveryPricingSection";
import LocationsSection from "../components/LocationsSection";
import ReviewsSection from "../components/ReviewsSection";
import CtaSection from "../components/CtaSection";
import { useBootstrap } from "@/context/BootstrapContext";

export default function HomePage() {
  const { categories = [], discounts = [], blogPosts = [], branches = [] } = useBootstrap();
  const hasCatalog = categories.some(
    (c) => (c.items?.length ?? 0) > 0 || Boolean(c.repairPriceList?.sections?.length),
  );

  return (
    <>
      <HeroSection />
      {discounts.length > 0 && <PromoSection />}
      {hasCatalog && <ServicesSection />}
      {hasCatalog && <CategoriesSection />}
      <ConsultationSection />
      {blogPosts.length > 0 && <BlogSection />}
      <DeliveryPricingSection />
      {branches.length > 0 && <LocationsSection />}
      <ReviewsSection />
      <CtaSection />
    </>
  );
}
