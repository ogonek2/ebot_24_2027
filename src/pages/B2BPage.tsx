import { useState } from "react";
import CategoryIcon from "../components/CategoryIcon";
import { submitB2bProposal } from "@/lib/api";
import {
  formatUaPhoneInput,
  isUaPhoneComplete,
  phoneDisplayPlaceholder,
} from "@/lib/phoneMask";
import type { IconName } from "@/storage/icons";

const clients: Array<{ icon: IconName; name: string }> = [
  { icon: "pillow", name: "Готелі та хостели" },
  { icon: "washer", name: "Ресторани та кафе" },
  { icon: "iron", name: "Офіси та коворкінги" },
  { icon: "eco", name: "СПА та салони краси" },
  { icon: "hands", name: "Медичні заклади" },
  { icon: "dress", name: "Шоуруми та бутики" },
  { icon: "jacket", name: "Театри та студії" },
  { icon: "bag", name: "Фітнес-клуби" },
];

const benefits: Array<{ icon: IconName; title: string; desc: string }> = [
  { icon: "price", title: "Корпоративні ціни", desc: "Знижки від 15% до 30% залежно від обсягу" },
  { icon: "washer", title: "Регулярне обслуговування", desc: "Щотижневий або щомісячний графік вивозу" },
  { icon: "chat", title: "Єдиний рахунок", desc: "Один рахунок на весь обсяг — зручно для бухгалтерії" },
  { icon: "delivery", title: "Корпоративна логістика", desc: "Великі обсяги — наш транспорт, ваш зручний час" },
  { icon: "raccoon", title: "Персональний менеджер", desc: "Виділений контакт для вирішення будь-яких питань" },
  { icon: "promo", title: "Звітність і аналітика", desc: "Місячні звіти по кількості та вартості послуг" },
];

const volumeOptions = [
  "До 5 000 грн",
  "5 000 – 20 000 грн",
  "20 000 – 50 000 грн",
  "Більше 50 000 грн",
] as const;

type B2bFormFields = "company" | "name" | "phone" | "email" | "volume" | "comment";
type FieldErrors = Partial<Record<B2bFormFields, string>>;

