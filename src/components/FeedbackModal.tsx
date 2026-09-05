import { useEffect, useState } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faCheckCircle, faHeadset, faPhone, faXmark } from "@fortawesome/free-solid-svg-icons";
import RaccoonLogo from "./RaccoonLogo";
import { submitContact } from "@/lib/api";
import {
  formatUaPhoneInput,
  isUaPhoneComplete,
  phoneDisplayPlaceholder,
} from "@/lib/phoneMask";

type Props = {
  onClose: () => void;
};

type FieldErrors = Partial<Record<"name" | "phone" | "message", string>>;

export default function FeedbackModal({ onClose }: Props) {
  const [name, setName] = useState("");
  const [phone, setPhone] = useState("+380 (");
  const [message, setMessage] = useState("");
  const [status, setStatus] = useState<"idle" | "loading" | "done" | "error">("idle");
  const [fieldErrors, setFieldErrors] = useState<FieldErrors>({});
  const [formError, setFormError] = useState("");

  useEffect(() => {
    document.body.classList.add("modal-open");
    const onKey = (e: KeyboardEvent) => {
      if (e.key === "Escape") onClose();
    };
    window.addEventListener("keydown", onKey);
    const t = window.setTimeout(() => {
      document.getElementById("feedback-name")?.focus();
    }, 280);
    return () => {
      document.body.classList.remove("modal-open");
      window.removeEventListener("keydown", onKey);
      window.clearTimeout(t);
    };
  }, [onClose]);

  const handlePhoneChange = (value: string) => {
    setPhone(formatUaPhoneInput(value));
    setFieldErrors((prev) => ({ ...prev, phone: undefined }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setFormError("");
    const errors: FieldErrors = {};

    if (!name.trim()) errors.name = "Ім'я є обов'язковим полем";
    if (!isUaPhoneComplete(phone)) errors.phone = "Введіть коректний номер телефону";

    if (Object.keys(errors).length) {
      setFieldErrors(errors);
      return;
    }

    setFieldErrors({});
    setStatus("loading");

    try {
      const res = (await submitContact(name.trim(), phone, message.trim() || undefined)) as {
        success?: boolean;
        message?: string;
        errors?: Record<string, string[]>;
      };

      if (res.success === false && res.errors) {
        const next: FieldErrors = {};
        for (const [key, msgs] of Object.entries(res.errors)) {
          if (key === "name" || key === "phone" || key === "message") {
            next[key] = msgs[0];
          }
        }
        setFieldErrors(next);
        setStatus("idle");
        return;
      }

      setStatus("done");
    } catch {
      setStatus("error");
      setFormError("Виникла помилка при відправці. Спробуйте пізніше.");
    }
  };

  const inputClass = (field: keyof FieldErrors) =>
    `feedback-modal-form__input ${fieldErrors[field] ? "is-error" : ""}`;

  return (
    <div
      className="feedback-modal fixed inset-0 z-[10100] flex items-center justify-center p-3 sm:p-5"
      role="dialog"
      aria-modal="true"
      aria-labelledby="feedback-modal-title"
    >
      <button
        type="button"
        className="feedback-modal__backdrop absolute inset-0"
        aria-label="Закрити"
        onClick={onClose}
      />

      <div className="feedback-modal__dialog glass-strong orbital-panel-in">
        <button type="button" onClick={onClose} className="feedback-modal__close" aria-label="Закрити">
          <FontAwesomeIcon icon={faXmark} />
        </button>

        <div className="feedback-modal__head">
          <div className="feedback-modal__brand">
            <RaccoonLogo size={36} />
            <span className="feedback-modal__badge">
              <FontAwesomeIcon icon={faHeadset} className="text-[10px]" />
              Консультація
            </span>
          </div>
          <h1 id="feedback-modal-title" className="feedback-modal__title">
            Зв&apos;яжіться з нами
          </h1>
          <p className="feedback-modal__subtitle">
            Заповніть форму — передзвонимо протягом робочого дня
          </p>
        </div>

        {status === "done" ? (
          <div className="feedback-modal__success">
            <FontAwesomeIcon icon={faCheckCircle} />
            <div>
              <p className="feedback-modal__success-title">Заявку надіслано!</p>
              <p className="feedback-modal__success-text">
                Дякуємо! Ми зв&apos;яжемося з вами найближчим часом.
              </p>
            </div>
          </div>
        ) : (
          <form onSubmit={handleSubmit} className="feedback-modal-form" noValidate>
            <div className="feedback-modal-form__field">
              <label htmlFor="feedback-name" className="feedback-modal-form__label">
                Ім&apos;я <span>*</span>
              </label>
              <input
                id="feedback-name"
                required
                value={name}
                onChange={(e) => {
                  setName(e.target.value);
                  setFieldErrors((prev) => ({ ...prev, name: undefined }));
                }}
                placeholder="Введіть ваше ім'я"
                className={inputClass("name")}
                autoComplete="name"
              />
              {fieldErrors.name && <p className="feedback-modal-form__error">{fieldErrors.name}</p>}
            </div>

            <div className="feedback-modal-form__field">
              <label htmlFor="feedback-phone" className="feedback-modal-form__label">
                Номер телефону <span>*</span>
              </label>
              <input
                id="feedback-phone"
                required
                type="tel"
                value={phone}
                onChange={(e) => handlePhoneChange(e.target.value)}
                onFocus={() => {
                  if (!phone) setPhone("+380 (");
                }}
                placeholder={phoneDisplayPlaceholder()}
                className={inputClass("phone")}
                autoComplete="tel"
              />
              {fieldErrors.phone && <p className="feedback-modal-form__error">{fieldErrors.phone}</p>}
            </div>

            <div className="feedback-modal-form__field">
              <label htmlFor="feedback-message" className="feedback-modal-form__label">
                Повідомлення
              </label>
              <textarea
                id="feedback-message"
                value={message}
                onChange={(e) => setMessage(e.target.value)}
                placeholder="Ваше повідомлення (необов'язково)"
                rows={4}
                className={`${inputClass("message")} feedback-modal-form__textarea`}
              />
              {fieldErrors.message && (
                <p className="feedback-modal-form__error">{fieldErrors.message}</p>
              )}
            </div>

            {formError && <p className="feedback-modal-form__error">{formError}</p>}
            {status === "error" && !formError && (
              <p className="feedback-modal-form__error">Помилка відправки. Спробуйте ще раз.</p>
            )}

            <div className="feedback-modal-form__actions">
              <button
                type="submit"
                disabled={status === "loading"}
                className="feedback-modal-form__submit btn-primary"
              >
                <FontAwesomeIcon icon={faPhone} className="text-[13px]" />
                {status === "loading" ? "Відправляємо…" : "Зв'язатися"}
              </button>
              <button type="button" onClick={onClose} className="feedback-modal-form__cancel">
                Закрити
              </button>
            </div>
          </form>
        )}
      </div>
    </div>
  );
}
