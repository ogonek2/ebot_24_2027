import Reveal from "./Reveal";

const reviews = [
  {
    name: "Наталія К.",
    avatar: "Н",
    color: "#f97171",
    rating: 5,
    date: "14 серпня 2026",
    text: "Здала пальто після зими — повернули як нове! Кур'єр приїхав вчасно, комунікація бездоганна.",
    service: "Пальто жіноче",
  },
  {
    name: "Михайло Р.",
    avatar: "М",
    color: "#4F6EF7",
    rating: 5,
    date: "10 серпня 2026",
    text: "Килим 8 м² — плями зникли, ворс відновився. Ціна адекватна, термін — 4 дні.",
    service: "Килим із ворсом",
  },
  {
    name: "Олена П.",
    avatar: "О",
    color: "#1A7A55",
    rating: 5,
    date: "5 серпня 2026",
    text: "Чистю вже 2 роки. Ціную гіпоалергенні засоби — жодних реакцій після чистки.",
    service: "Одяг та подушки",
  },
  {
    name: "Андрій С.",
    avatar: "А",
    color: "#7C3AED",
    rating: 5,
    date: "28 липня 2026",
    text: "Весільний костюм — ідеально. Ще й підказали, як правильно зберігати.",
    service: "Весільний костюм",
  },
];

export default function ReviewsSection() {
  return (
    <section className="py-16 sm:py-20">
      <div className="site-container">
        <Reveal>
          <div className="flex flex-col sm:flex-row sm:items-end sm:justify-between mb-8 gap-4">
            <div>
              <div className="tag-badge mb-3 w-fit">Відгуки</div>
              <h2 className="text-section text-[#1A1A2E]">
                Що кажуть клієнти
              </h2>
            </div>
            <div className="glass-pill">
              <span className="font-black text-[18px] text-[#f97171]">4.9</span>
              <span className="text-[12px] text-[#1A1A2E]/50">/ 5 Google</span>
            </div>
          </div>
        </Reveal>

        <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
          {reviews.map((rev, i) => (
            <Reveal key={rev.name} delay={i * 70}>
              <div className="glass-card p-5 h-full flex flex-col">
                <div className="flex gap-0.5 mb-3">
                  {[1, 2, 3, 4, 5].map((n) => (
                    <svg key={n} width="12" height="12" viewBox="0 0 24 24" fill="#f97171">
                      <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                    </svg>
                  ))}
                </div>
                <p className="text-[13px] text-[#1A1A2E]/65 leading-relaxed flex-1 mb-4">
                  "{rev.text}"
                </p>
                <div className="text-[10px] text-[#f97171] font-bold mb-3 bg-white/50 px-2.5 py-1 rounded-full w-fit">
                  {rev.service}
                </div>
                <div className="flex items-center gap-2.5">
                  <div
                    className="w-8 h-8 rounded-full flex items-center justify-center text-white text-[12px] font-bold"
                    style={{ background: rev.color }}
                  >
                    {rev.avatar}
                  </div>
                  <div>
                    <div className="font-bold text-[12px] text-[#1A1A2E]">{rev.name}</div>
                    <div className="text-[10px] text-[#1A1A2E]/40">{rev.date}</div>
                  </div>
                </div>
              </div>
            </Reveal>
          ))}
        </div>
      </div>
    </section>
  );
}