export default function B2BPage() {
  const [b2bForm, setB2bForm] = useState({
    company: "",
    name: "",
    phone: "+380 (",
    email: "",
    volume: "",
    comment: "",
  });
  const [sent, setSent] = useState(false);
  const [status, setStatus] = useState<"idle" | "loading">("idle");
  const [fieldErrors, setFieldErrors] = useState<FieldErrors>({});
  const [formError, setFormError] = useState("");

  const updateField = (key: B2bFormFields, value: string) => {
    setB2bForm((prev) => ({ ...prev, [key]: value }));
    setFieldErrors((prev) => ({ ...prev, [key]: undefined }));
    setFormError("");
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setFormError("");

    const errors: FieldErrors = {};
    if (!b2bForm.company.trim()) errors.company = "Назва компанії є обов'язковим полем";
    if (!b2bForm.name.trim()) errors.name = "Ім'я є обов'язковим полем";
    if (!isUaPhoneComplete(b2bForm.phone)) errors.phone = "Введіть коректний номер телефону";
    if (!b2bForm.email.trim()) {
      errors.email = "Email є обов'язковим полем";
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(b2bForm.email.trim())) {
      errors.email = "Введіть коректний email";
    }
    if (!b2bForm.volume) errors.volume = "Оберіть приблизний обсяг";

    if (Object.keys(errors).length) {
      setFieldErrors(errors);
      return;
    }

    setFieldErrors({});
    setStatus("loading");

    try {
      const res = await submitB2bProposal({
        company: b2bForm.company.trim(),
        name: b2bForm.name.trim(),
        phone: b2bForm.phone,
        email: b2bForm.email.trim(),
        volume: b2bForm.volume,
        comment: b2bForm.comment.trim() || undefined,
      });

      if (res.success === false && res.errors) {
        const next: FieldErrors = {};
        for (const [key, msgs] of Object.entries(res.errors)) {
          if (key in b2bForm) next[key as B2bFormFields] = msgs[0];
        }
        setFieldErrors(next);
        setStatus("idle");
        return;
      }

      if (res.success === false) {
        setFormError(res.message ?? "Виникла помилка при відправці. Спробуйте пізніше.");
        setStatus("idle");
        return;
      }

      setSent(true);
    } catch {
      setFormError("Виникла помилка при відправці. Спробуйте пізніше.");
      setStatus("idle");
    }
  };

  const inputClass = (field: B2bFormFields) =>
    `w-full px-4 py-3.5 rounded-2xl border bg-[#faf8f6] text-[#1A1A2E] text-[14px] focus:outline-none focus:border-[#f97171] focus:ring-2 focus:ring-[#f97171]/20 transition-all ${
      fieldErrors[field] ? "border-red-400" : "border-[#f3eeeb]"
    }`;

  return (
    <div className="min-h-screen">
      {/* Hero */}
      <section className="pt-32 pb-20">
        <div className="site-container">
          <div>
            <div className="tag-badge mb-4 w-fit">B2B рішення</div>
            <h1
              className="text-hero text-[#1A1A2E] mb-5"

            >
              Хімчистка для
              <br />
              <span className="text-[#f97171]">вашого бізнесу</span>
            </h1>
            <p className="text-[17px] text-[#1A1A2E]/60 leading-relaxed mb-8 max-w-md">
              Корпоративні умови, виділений менеджер та регулярне обслуговування для готелів, ресторанів, офісів та інших бізнесів Києва.
            </p>
            <div className="flex flex-wrap gap-3">
              <button
                onClick={() => document.getElementById("b2b-form")?.scrollIntoView({ behavior: "smooth" })}
                className="btn-primary px-8 py-4 text-[15px]"
              >
                Отримати пропозицію
              </button>
              <a
                href="tel:+380678872233"
                className="btn-outline px-8 py-4 text-[15px]"
              >
                Зателефонувати
              </a>
            </div>
          </div>
        </div>
      </section>

      {/* Benefits */}
      <section className="py-20">
        <div className="site-container">
          <div className="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-4 gap-3">
            {[
              { v: "200+", l: "корпоративних клієнтів" },
              { v: "30%", l: "знижка для бізнесу" },
              { v: "24/7", l: "підтримка менеджера" },
              { v: "0 грн", l: "за логістику від 3 000 грн" },
            ].map((s) => (
              <div key={s.l} className="glass-card p-4 text-center">
                <div className="font-black text-[24px] text-[#f97171] mb-0.5" >
                  {s.v}
                </div>
                <div className="text-[11px] text-[#1A1A2E]/50 font-medium">{s.l}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Form */}
      <section id="b2b-form" className="py-20 site-container">
        <div className="flex flex-col lg:flex-row items-center justify-between">
          <div className="text-left mb-10">
            <h2
              className="text-section text-[#1A1A2E] mb-3"

            >
              Отримати корпоративну пропозицію
            </h2>
            <p className="text-[15px] text-[#1A1A2E]/55">
              Заповніть форму — ваш менеджер зв'яжеться протягом 2 годин
            </p>
          </div>

          {sent ? (
            <div className="text-center py-12">
              <div className="text-6xl mb-4">🤝</div>
              <h3 className="font-black text-[24px] text-[#1A1A2E] mb-2" >
                Дякуємо за заявку!
              </h3>
              <p className="text-[15px] text-[#1A1A2E]/55">
                Наш менеджер зв'яжеться з вами протягом 2 годин.
              </p>
            </div>
          ) : (
            <form onSubmit={handleSubmit} className="glass-strong w-full lg:w-1/2 rounded-[28px] p-8 shadow-sm">
              <div className="grid sm:grid-cols-2 gap-5">
                {[
                  { label: "Назва компанії", key: "company" as const, placeholder: "ТОВ «Ваша Компанія»", type: "text" },
                  { label: "Ваше ім'я", key: "name" as const, placeholder: "Контактна особа", type: "text" },
                  { label: "Телефон", key: "phone" as const, placeholder: phoneDisplayPlaceholder(), type: "tel" },
                  { label: "Email", key: "email" as const, placeholder: "info@company.ua", type: "email" },
                ].map((field) => (
                  <div key={field.key}>
                    <label className="block text-[13px] font-semibold text-[#1A1A2E]/60 mb-2">{field.label}</label>
                    <input
                      type={field.type}
                      value={b2bForm[field.key]}
                      onChange={(e) =>
                        updateField(
                          field.key,
                          field.key === "phone" ? formatUaPhoneInput(e.target.value) : e.target.value
                        )
                      }
                      placeholder={field.placeholder}
                      className={inputClass(field.key)}
                      disabled={status === "loading"}
                    />
                    {fieldErrors[field.key] && (
                      <p className="mt-1.5 text-[12px] text-red-500">{fieldErrors[field.key]}</p>
                    )}
                  </div>
                ))}
              </div>

              <div className="mt-5">
                <label className="block text-[13px] font-semibold text-[#1A1A2E]/60 mb-2">Приблизний обсяг на місяць</label>
                <select
                  value={b2bForm.volume}
                  onChange={(e) => updateField("volume", e.target.value)}
                  className={inputClass("volume")}
                  disabled={status === "loading"}
                >
                  <option value="">Оберіть обсяг</option>
                  {volumeOptions.map((option) => (
                    <option key={option} value={option}>
                      {option}
                    </option>
                  ))}
                </select>
                {fieldErrors.volume && (
                  <p className="mt-1.5 text-[12px] text-red-500">{fieldErrors.volume}</p>
                )}
              </div>

              <div className="mt-5">
                <label className="block text-[13px] font-semibold text-[#1A1A2E]/60 mb-2">Коментар</label>
                <textarea
                  value={b2bForm.comment}
                  onChange={(e) => updateField("comment", e.target.value)}
                  placeholder="Розкажіть про вашу потребу..."
                  rows={3}
                  className={`${inputClass("comment")} resize-none`}
                  disabled={status === "loading"}
                />
                {fieldErrors.comment && (
                  <p className="mt-1.5 text-[12px] text-red-500">{fieldErrors.comment}</p>
                )}
              </div>

              {formError && (
                <p className="mt-5 text-[13px] text-red-500 text-center">{formError}</p>
              )}

              <button
                type="submit"
                disabled={status === "loading"}
                className="btn-primary w-full py-4 text-[15px] mt-6 disabled:opacity-60 disabled:cursor-not-allowed"
              >
                {status === "loading" ? "Надсилаємо..." : "Надіслати заявку →"}
              </button>
            </form>
          )}
        </div>
      </section>

      {/* Who we serve */}
      <section className="py-20">
        <div className="site-container">
          <div className="text-center mb-12">
            <h2
              className="text-section text-[#1A1A2E]"

            >
              Хто наші B2B клієнти
            </h2>
          </div>
          <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
            {clients.map((c) => (
              <div key={c.name} className="glass-card text-center p-6">
                <div className="flex justify-center mb-3">
                  <CategoryIcon name={c.icon} size={48} alt={c.name} fallback />
                </div>
                <div className="font-semibold text-[14px] text-[#1A1A2E]" >
                  {c.name}
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Benefits */}
      <section className="py-20">
        <div className="site-container">
          <div className="text-center mb-12">
            <h2
              className="text-section text-[#1A1A2E]"

            >
              Переваги B2B партнерства
            </h2>
          </div>
          <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            {benefits.map((b, index) => (
              <div key={b.title} className="card-hover glass-strong rounded-[28px] p-7">
                <div className="w-12 h-12 rounded-2xl flex items-center justify-center mb-4 overflow-hidden">
                  <span className="text-[24px] font-bold text-[#1A1A2E]">{index + 1}</span>
                </div>
                <h3 className="font-bold text-[16px] text-[#1A1A2E] mb-2" >
                  {b.title}
                </h3>
                <p className="text-[14px] text-[#1A1A2E]/55">{b.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>


    </div>
  );
}
