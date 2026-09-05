import Breadcrumbs, { homeCrumb } from "@/components/Breadcrumbs";
import CategoriesSection from "@/components/CategoriesSection";
import CategoryIcon from "@/components/CategoryIcon";
import { openFeedbackModal } from "@/context/FeedbackContext";

const contacts = [
  { param: 'brands', icon: "instagram" as const, title: "Instagram", value: "@enot24cleaner", href: "https://instagram.com/enot24cleaner", external: true},
  { param: 'brands', icon: "tiktok" as const, title: "TikTok", value: "@enot24_cleanner", href: "https://www.tiktok.com/@enot24_cleanner", external: true},
  { param: 'brands', icon: "telegram" as const, title: "Telegram", value: "@enot24ServiceBot", href: "https://t.me/enot24ServiceBot", external: true},
  { param: 'solid', icon: "phone" as const, title: "Телефон", value: "+38 (067) 887-22-33", href: "tel:+380678872233" },
  { param: 'solid', icon: "phone" as const, title: "Телефон", value: "+38 (044) 337-22-33", href: "tel:+380443372233" },
  { param: 'solid', icon: "envelope" as const, title: "Email", value: "office.enot24@gmail.com", href: "mailto:office.enot24@gmail.com" }
];

export default function ContactsPage() {
  return (
    <div className="min-h-screen pt-24 pb-20">
      <div className="site-container">
        <Breadcrumbs items={[homeCrumb(), { name: "Контакти" }]} />

        <div className="text-center mb-12">
          <div className="tag-badge mb-4 w-fit mx-auto">Зв&apos;яжіться з нами</div>
          <h1 className="text-section text-[#1A1A2E] mb-3">
            Наші <span className="text-[#f97171]">контакти</span>
          </h1>
          <p className="text-[16px] text-[#1A1A2E]/55 max-w-xl mx-auto">
            Ми завжди на зв&apos;язку! Зв&apos;яжіться з нами будь-яким зручним способом
          </p>
        </div>

        <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 max-w-5xl mx-auto mb-12">
          {contacts.map((c) => (
            <a
              key={c.href}
              href={c.href}
              target={c.external ? "_blank" : undefined}
              rel={c.external ? "noopener noreferrer" : undefined}
              className="border border-[#1A1A2E]/75 rounded-[28px] p-6 text-center block no-underline transition-transform hover:-translate-y-1">
              <div className="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <span className="text-[34px] font-bold text-[#1A1A2E]"><i className={`fa-${c.param} fa-${c.icon}`}></i></span>
              </div>
              <h3 className="font-bold text-[15px] mb-1 text-[#1A1A2E]">{c.title}</h3>
              <p className="font-semibold text-[14px] text-[#f97171]">{c.value}</p>
            </a>
          ))}
        </div>

        <div className="glass-strong rounded-[28px] p-8 text-center max-w-xl mx-auto">
          <h2 className="font-black text-[20px] mb-3">Потрібна консультація?</h2>
          <p className="text-[14px] text-[#1A1A2E]/55 mb-6">Залиште заявку — передзвонимо</p>
          <button type="button" onClick={openFeedbackModal} className="btn-primary px-8 py-3.5">
            Зв&apos;язатися з нами
          </button>
        </div>
      </div>
      <CategoriesSection />
    </div>
  );
}
