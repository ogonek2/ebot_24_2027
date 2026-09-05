import { useEffect, useMemo, useState } from "react";
import {
  formatUah,
  resolveCleaningAvailability,
  type AddToCartTarget,
} from "@/lib/cartPrices";

type CleaningType = "stream" | "individual";

type Props = {
  open: boolean;
  target: AddToCartTarget | null;
  onClose: () => void;
  onConfirm: (cleaningType: CleaningType, quantity: number) => Promise<void>;
};

export default function AddToCartModal({ open, target, onClose, onConfirm }: Props) {
  const [cleaningType, setCleaningType] = useState<CleaningType>("stream");
  const [quantity, setQuantity] = useState(1);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const availability = useMemo(
    () => (target ? resolveCleaningAvailability(target) : null),
    [target],
  );

  const unitPrice =
    cleaningType === "individual" && availability?.hasIndividual
      ? availability.individualPrice ?? 0
      : availability?.streamPrice ?? 0;
  const total = unitPrice * quantity;
  const showTypePicker = Boolean(availability?.hasStream && availability?.hasIndividual);

  useEffect(() => {
    if (!open || !target || !availability) return;
    setCleaningType(availability.defaultType);
    setQuantity(target.initialQuantity ?? 1);
    setError(null);
    setSubmitting(false);
    document.body.style.overflow = "hidden";
    return () => {
      document.body.style.overflow = "";
    };
  }, [open, target, availability]);

  useEffect(() => {
    if (!open) return;
    const onKey = (e: KeyboardEvent) => {
      if (e.key === "Escape") onClose();
    };
    window.addEventListener("keydown", onKey);
    return () => window.removeEventListener("keydown", onKey);
  }, [open, onClose]);

  if (!open || !target || !availability) return null;

  const handleSubmit = async () => {
    setSubmitting(true);
    setError(null);
    try {
      const type =
        cleaningType === "individual" && availability.hasIndividual
          ? "individual"
          : availability.hasStream
            ? "stream"
            : "individual";
      await onConfirm(type, quantity);
    } catch (err) {
      setError(err instanceof Error ? err.message : "Не вдалося додати до кошика");
      setSubmitting(false);
    }
  };

  return (
    <div className="cart-modal-overlay" onClick={onClose} role="presentation">
      <div
        className="cart-modal"
        onClick={(e) => e.stopPropagation()}
        role="dialog"
        aria-modal="true"
        aria-labelledby="cart-modal-title"
      >
        <div className="cart-modal__header">
          <h2 id="cart-modal-title" className="cart-modal__title">
            Додати до корзини
          </h2>
          <button type="button" onClick={onClose} className="cart-modal__close" aria-label="Закрити">
            ✕
          </button>
        </div>

        <p className="cart-modal__service">{target.serviceName}</p>

        <div className="cart-modal__section">
          <p className="cart-modal__label">Тип чистки</p>
          {showTypePicker ? (
            <div className="cart-modal__options">
              {availability.hasStream && (
                <label
                  className={`cart-modal__option ${cleaningType === "stream" ? "cart-modal__option--active" : ""}`}
                >
                  <input
                    type="radio"
                    name="cleaning_type"
                    value="stream"
                    checked={cleaningType === "stream"}
                    onChange={() => setCleaningType("stream")}
                  />
                  <span className="cart-modal__option-body">
                    <span className="cart-modal__option-title">Потокова</span>
                    <span className="cart-modal__option-price">
                      {formatUah(availability.streamPrice)} за одиницю
                    </span>
                  </span>
                </label>
              )}

              {availability.hasIndividual && (
                <label
                  className={`cart-modal__option ${cleaningType === "individual" ? "cart-modal__option--active" : ""}`}
                >
                  <input
                    type="radio"
                    name="cleaning_type"
                    value="individual"
                    checked={cleaningType === "individual"}
                    onChange={() => setCleaningType("individual")}
                  />
                  <span className="cart-modal__option-body">
                    <span className="cart-modal__option-title">Індивідуальна</span>
                    <span className="cart-modal__option-price">
                      {formatUah(availability.individualPrice ?? 0)} за одиницю
                    </span>
                  </span>
                </label>
              )}
            </div>
          ) : (
            <div className="cart-modal__single-type">
              <span className="cart-modal__single-type-label">
                {availability.hasIndividual ? "Індивідуальна" : "Потокова"}
              </span>
              <span className="cart-modal__single-type-price">{formatUah(unitPrice)} за одиницю</span>
            </div>
          )}
        </div>

        <div className="cart-modal__section">
          <p className="cart-modal__label">Кількість</p>
          <div className="cart-modal__stepper">
            <button
              type="button"
              onClick={() => setQuantity((q) => Math.max(1, q - 1))}
              className="cart-modal__stepper-btn"
              aria-label="Зменшити"
            >
              −
            </button>
            <span className="cart-modal__stepper-value">{quantity}</span>
            <button
              type="button"
              onClick={() => setQuantity((q) => q + 1)}
              className="cart-modal__stepper-btn"
              aria-label="Збільшити"
            >
              +
            </button>
          </div>
        </div>

        <div className="cart-modal__total">
          <span className="cart-modal__total-label">Разом:</span>
          <span className="cart-modal__total-value">{formatUah(total)}</span>
        </div>

        {error && <p className="cart-modal__error">{error}</p>}

        <div className="cart-modal__actions">
          <button type="button" onClick={onClose} className="cart-modal__btn cart-modal__btn--ghost">
            Скасувати
          </button>
          <button
            type="button"
            onClick={() => void handleSubmit()}
            disabled={submitting || unitPrice <= 0}
            className="cart-modal__btn cart-modal__btn--primary"
          >
            {submitting ? "Додаємо…" : "Додати"}
          </button>
        </div>
      </div>
    </div>
  );
}
