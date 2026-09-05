import CategoryIcon from "./CategoryIcon";
import Reveal from "./Reveal";
import type { IconName } from "@/storage/icons";

const advantages: Array<{
  icon: IconName;
  title: string;
  desc: string;
}> = [
  {
    icon: "eco",
    title: "Гіпоалергенні засоби",
    desc: "Сертифікована хімія без токсинів. Безпечно для дітей та алергіків.",
  },
  {
    icon: "delivery",
    title: "Безкоштовна доставка",
    desc: "Кур'єр забере і поверне речі. Безкоштовно від 500 грн.",
  },
  {
    icon: "washer",
    title: "Приймаємо 24/7",
    desc: "Замовляйте онлайн у будь-який час. Відповідь за 30 хвилин.",
  },
  {
    icon: "promo",
    title: "Гарантія якості",
    desc: "Не сподобалось — почистимо ще раз або повернемо гроші.",
  },
  {
    icon: "iron",
    title: "Сучасне обладнання",
    desc: "Технології від провідних виробників Італії та Японії.",
  },
  {
    icon: "bag",
    title: "Упаковка та зберігання",
    desc: "Індивідуальна упаковка. Сезонне зберігання — по запиту.",
  },
];

export default function AdvantagesSection() {
  return (
    <section className="py-16 sm:py-20">
      <div className="site-container">
        <Reveal className="text-center mb-10">
          <div className="tag-badge mb-4 mx-auto w-fit">Чому ми?</div>
          <h2 className="text-section text-[#1A1A2E]">
            Переваги ЄНОТ 24
          </h2>
        </Reveal>

        <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
          {advantages.map((adv, i) => {
            const index = i + 1;
            return (
              <Reveal key={adv.title} delay={i * 70}>
              <div className="glass-card p-6 h-full">
                <div className="w-12 h-12 rounded-2xl bg-white/60 flex items-center justify-center mb-4 overflow-hidden">
                  <span className="text-[24px] font-bold text-[#1A1A2E]">{index}</span>
                </div>
                <h3 className="font-bold text-[16px] text-[#1A1A2E] mb-2">{adv.title}</h3>
                <p className="text-[13px] text-[#1A1A2E]/55 leading-relaxed">{adv.desc}</p>
              </div>
            </Reveal>
          );
        })}
        </div>

        <Reveal delay={200}>
          <div className="mt-8 glass-strong rounded-[28px] p-6 sm:p-8">
            <div className="grid grid-cols-2 sm:grid-cols-4 gap-6 text-center">
              {[
                { v: "5+", l: "років на ринку" },
                { v: "15 000+", l: "задоволених клієнтів" },
                { v: "30 хв", l: "до відповіді" },
                { v: "99%", l: "без пошкоджень" },
              ].map((item) => (
                <div key={item.l}>
                  <div className="text-[28px] sm:text-[36px] font-black text-[#f97171]">{item.v}</div>
                  <div className="text-[12px] text-[#1A1A2E]/50 mt-1 font-medium">{item.l}</div>
                </div>
              ))}
            </div>
          </div>
        </Reveal>
      </div>
    </section>
  );
}
