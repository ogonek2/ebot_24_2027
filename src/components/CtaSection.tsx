import RaccoonLogo from "./RaccoonLogo";
import Reveal from "./Reveal";
import { openFeedbackModal } from "@/context/FeedbackContext";

export default function CtaSection() {
  return (
    <section className="py-16 sm:py-20">
      <div className="site-container">
        <Reveal>
          <div className="glass-pink rounded-[36px] px-6 py-12 sm:px-12 relative overflow-hidden">

            <div className="relative z-10 flex flex-col items-center gap-8 lg:gap-12 text-white">
              <div className="text-center w-full">
                <h2 className="text-section text-[#1A1A2E] mb-4">
                  Ваші речі заслуговують
                  <br />
                  найкращого догляду
                </h2>
                <p className="text-[#1A1A2E]/75 text-[15px] mb-7">
                  Замовте зараз — кур'єр приїде у зручний час.
                </p>
                <div className="flex flex-wrap gap-3 justify-center">
                  <button
                    type="button"
                    onClick={openFeedbackModal}
                    className="bg-white text-[#f97171] px-7 py-3.5 rounded-full font-black text-[14px] hover:scale-105 active:scale-95 transition-transform shadow-xl"
                  >
                    Замовити зараз
                  </button>
                  <a
                    href="tel:+380678872233"
                    className="border border-[#1A1A2E]/40 bg-[#1A1A2E]/10 backdrop-blur-md text-[#1A1A2E] px-7 py-3.5 rounded-full font-bold text-[14px] hover:bg-[#1A1A2E]/20 transition-colors"
                  >
                    067 887 22 33
                  </a>
                </div>
              </div>
            </div>
          </div>
        </Reveal>
      </div>
    </section>
  );
}
