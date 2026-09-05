import CategoryIcon from "./CategoryIcon";
import Reveal from "./Reveal";
import { useAppNavigate } from "../lib/navigation";
import { useBootstrap } from "@/context/BootstrapContext";

export default function LocationsSection() {
  const { goOrder, goLocations } = useAppNavigate();
  const { branches = [] } = useBootstrap();

  const locations = branches.map((branch, index) => ({
    id: branch.id,
    name: branch.city,
    address: branch.address,
    hours: branch.workingHours,
    image: branch.image,
    badge: index === 0 ? "Головний" : null,
    linkMap: branch.linkMap,
  }));

  return (
    <section className="py-16 sm:py-20">
      <div className="site-container">
        <div className="grid lg:grid-cols-2 gap-8 lg:gap-10 items-start">
          <div className="lg:sticky lg:top-[100px]">
            <div className="tag-badge mb-4 w-fit">Де ми є</div>
            <h2 className="text-section text-[#1A1A2E] mb-4">
              Пункти прийому
              <br />
              по всьому Києву
            </h2>
            <p className="text-[15px] text-[#1A1A2E]/55 mb-6 leading-relaxed">
              {locations.length > 0
                ? `${locations.length} зручних локацій та безкоштовний кур'єр — речі забирають і повертають до дверей`
                : "Безкоштовний кур'єр — речі забирають і повертають до дверей"}
            </p>

            <div className="glass-pink rounded-[28px] p-6 mb-5">
              <div className="flex items-start gap-4">
                <div className="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center shrink-0 overflow-hidden">
                  <CategoryIcon name="delivery" size={36} alt="Кур'єр" fallback />
                </div>
                <div>
                  <h3 className="font-black text-[18px] mb-1">
                  Доставка в обидві сторони</h3>
                  <p className="text-white/80 text-[13px] mb-4">
                    Вартість доставки по Києву від 400 грн.
                  </p>
                  <button
                    onClick={goOrder}
                    className="bg-white text-[#f97171] px-5 py-2.5 rounded-full font-bold text-[13px] hover:scale-105 active:scale-95 transition-transform"
                  >
                    Викликати кур&apos;єра →
                  </button>
                </div>
              </div>
            </div>

            <button onClick={goLocations} className="btn-outline px-5 py-2.5 text-[13px]">
              Дивитися всі на карті
            </button>
          </div>

          {locations.length > 0 && (
            <div className="grid sm:grid-cols-2 gap-3">
              {locations.map((loc, i) => (
                <Reveal key={loc.id} delay={i * 80}>
                  <div className="glass-card p-5 h-full">
                    <h3 className="font-bold text-[14px] text-[#1A1A2E] mb-1">{loc.name}</h3>
                    <p className="text-[12px] text-[#1A1A2E]/55 mb-2">{loc.address}</p>
                    <div className="text-[11px] text-[#1A1A2E]/45">{loc.hours}</div>
                    {loc.linkMap && (
                      <a
                        href={loc.linkMap}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="text-[11px] text-[#4F6EF7] font-medium mt-1 inline-block no-underline hover:underline"
                      >
                        На карті →
                      </a>
                    )}
                  </div>
                </Reveal>
              ))}
            </div>
          )}
        </div>
      </div>
    </section>
  );
}
