import CategoryIcon from "./CategoryIcon";
import Reveal from "./Reveal";
import { Link } from "react-router-dom";
import { ROUTES } from "@/lib/routes";

export default function DeliveryPricingSection() {
  return (
    <section className="py-16 sm:py-20" id="delivery">
      <div className="site-container">
        <Reveal>
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
        </Reveal>
      </div>
    </section>
  );
}
