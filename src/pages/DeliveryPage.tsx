import { Link } from "react-router-dom";
import Breadcrumbs, { homeCrumb } from "@/components/Breadcrumbs";
import DeliveryPricingSection from "@/components/DeliveryPricingSection";
import { openFeedbackModal } from "@/context/FeedbackContext";
import { ROUTES } from "@/lib/routes";

export default function DeliveryPage() {
  return (
    <div className="py-16 sm:py-20">
      <div className="site-container">
        <Breadcrumbs items={[homeCrumb(), { name: "Доставка" }]} />

        <div className="grid lg:grid-cols-2 gap-10 items-center mb-10">
          <div>
            <div className="tag-badge mb-4 w-fit">Швидка доставка</div>
            <h1 className="text-section text-[#1A1A2E] mb-4">
              <span className="text-[#f97171]">Доставка</span> одягу
            </h1>
            <p className="text-[16px] text-[#1A1A2E]/55 mb-8 leading-relaxed">
              Швидка та надійна доставка ваших речей прямо до дверей. Кур&apos;єр забере і поверне готові речі у зручний час.
            </p>
            <div className="flex flex-wrap gap-3">
              <Link to={ROUTES.courier} className="btn-primary px-7 py-3.5 no-underline">
                Викликати кур&apos;єра
              </Link>
              <button type="button" onClick={openFeedbackModal} className="btn-outline px-7 py-3.5">
                Консультація
              </button>
            </div>
          </div>
          <div className="glass-pink rounded-[32px] p-6 sm:p-10 flex flex-col justify-center">
            <div className="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center mb-5 overflow-hidden">
              <i className="fa-solid fa-truck text-[20px] text-[#1A1A2E]"></i>
            </div>
            <h2 className="text-section mb-3">Вартість доставки</h2>
            <div className="text-[40px] xl:text-[48px] font-black mb-2">Від 400 грн</div>
            <p className="text-white/80 text-[15px] mb-6">Доставка в обидві сторони</p>
            <Link
              to={ROUTES.courier}
              className="bg-white text-[#f97171] px-6 py-3.5 rounded-full font-bold text-[14px] w-fit hover:scale-105 active:scale-95 transition-transform no-underline inline-block"
            >
              Викликати кур&apos;єра →
            </Link>
          </div>
        </div>
      </div>

      <DeliveryPricingSection />
    </div>
  );
}
