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

export default function HomePage() {
  return (
    <>
      <HeroSection />
      <PromoSection />
      <ServicesSection />
      <CategoriesSection />
      <ConsultationSection />
      <BlogSection />
      <DeliveryPricingSection />
      <LocationsSection />
      <ReviewsSection />
      <CtaSection />
    </>
  );
}
