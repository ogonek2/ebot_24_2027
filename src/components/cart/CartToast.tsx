import { Link } from "react-router-dom";
import { ROUTES } from "@/lib/routes";

type Props = {
  toast: { message: string; serviceName: string } | null;
  onDismiss: () => void;
};

export default function CartToast({ toast, onDismiss }: Props) {
  if (!toast) return null;

  return (
    <div className="cart-toast" role="status" aria-live="polite">
      <div className="cart-toast__inner">
        <span className="cart-toast__icon" aria-hidden>
          ✓
        </span>
        <div className="cart-toast__text">
          <strong>{toast.message}</strong>
          <span>{toast.serviceName}</span>
        </div>
        <Link to={ROUTES.cart} className="cart-toast__link no-underline" onClick={onDismiss}>
          Переглянути
        </Link>
        <button type="button" onClick={onDismiss} className="cart-toast__dismiss" aria-label="Закрити">
          ✕
        </button>
      </div>
    </div>
  );
}
