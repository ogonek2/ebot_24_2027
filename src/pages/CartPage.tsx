import { useEffect, useState } from "react";
import { Link, Navigate, useNavigate } from "react-router-dom";
import Breadcrumbs, { homeCrumb } from "@/components/Breadcrumbs";
import OrderLoadingModal from "@/components/cart/OrderLoadingModal";
import PickupLocationSelect, { type PickupLocation } from "@/components/cart/PickupLocationSelect";
import CheckoutSkeleton from "@/components/skeleton/CheckoutSkeleton";
import { useCart } from "@/context/CartContext";
import {
  fetchPickupLocationsCached,
  removeFromCart,
  submitOrder,
  updateCart,
} from "@/lib/api";
import { cleaningTypeLabel, formatUah } from "@/lib/cartPrices";
import { ROUTES } from "@/lib/routes";

export default function CartPage() {
  const navigate = useNavigate();
  const { items, total, loading, refresh } = useCart();
  const [locations, setLocations] = useState<PickupLocation[]>([]);
  const [locationsReady, setLocationsReady] = useState(false);
  const [submitting, setSubmitting] = useState(false);
  const [showLoadingModal, setShowLoadingModal] = useState(false);
  const [formError, setFormError] = useState<string | null>(null);
  const [form, setForm] = useState({
    name: "",
    phone: "",
    delivery_method: "self" as "self" | "courier",
    pickup_location_id: null as number | null,
    delivery_address: "",
  });

  useEffect(() => {
    fetchPickupLocationsCached()
      .then((res) => setLocations(res.locations ?? []))
      .finally(() => setLocationsReady(true));
  }, []);

  const handleQtyChange = async (key: string, quantity: number) => {
    if (quantity < 1) return;
    await updateCart(key, quantity);
    await refresh();
  };

  const handleRemove = async (key: string) => {
    await removeFromCart(key);
    await refresh();
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setFormError(null);

    if (form.delivery_method === "self" && !form.pickup_location_id) {
      setFormError("Будь ласка, оберіть приймальний пункт");
      return;
    }
    if (form.delivery_method === "courier" && !form.delivery_address.trim()) {
      setFormError("Будь ласка, введіть адресу доставки");
      return;
    }

    setSubmitting(true);
    setShowLoadingModal(true);

    try {
      const payload: Record<string, unknown> = {
        name: form.name.trim(),
        phone: form.phone.trim(),
        delivery_method: form.delivery_method,
      };
      if (form.delivery_method === "self") {
        payload.pickup_location_id = form.pickup_location_id;
      } else {
        payload.delivery_address = form.delivery_address.trim();
      }

      const res = await submitOrder(payload);
      if (res.success) {
        await refresh();
        window.setTimeout(() => {
          setShowLoadingModal(false);
          if (res.order_id) {
            navigate(`${ROUTES.orderSuccess}/${res.order_id}`);
          } else {
            navigate(ROUTES.orderSuccess);
          }
        }, 1400);
      } else {
        setShowLoadingModal(false);
        setFormError(res.message ?? "Помилка оформлення замовлення");
        setSubmitting(false);
      }
    } catch {
      setShowLoadingModal(false);
      setFormError("Помилка оформлення замовлення. Спробуйте ще раз.");
      setSubmitting(false);
    }
  };

  if (loading || !locationsReady) {
    return <CheckoutSkeleton />;
  }

  if (items.length === 0) {
    return (
      <div className="min-h-screen pt-24 pb-20 site-container text-center">
        <Breadcrumbs items={[homeCrumb(), { name: "Кошик" }]} />
        <h1 className="text-section mb-4 mt-6">Кошик порожній</h1>
        <p className="text-[15px] text-[#1A1A2E]/55 mb-8 max-w-md mx-auto">
          Додайте послуги з прайсу — і оформіть замовлення за кілька хвилин.
        </p>
        <Link to={ROUTES.services} className="btn-primary px-8 py-3.5 no-underline inline-block">
          До послуг
        </Link>
      </div>
    );
  }

  return (
    <div className="min-h-screen pt-24 pb-20">
      <OrderLoadingModal open={showLoadingModal} />
      <div className="site-container">
        <Breadcrumbs
          items={[
            homeCrumb(),
            { name: "Послуги", url: ROUTES.services },
            { name: "Кошик" },
          ]}
        />
        <h1 className="text-section text-[#1A1A2E] mb-8">Оформлення замовлення</h1>

        <div className="grid lg:grid-cols-3 gap-8">
          <form onSubmit={(e) => void handleSubmit(e)} className="lg:col-span-2 glass-strong rounded-[28px] p-6 md:p-8 space-y-6">
            <div>
              <h2 className="font-bold text-[18px] mb-4">Контактна інформація</h2>
              <div className="space-y-4">
                <div>
                  <label htmlFor="checkout-name" className="cart-form-label">
                    Ваше ім&apos;я <span className="text-[#f97171]">*</span>
                  </label>
                  <input
                    id="checkout-name"
                    required
                    placeholder="Іван Іванов"
                    value={form.name}
                    onChange={(e) => setForm({ ...form, name: e.target.value })}
                    className="cart-form-input"
                  />
                </div>
                <div>
                  <label htmlFor="checkout-phone" className="cart-form-label">
                    Номер телефону <span className="text-[#f97171]">*</span>
                  </label>
                  <input
                    id="checkout-phone"
                    required
                    type="tel"
                    placeholder="+380991234567"
                    value={form.phone}
                    onChange={(e) => setForm({ ...form, phone: e.target.value })}
                    className="cart-form-input"
                  />
                </div>
              </div>
            </div>

            <div>
              <h2 className="font-bold text-[18px] mb-4">Спосіб отримання</h2>
              <div className="space-y-3">
                <label className={`cart-delivery-option ${form.delivery_method === "self" ? "cart-delivery-option--active" : ""}`}>
                  <input
                    type="radio"
                    name="delivery_method"
                    checked={form.delivery_method === "self"}
                    onChange={() => setForm({ ...form, delivery_method: "self" })}
                  />
                  <span>
                    <span className="cart-delivery-option__title">Занесу сам</span>
                    <span className="cart-delivery-option__sub">Самовивіз з приймального пункту</span>
                  </span>
                </label>

                <label className={`cart-delivery-option ${form.delivery_method === "courier" ? "cart-delivery-option--active" : ""}`}>
                  <input
                    type="radio"
                    name="delivery_method"
                    checked={form.delivery_method === "courier"}
                    onChange={() => setForm({ ...form, delivery_method: "courier" })}
                  />
                  <span>
                    <span className="cart-delivery-option__title">Кур&apos;єрська доставка</span>
                    <span className="cart-delivery-option__sub">Доставка за вашою адресою</span>
                  </span>
                </label>
              </div>
            </div>

            {form.delivery_method === "self" && (
              <div>
                <label className="cart-form-label">
                  Приймальний пункт <span className="text-[#f97171]">*</span>
                </label>
                <PickupLocationSelect
                  locations={locations}
                  value={form.pickup_location_id}
                  onChange={(id) => setForm({ ...form, pickup_location_id: id })}
                  required
                />
              </div>
            )}

            {form.delivery_method === "courier" && (
              <div>
                <label htmlFor="checkout-address" className="cart-form-label">
                  Адреса доставки <span className="text-[#f97171]">*</span>
                </label>
                <textarea
                  id="checkout-address"
                  required
                  rows={3}
                  placeholder="Введіть повну адресу доставки…"
                  value={form.delivery_address}
                  onChange={(e) => setForm({ ...form, delivery_address: e.target.value })}
                  className="cart-form-input cart-form-textarea"
                />
              </div>
            )}

            {formError && <p className="cart-form-error">{formError}</p>}

            <button type="submit" disabled={submitting} className="btn-primary w-full py-4">
              {submitting ? "Оформлюємо…" : "Оформити замовлення"}
            </button>
          </form>

          <aside className="glass-strong rounded-[28px] p-6 h-fit lg:sticky lg:top-28">
            <h2 className="font-bold text-[18px] mb-4">Ваше замовлення</h2>
            <ul className="space-y-4 mb-6">
              {items.map((item) => (
                <li key={item.key} className="cart-line">
                  <div className="cart-line__head">
                    <div>
                      <div className="font-semibold text-[14px]">{item.service_name}</div>
                      <div className="text-[12px] text-[#1A1A2E]/45">{item.category_name}</div>
                      <div className="text-[12px] text-[#1A1A2E]/45">
                        Тип: {cleaningTypeLabel(item.cleaning_type)}
                      </div>
                    </div>
                    <button
                      type="button"
                      onClick={() => void handleRemove(item.key)}
                      className="cart-line__remove"
                    >
                      Видалити
                    </button>
                  </div>
                  <div className="cart-line__foot">
                    <div className="cart-modal__stepper cart-modal__stepper--compact">
                      <button
                        type="button"
                        onClick={() => void handleQtyChange(item.key, item.quantity - 1)}
                        className="cart-modal__stepper-btn"
                        disabled={item.quantity <= 1}
                      >
                        −
                      </button>
                      <span className="cart-modal__stepper-value">{item.quantity}</span>
                      <button
                        type="button"
                        onClick={() => void handleQtyChange(item.key, item.quantity + 1)}
                        className="cart-modal__stepper-btn"
                      >
                        +
                      </button>
                    </div>
                    <div className="text-right">
                      <div className="text-[12px] text-[#1A1A2E]/45">
                        {formatUah(item.price)} × {item.quantity}
                      </div>
                      <div className="font-bold text-[#f97171]">{formatUah(item.total)}</div>
                    </div>
                  </div>
                </li>
              ))}
            </ul>
            <div className="border-t border-[#f3eeeb] pt-4 flex justify-between items-center">
              <span className="text-[16px] font-semibold text-[#1A1A2E]/70">Разом:</span>
              <span className="text-[28px] font-black text-[#f97171]">{formatUah(total)}</span>
            </div>
          </aside>
        </div>
      </div>
    </div>
  );
}

/** Legacy checkout URL → cart page */
export function CheckoutRedirect() {
  return <Navigate to={ROUTES.cart} replace />;
}
