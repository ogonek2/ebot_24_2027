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
            <p className="text-[15px] sm:text-[16px] text-[#1A1A2E]/75 leading-relaxed flex gap-3 items-start">
              <CategoryIcon name="chat" size={24} alt="" className="shrink-0 mt-0.5" fallback />
              <span>
                Вартість доставки уточнюйте при замовленні.
              </span>
            </p>

            <div className="mt-8 pt-6 border-t border-white/40">
              <h3 className="font-bold text-[18px] text-[#1A1A2E] mb-2">
                Бажаєте замовити кур&apos;єра або залишились питання?
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
