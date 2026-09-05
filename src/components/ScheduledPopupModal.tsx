import { useEffect, useState } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faCheckCircle, faPercent, faPhone, faXmark } from "@fortawesome/free-solid-svg-icons";
import RaccoonLogo from "./RaccoonLogo";
import { submitScheduledPopupContact } from "@/lib/api";
import {
  formatUaPhoneInput,
  isUaPhoneComplete,
  phoneDisplayPlaceholder,
} from "@/lib/phoneMask";
import type { ScheduledPopupModal as PopupData } from "@/lib/scheduledPopups";

type Props = {
  modal: PopupData;
  onClose: () => void;
  onSubmitted: () => void;
};

type FieldErrors = Partial<Record<"name" | "phone", string>>;

export default function ScheduledPopupModal({ modal, onClose, onSubmitted }: Props) {
  const [name, setName] = useState("");
  const [phone, setPhone] = useState("+380 (");
  const [status, setStatus] = useState<"idle" | "loading" | "done" | "error">("idle");
  const [fieldErrors, setFieldErrors] = useState<FieldErrors>({});
  const [formError, setFormError] = useState("");

  const desktopBanner = modal.desktop_image_url ?? modal.mobile_image_url;
  const mobileBanner = modal.mobile_image_url ?? modal.desktop_image_url;

  useEffect(() => {
    document.body.classList.add("modal-open");
    const onKey = (e: KeyboardEvent) => {
      if (e.key === "Escape") onClose();
    };
    window.addEventListener("keydown", onKey);
    const t = window.setTimeout(() => {
      document.getElementById("scheduled-popup-name")?.focus();
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
      const res = await submitScheduledPopupContact(name.trim(), phone, modal.id);

      if (res.success === false && res.errors) {
        const next: FieldErrors = {};
        for (const [key, msgs] of Object.entries(res.errors)) {
          if (key === "name" || key === "phone") next[key] = msgs[0];
        }
        setFieldErrors(next);
        setStatus("idle");
        return;
      }

      setStatus("done");
      window.setTimeout(() => {
        onSubmitted();
      }, 1400);
    } catch {
      setStatus("error");
      setFormError("Виникла помилка при відправці. Спробуйте пізніше.");
    }
  };

  return (
    <div
      className="scheduled-popup fixed inset-0 z-[10102] flex items-center justify-center p-3 sm:p-5"
      role="dialog"
      aria-modal="true"
      aria-labelledby="scheduled-popup-title"
    >
      <button
        type="button"
        className="scheduled-popup__backdrop absolute inset-0"
        aria-label="Закрити"
        onClick={onClose}
      />

      <div className="scheduled-popup__dialog glass-strong orbital-panel-in">
        <button type="button" onClick={onClose} className="scheduled-popup__close" aria-label="Закрити">
          <FontAwesomeIcon icon={faXmark} />
        </button>

        <div className="scheduled-popup__layout">
          {(desktopBanner || mobileBanner) && (
            <div className="scheduled-popup__banner">
              <div className="scheduled-popup__banner-glow" aria-hidden />
              {mobileBanner && (
                <img
                  src={mobileBanner}
                  alt=""
                  className="scheduled-popup__banner-img scheduled-popup__banner-img--mobile"
                  loading="lazy"
                />
              )}
              {desktopBanner && (
                <img
                  src={desktopBanner}
                  alt=""
                  className="scheduled-popup__banner-img scheduled-popup__banner-img--desktop"
                  loading="lazy"
                />
              )}
            </div>
          )}

          <div className="scheduled-popup__form-wrap">
            <div className="scheduled-popup__form-head">
              <div className="scheduled-popup__brand">
                <RaccoonLogo size={32} />
                <span className="scheduled-popup__badge">
                  <FontAwesomeIcon icon={faPercent} className="text-[10px]" />
                  Акція
                </span>
              </div>
              <h2 id="scheduled-popup-title" className="scheduled-popup__title">
                {modal.form_title || "Спеціальна пропозиція"}
              </h2>
              <p className="scheduled-popup__subtitle">
                {modal.form_subtitle || "Залиште заявку — ми зв'яжемося з вами найближчим часом"}
              </p>
            </div>

            {status === "done" ? (
              <div className="scheduled-popup__success">
                <FontAwesomeIcon icon={faCheckCircle} />
                <div>
                  <p className="scheduled-popup__success-title">Заявку надіслано!</p>
                  <p className="scheduled-popup__success-text">
                    Дякуємо! Ми зв&apos;яжемося з вами найближчим часом.
                  </p>
                </div>
              </div>
            ) : (
              <form onSubmit={handleSubmit} className="scheduled-popup-form" noValidate>
                <div className="scheduled-popup-form__field">
                  <label htmlFor="scheduled-popup-name" className="scheduled-popup-form__label">
                    Ім&apos;я <span>*</span>
                  </label>
                  <input
                    id="scheduled-popup-name"
                    required
                    value={name}
                    onChange={(e) => {
                      setName(e.target.value);
                      setFieldErrors((prev) => ({ ...prev, name: undefined }));
                    }}
                    placeholder="Введіть ваше ім'я"
                    className={`scheduled-popup-form__input ${fieldErrors.name ? "is-error" : ""}`}
                    autoComplete="name"
                  />
                  {fieldErrors.name && (
                    <p className="scheduled-popup-form__error">{fieldErrors.name}</p>
                  )}
                </div>

                <div className="scheduled-popup-form__field">
                  <label htmlFor="scheduled-popup-phone" className="scheduled-popup-form__label">
                    Номер телефону <span>*</span>
                  </label>
                  <input
                    id="scheduled-popup-phone"
                    required
                    type="tel"
                    value={phone}
                    onChange={(e) => handlePhoneChange(e.target.value)}
                    onFocus={() => {
                      if (!phone) setPhone("+380 (");
                    }}
                    placeholder={phoneDisplayPlaceholder()}
                    className={`scheduled-popup-form__input ${fieldErrors.phone ? "is-error" : ""}`}
                    autoComplete="tel"
                  />
                  {fieldErrors.phone && (
                    <p className="scheduled-popup-form__error">{fieldErrors.phone}</p>
                  )}
                </div>

                {formError && <p className="scheduled-popup-form__error">{formError}</p>}
                {status === "error" && !formError && (
                  <p className="scheduled-popup-form__error">Помилка відправки. Спробуйте ще раз.</p>
                )}

                <div className="scheduled-popup-form__actions">
                  <button
                    type="submit"
                    disabled={status === "loading"}
                    className="scheduled-popup-form__submit btn-primary"
                  >
                    <FontAwesomeIcon icon={faPhone} className="text-[13px]" />
                    {status === "loading" ? "Відправляємо…" : "Відправити заявку"}
                  </button>
                  <button type="button" onClick={onClose} className="scheduled-popup-form__cancel">
                    Закрити
                  </button>
                </div>
              </form>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
