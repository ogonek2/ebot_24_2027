import { Link } from "react-router-dom";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faCartShopping } from "@fortawesome/free-solid-svg-icons";
import { ROUTES } from "@/lib/routes";
import { useCartOptional } from "@/context/CartContext";

type Props = {
  className?: string;
  showLabel?: boolean;
  onClick?: () => void;
};

export default function CartButton({ className = "", showLabel = false, onClick }: Props) {
  const cart = useCartOptional();
  const count = cart?.count ?? 0;

  return (
    <Link
      to={ROUTES.cart}
      onClick={onClick}
      className={`cart-nav-btn no-underline ${className}`}
      aria-label={count > 0 ? `Кошик, ${count} позицій` : "Кошик"}
    >
      <span className="cart-nav-btn__icon-wrap">
        <FontAwesomeIcon icon={faCartShopping} className="cart-nav-btn__icon" />
        {count > 0 && <span className="cart-nav-btn__badge">{count > 99 ? "99+" : count}</span>}
      </span>
      {showLabel && <span className="cart-nav-btn__label">Кошик</span>}
    </Link>
  );
}
