import HeroSection from "../components/HeroSection";
import CategoriesSection from "../components/CategoriesSection";
import ServicesSection from "../components/ServicesSection";
import PromoSection from "../components/PromoSection";
import AdvantagesSection from "../components/AdvantagesSection";
import ConsultationSection from "../components/ConsultationSection";
import BlogSection from "../components/BlogSection";
import DeliveryPricingSection from "../components/DeliveryPricingSection";
import LocationsSection from "../components/LocationsSection";
import ReviewsSection from "../components/ReviewsSection";
import CtaSection from "../components/CtaSection";
import HomeSkeleton from "../components/skeleton/HomeSkeleton";
import { useBootstrapState } from "@/context/BootstrapContext";
import { hasBootstrapData } from "@/lib/bootstrapLoader";

export default function HomePage() {
  const { data, loading } = useBootstrapState();

  if (loading && !hasBootstrapData(data)) {
    return <HomeSkeleton />;
  }

  return (
    <>
      <HeroSection />
      <PromoSection />
      <ServicesSection />
      <CategoriesSection />
      <AdvantagesSection />
      <ConsultationSection />
      <BlogSection />
      <DeliveryPricingSection />
      <LocationsSection />
      <ReviewsSection />
      <CtaSection />
    </>
  );
}
