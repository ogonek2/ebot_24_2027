import { useState } from "react";
import CategoryIcon from "./CategoryIcon";
import Reveal from "./Reveal";
import { submitContact } from "@/lib/api";
import {
  formatUaPhoneInput,
  isUaPhoneComplete,
  phoneDisplayPlaceholder,
} from "@/lib/phoneMask";

export default function ConsultationSection() {
  const [sent, setSent] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [form, setForm] = useState({ name: "", phone: "+380 (" });

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");

    if (!form.name.trim()) {
      setError("Введіть ім'я");
      return;
    }
    if (!isUaPhoneComplete(form.phone)) {
      setError("Введіть коректний номер телефону");
      return;
    }

    setLoading(true);
    try {
      const res = await submitContact(form.name.trim(), form.phone);
      if (res.success === false) {
        setError(res.message ?? "Помилка відправки");
        return;
      }
      setSent(true);
    } catch {
      setError("Помилка відправки. Спробуйте ще раз.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <section className="py-16 sm:py-20" id="consultation">
      <div className="site-container">
        <Reveal>
          <div className="grid lg:grid-cols-2 gap-6 items-stretch">
            <div className="glass-strong rounded-[32px] p-6 sm:p-10">
              <div className="tag-badge mb-4 w-fit">Безкоштовна консультація</div>
              <h2 className="text-section text-[#1A1A2E] mb-3">Отримати консультацію</h2>
              <p className="text-lead text-[#1A1A2E]/55 mb-8">
                Залиште свої контакти і ми зв&apos;яжемося з вами найближчим часом для обговорення ваших потреб
              </p>

              <div className="grid grid-cols-2 gap-3 mb-8">
                {[
                  { icon: "chat" as const, title: "Швидко", desc: "Відповідь за 15 хв" },
                  { icon: "eco" as const, title: "Безпечно", desc: "Конфіденційно" },
                  { icon: "raccoon" as const, title: "Експерт", desc: "Професійна порада" },
                  { icon: "promo" as const, title: "Безкоштовно", desc: "Без зобов'язань" },
                ].map((f) => (
                  <div key={f.title} className="glass rounded-2xl p-4">
                    <CategoryIcon name={f.icon} size={28} alt="" fallback />
                    <div className="font-bold text-[14px] text-[#1A1A2E] mt-2">{f.title}</div>
                    <div className="text-[12px] text-[#1A1A2E]/50">{f.desc}</div>
                  </div>
                ))}
              </div>
            </div>

            <div className="glass-strong rounded-[32px] p-6 sm:p-10 flex flex-col justify-center">
              <h3 className="font-bold text-[22px] text-[#1A1A2E] mb-2">Зв&apos;яжіться з нами</h3>
              <p className="text-[14px] text-[#1A1A2E]/50 mb-6">
                Заповніть форму і ми обов&apos;язково відповімо
              </p>

              {sent ? (
                <div className="text-center py-8">
                  <div className="w-16 h-16 rounded-full glass mx-auto mb-4 flex items-center justify-center overflow-hidden">
                    <CategoryIcon name="promo" size={40} alt="" fallback />
                  </div>
                  <div className="font-black text-[20px] text-[#1A1A2E] mb-2">Заявку прийнято!</div>
                  <p className="text-[14px] text-[#1A1A2E]/55">Ми зателефонуємо протягом 15 хвилин</p>
                </div>
              ) : (
                <form className="space-y-4" onSubmit={handleSubmit} noValidate>
                  <div>
                    <label className="block text-[13px] font-semibold text-[#1A1A2E]/60 mb-2">
                      Ім&apos;я <span className="text-red-500">*</span>
                    </label>
                    <input
                      required
                      value={form.name}
                      onChange={(e) => setForm({ ...form, name: e.target.value })}
                      className="glass-input"
                      placeholder="Ваше ім'я"
                    />
                  </div>
                  <div>
                    <label className="block text-[13px] font-semibold text-[#1A1A2E]/60 mb-2">
                      Номер телефону <span className="text-red-500">*</span>
                    </label>
                    <input
                      required
                      type="tel"
                      value={form.phone}
                      onChange={(e) => setForm({ ...form, phone: formatUaPhoneInput(e.target.value) })}
                      onFocus={() => {
                        if (!form.phone) setForm({ ...form, phone: "+380 (" });
                      }}
                      className="glass-input"
                      placeholder={phoneDisplayPlaceholder()}
                    />
                  </div>
                  {error && <p className="text-sm text-red-500">{error}</p>}
                  <button type="submit" disabled={loading} className="btn-primary w-full py-4 text-[15px] mt-2">
                    {loading ? "Відправляємо…" : "Відправити заявку"}
                  </button>
                </form>
              )}
            </div>
          </div>
        </Reveal>
      </div>
    </section>
  );
}
