import { useState } from "react";
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
    <section className="consult" id="consultation">
      <div className="site-container">
        <Reveal>
          <div className="consult__stage">
            <p className="consult__watermark" aria-hidden="true">
              ENOT
            </p>
            <div className="consult__glow consult__glow--a" aria-hidden="true" />
            <div className="consult__glow consult__glow--b" aria-hidden="true" />
            <div className="consult__grain" aria-hidden="true" />

            <div className="consult__layout">
              <div className="consult__intro">
                <p className="consult__eyebrow">Консультація</p>
                <h2 className="consult__title">
                  Залиште заявку —
                  <span className="consult__title-accent"> ми передзвонимо</span>
                </h2>
                <p className="consult__lead">
                  Коротко розкажемо про догляд, терміни та підберемо зручний варіант.
                </p>
                <a href="tel:+380678872233" className="consult__phone no-underline">
                  <span className="consult__phone-label">Або одразу</span>
                  <span className="consult__phone-num">067 887 22 33</span>
                </a>
              </div>

              <div className="consult__panel">
                {sent ? (
                  <div className="consult__success">
                    <div className="consult__success-mark" aria-hidden="true">
                      ✓
                    </div>
                    <h3 className="consult__success-title">Заявку прийнято</h3>
                    <p className="consult__success-text">Ми зателефонуємо протягом 15 хвилин</p>
                  </div>
                ) : (
                  <form className="consult__form" onSubmit={handleSubmit} noValidate>
                    <div className="consult__field">
                      <label htmlFor="consult-name">
                        Ім&apos;я <span className="consult__req">*</span>
                      </label>
                      <input
                        id="consult-name"
                        required
                        value={form.name}
                        onChange={(e) => setForm({ ...form, name: e.target.value })}
                        className="glass-input"
                        placeholder="Ваше ім'я"
                        autoComplete="name"
                      />
                    </div>
                    <div className="consult__field">
                      <label htmlFor="consult-phone">
                        Телефон <span className="consult__req">*</span>
                      </label>
                      <input
                        id="consult-phone"
                        required
                        type="tel"
                        value={form.phone}
                        onChange={(e) => setForm({ ...form, phone: formatUaPhoneInput(e.target.value) })}
                        onFocus={() => {
                          if (!form.phone) setForm({ ...form, phone: "+380 (" });
                        }}
                        className="glass-input"
                        placeholder={phoneDisplayPlaceholder()}
                        autoComplete="tel"
                      />
                    </div>
                    {error && <p className="consult__error">{error}</p>}
                    <button type="submit" disabled={loading} className="btn-primary consult__submit">
                      {loading ? "Відправляємо…" : "Відправити заявку"}
                    </button>
                  </form>
                )}
              </div>
            </div>
          </div>
        </Reveal>
      </div>
    </section>
  );
}
