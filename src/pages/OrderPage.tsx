import { useState } from "react";
import CategoryIcon from "../components/CategoryIcon";
import { useAppNavigate } from "../lib/navigation";
import { ROUTES } from "@/lib/routes";
import { Link } from "react-router-dom";
import { submitCourierOrder } from "@/lib/api";
import {
  formatUaPhoneInput,
  isUaPhoneComplete,
  phoneDisplayPlaceholder,
} from "@/lib/phoneMask";

type Step = 1 | 2 | 3;
type FormFields = "name" | "phone" | "address" | "date" | "time" | "comment";
type FieldErrors = Partial<Record<FormFields, string>>;

const initialForm = {
  name: "",
  phone: "+380 (",
  address: "",
  date: "",
  time: "",
  comment: "",
  type: "courier",
};

export default function OrderPage() {
  const { goHome } = useAppNavigate();
  const [step, setStep] = useState<Step>(1);
  const [form, setForm] = useState(initialForm);
  const [submitted, setSubmitted] = useState(false);
  const [status, setStatus] = useState<"idle" | "loading">("idle");
  const [fieldErrors, setFieldErrors] = useState<FieldErrors>({});
  const [formError, setFormError] = useState("");

  const updateField = (key: FormFields, value: string) => {
    setForm((prev) => ({ ...prev, [key]: value }));
    setFieldErrors((prev) => ({ ...prev, [key]: undefined }));
    setFormError("");
  };

  const validateStep1 = (): FieldErrors => {
    const errors: FieldErrors = {};
    if (!form.name.trim()) errors.name = "Ім'я є обов'язковим полем";
    if (!isUaPhoneComplete(form.phone)) errors.phone = "Введіть коректний номер телефону";
    return errors;
  };

  const goToStep2 = () => {
    const errors = validateStep1();
    if (Object.keys(errors).length) {
      setFieldErrors(errors);
      return;
    }
    setFieldErrors({});
    setStep(2);
  };

  const handleSubmit = async () => {
    setFormError("");
    setStatus("loading");

    try {
      const res = await submitCourierOrder({
        name: form.name.trim(),
        phone: form.phone,
        type: form.type as "courier" | "pickup",
        address: form.type === "courier" ? form.address.trim() || undefined : undefined,
        date: form.date.trim() || undefined,
        time: form.time.trim() || undefined,
        comment: form.comment.trim() || undefined,
      });

      if (res.success === false && res.errors) {
        const next: FieldErrors = {};
        for (const [key, msgs] of Object.entries(res.errors)) {
          if (key in form) next[key as FormFields] = msgs[0];
        }
        setFieldErrors(next);
        if (next.name || next.phone) setStep(1);
        else if (next.address) setStep(2);
        setStatus("idle");
        return;
      }

      if (res.success === false) {
        setFormError(res.message ?? "Виникла помилка при відправці. Спробуйте пізніше.");
        setStatus("idle");
        return;
      }

      setSubmitted(true);
    } catch {
      setFormError("Виникла помилка при відправці. Спробуйте пізніше.");
      setStatus("idle");
    }
  };

  const resetForm = () => {
    setForm(initialForm);
    setFieldErrors({});
    setFormError("");
    setStatus("idle");
    setSubmitted(false);
    setStep(1);
  };

  const inputClass = (field?: FormFields) =>
    `w-full px-4 py-3.5 rounded-2xl border bg-[#faf8f6] text-[#1A1A2E] text-[14px] focus:outline-none focus:border-[#f97171] focus:ring-2 focus:ring-[#f97171]/20 transition-all ${
      field && fieldErrors[field] ? "border-red-400" : "border-[#f3eeeb]"
    }`;

  if (submitted) {
    return (
      <div className="min-h-screen pt-24 pb-20 flex items-center justify-center">
        <div className="text-center max-w-md px-4">
          <div className="w-24 h-24 glass rounded-full flex items-center justify-center mx-auto mb-6 overflow-hidden anim-scale-in">
            <span className="text-[48px] font-bold text-[#1A1A2E]">👍</span>
          </div>
          <h2 className="text-section text-[#1A1A2E] mb-3">Заявку прийнято!</h2>
          <p className="text-[16px] text-[#1A1A2E]/60 mb-8">
            Ми зв&apos;яжемося з вами протягом 30 хвилин для підтвердження деталей.
          </p>
          <div className="flex gap-3 justify-center">
            <button onClick={goHome} className="btn-primary px-8 py-3.5 text-sm">
              На головну
            </button>
            <button onClick={resetForm} className="btn-outline px-8 py-3.5 text-sm">
              Нове замовлення
            </button>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="pt-24 pb-20">
      <div className="site-container">
        <div className="flex items-center gap-2 text-[13px] text-[#1A1A2E]/40 mb-8">
          <button onClick={goHome} className="hover:text-[#f97171]">Головна</button>
          <span>/</span>
          <span className="text-[#1A1A2E]">Замовлення</span>
        </div>

        <div className="flex flex-col lg:flex-row items-center justify-between">
          <div>
            <h1 className="text-section text-[#1A1A2E] mb-3 text-left">Замовити хімчистку</h1>
            <p className="text-[15px] text-[#1A1A2E]/55 mb-8 text-left">
              Заповніть форму — ми зв&apos;яжемося з вами протягом 30 хвилин
            </p>
            <div className="flex flex-col lg:flex-row gap-3">
              <Link className="btn-primary px-6 py-4 text-[15px]" to={ROUTES.services}>
                Замовити послугу →
              </Link>
            </div>
          </div>

          <div className="w-full lg:w-1/2">
            <div className="flex items-center gap-2 mb-10">
              {([1, 2, 3] as Step[]).map((s) => (
                <div key={s} className="flex items-center gap-2">
                  <div
                    className={`w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold transition-all ${
                      step === s
                        ? "bg-[#f97171] text-white"
                        : step > s
                          ? "bg-[#E8FBF4] text-[#1A7A55]"
                          : "glass border border-white/45 text-[#1A1A2E]/40"
                    }`}
                  >
                    {step > s ? "✓" : s}
                  </div>
                  <span className={`text-[13px] font-medium hidden sm:inline ${step === s ? "text-[#1A1A2E]" : "text-[#1A1A2E]/40"}`}>
                    {s === 1 ? "Контакти" : s === 2 ? "Деталі" : "Підтвердження"}
                  </span>
                  {s < 3 && <div className={`flex-1 h-[2px] rounded-full mx-2 ${step > s ? "bg-[#f97171]" : "bg-[#f3eeeb]"}`} style={{ width: 40 }} />}
                </div>
              ))}
            </div>

            <div className="glass-strong rounded-[28px] p-8">
              {step === 1 && (
                <div className="space-y-5">
                  <h3 className="font-bold text-[18px] text-[#1A1A2E] mb-6">Ваші контактні дані</h3>
                  <div>
                    <label className="block text-[13px] font-semibold text-[#1A1A2E]/60 mb-2">Ім&apos;я *</label>
                    <input
                      type="text"
                      value={form.name}
                      onChange={(e) => updateField("name", e.target.value)}
                      placeholder="Як до вас звертатись?"
                      className={inputClass("name")}
                      disabled={status === "loading"}
                    />
                    {fieldErrors.name && <p className="mt-1.5 text-[12px] text-red-500">{fieldErrors.name}</p>}
                  </div>
                  <div>
                    <label className="block text-[13px] font-semibold text-[#1A1A2E]/60 mb-2">Телефон *</label>
                    <input
                      type="tel"
                      value={form.phone}
                      onChange={(e) => updateField("phone", formatUaPhoneInput(e.target.value))}
                      placeholder={phoneDisplayPlaceholder()}
                      className={inputClass("phone")}
                      disabled={status === "loading"}
                    />
                    {fieldErrors.phone && <p className="mt-1.5 text-[12px] text-red-500">{fieldErrors.phone}</p>}
                  </div>
                  <div>
                    <label className="block text-[13px] font-semibold text-[#1A1A2E]/60 mb-2">Спосіб отримання</label>
                    <div className="grid grid-cols-2 gap-3">
                      {[
                        { id: "courier", icon: "delivery" as const, label: "Кур'єр до дверей" },
                        { id: "pickup", icon: "location" as const, label: "Самовивіз" },
                      ].map((opt) => (
                        <button
                          key={opt.id}
                          type="button"
                          onClick={() => setForm({ ...form, type: opt.id })}
                          className={`p-4 rounded-2xl border-2 text-left transition-all ${
                            form.type === opt.id
                              ? "border-[#f97171] bg-white/28 backdrop-blur-sm"
                              : "border-white/45 bg-white/15 backdrop-blur-sm hover:border-[#f97171]/30"
                          }`}
                          disabled={status === "loading"}
                        >
                          <div className="mb-1">
                            <CategoryIcon name={opt.icon} size={32} alt={opt.label} fallback />
                          </div>
                          <div className="text-[13px] font-semibold text-[#1A1A2E]">{opt.label}</div>
                        </button>
                      ))}
                    </div>
                  </div>
                  <button
                    type="button"
                    onClick={goToStep2}
                    disabled={status === "loading"}
                    className="btn-primary w-full py-4 text-[15px] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none mt-2"
                  >
                    Далі →
                  </button>
                </div>
              )}

              {step === 2 && (
                <div className="space-y-5">
                  <h3 className="font-bold text-[18px] text-[#1A1A2E] mb-6">Деталі замовлення</h3>
                  {form.type === "courier" && (
                    <div>
                      <label className="block text-[13px] font-semibold text-[#1A1A2E]/60 mb-2">Адреса забору</label>
                      <input
                        type="text"
                        value={form.address}
                        onChange={(e) => updateField("address", e.target.value)}
                        placeholder="вул. Хрещатик, 1, кв. 5"
                        className={inputClass("address")}
                        disabled={status === "loading"}
                      />
                      {fieldErrors.address && <p className="mt-1.5 text-[12px] text-red-500">{fieldErrors.address}</p>}
                    </div>
                  )}
                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <label className="block text-[13px] font-semibold text-[#1A1A2E]/60 mb-2">Дата</label>
                      <input
                        type="date"
                        value={form.date}
                        onChange={(e) => updateField("date", e.target.value)}
                        className={inputClass("date")}
                        disabled={status === "loading"}
                      />
                    </div>
                    <div>
                      <label className="block text-[13px] font-semibold text-[#1A1A2E]/60 mb-2">Час</label>
                      <select
                        value={form.time}
                        onChange={(e) => updateField("time", e.target.value)}
                        className={inputClass("time")}
                        disabled={status === "loading"}
                      >
                        <option value="">Оберіть час</option>
                        {["9:00–11:00", "11:00–13:00", "13:00–15:00", "15:00–17:00", "17:00–19:00", "19:00–21:00"].map((t) => (
                          <option key={t} value={t}>{t}</option>
                        ))}
                      </select>
                    </div>
                  </div>
                  <div>
                    <label className="block text-[13px] font-semibold text-[#1A1A2E]/60 mb-2">Коментар</label>
                    <textarea
                      value={form.comment}
                      onChange={(e) => updateField("comment", e.target.value)}
                      placeholder="Опишіть, що потрібно почистити та будь-які особливості..."
                      rows={4}
                      className={`${inputClass("comment")} resize-none`}
                      disabled={status === "loading"}
                    />
                    {fieldErrors.comment && <p className="mt-1.5 text-[12px] text-red-500">{fieldErrors.comment}</p>}
                  </div>
                  <div className="flex gap-3">
                    <button
                      type="button"
                      onClick={() => setStep(1)}
                      className="btn-outline px-6 py-4 text-[15px] flex-shrink-0"
                      disabled={status === "loading"}
                    >
                      ← Назад
                    </button>
                    <button
                      type="button"
                      onClick={() => setStep(3)}
                      className="btn-primary flex-1 py-4 text-[15px]"
                      disabled={status === "loading"}
                    >
                      Далі →
                    </button>
                  </div>
                </div>
              )}

              {step === 3 && (
                <div className="space-y-5">
                  <h3 className="font-bold text-[18px] text-[#1A1A2E] mb-6">Підтвердіть замовлення</h3>
                  <div className="glass rounded-2xl p-5 space-y-3">
                    {[
                      { l: "Ім'я", v: form.name },
                      { l: "Телефон", v: form.phone },
                      { l: "Тип", v: form.type === "courier" ? "Кур'єр до дверей" : "Самовивіз" },
                      ...(form.address ? [{ l: "Адреса", v: form.address }] : []),
                      ...(form.date ? [{ l: "Дата", v: form.date }] : []),
                      ...(form.time ? [{ l: "Час", v: form.time }] : []),
                      ...(form.comment ? [{ l: "Коментар", v: form.comment }] : []),
                    ].map((row) => (
                      <div key={row.l} className="flex justify-between text-[14px]">
                        <span className="text-[#1A1A2E]/50">{row.l}:</span>
                        <span className="font-semibold text-[#1A1A2E] text-right max-w-[60%]">{row.v}</span>
                      </div>
                    ))}
                  </div>
                  <p className="text-[12px] text-[#1A1A2E]/40">
                    Натискаючи «Підтвердити», ви погоджуєтесь з умовами надання послуг та обробкою персональних даних.
                  </p>
                  {formError && (
                    <p className="text-[13px] text-red-500 text-center">{formError}</p>
                  )}
                  <div className="flex gap-3">
                    <button
                      type="button"
                      onClick={() => setStep(2)}
                      className="btn-outline px-6 py-4 text-[15px] flex-shrink-0"
                      disabled={status === "loading"}
                    >
                      ← Назад
                    </button>
                    <button
                      type="button"
                      onClick={handleSubmit}
                      disabled={status === "loading"}
                      className="btn-primary flex-1 py-4 text-[15px] disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                      {status === "loading" ? "Надсилаємо..." : "Підтвердити замовлення"}
                    </button>
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
