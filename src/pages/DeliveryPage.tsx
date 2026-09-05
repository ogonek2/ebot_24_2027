import { Link } from "react-router-dom";
import Breadcrumbs, { homeCrumb } from "@/components/Breadcrumbs";
import DeliveryPricingSection from "@/components/DeliveryPricingSection";
import CategoryIcon from "@/components/CategoryIcon";
import { openFeedbackModal } from "@/context/FeedbackContext";
import { ROUTES } from "@/lib/routes";

export default function DeliveryPage() {
  return (
    <div className="py-16 sm:py-20">
      <div className="site-container">
        <Breadcrumbs items={[homeCrumb(), { name: "Доставка" }]} />

        <div className="grid lg:grid-cols-2 gap-10 items-center mb-16">
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
              Викликати кур'єра →
            </Link>
          </div>
        </div>

        <div className="glass-strong rounded-[32px] p-6 sm:p-10">
          <div className="tag-badge mb-4 w-fit">Важливо</div>
          <ul className="space-y-4 text-[15px] text-[#1A1A2E]/70 leading-relaxed">
            <li className="flex gap-3">
              <CategoryIcon name="promo" size={24} alt="" className="shrink-0 mt-0.5" fallback />
              <span>
                Якщо ліфт не працює, забір або доставка замовлення до 3-го поверху —{" "}
                <strong className="text-[#1A1A2E]">безкоштовно</strong>
              </span>
            </li>
            <li className="flex gap-3">
              <CategoryIcon name="location" size={24} alt="" className="shrink-0 mt-0.5" fallback />
              <span>
                Підйом замовлення до дверей — <strong className="text-[#1A1A2E]">25 грн/поверх</strong>
              </span>
            </li>
            <li className="flex gap-3">
              <CategoryIcon name="chat" size={24} alt="" className="shrink-0 mt-0.5" fallback />
              <span>Вартість доставки за межі міста уточнюйте при замовленні</span>
            </li>
          </ul>

          <div className="mt-8 pt-6 border-t border-white/40">
            <h3 className="font-bold text-[18px] text-[#1A1A2E] mb-2">
              Бажаєте замовити кур'єра або залишились питання?
            </h3>
            <p className="text-[14px] text-[#1A1A2E]/50 mb-4">
              Залиште заявку — відповімо швидко
            </p>
            <div className="flex flex-wrap gap-3">
              <Link to={ROUTES.courier} className="btn-primary px-6 py-3 text-[13px] no-underline">
                Замовити зараз
              </Link>
              <a href="tel:+380678872233" className="btn-outline px-6 py-3 text-[13px]">
                067 887 22 33
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
