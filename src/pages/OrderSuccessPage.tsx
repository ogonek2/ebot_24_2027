import { useEffect, useState } from "react";
import { Link, useParams } from "react-router-dom";
import Breadcrumbs, { homeCrumb } from "@/components/Breadcrumbs";
import CategoryIcon from "@/components/CategoryIcon";
import { useAppNavigate } from "@/lib/navigation";
import { getLastOrder, invoiceDownloadUrl, type LastOrder } from "@/lib/api";
import { cleaningTypeLabel, formatUah } from "@/lib/cartPrices";
import { ROUTES } from "@/lib/routes";

export default function OrderSuccessPage() {
  const { orderId: paramOrderId } = useParams();
  const { goHome } = useAppNavigate();
  const [order, setOrder] = useState<LastOrder | null>(null);
  const [loading, setLoading] = useState(true);
  const [notFound, setNotFound] = useState(false);

  useEffect(() => {
    let cancelled = false;
    getLastOrder(paramOrderId)
      .then((data) => {
        if (cancelled) return;
        if (!data) {
          setNotFound(true);
        } else {
          setOrder(data);
        }
      })
      .catch(() => {
        if (!cancelled) setNotFound(true);
      })
      .finally(() => {
        if (!cancelled) setLoading(false);
      });
    return () => {
      cancelled = true;
    };
  }, [paramOrderId]);

  if (loading) {
    return (
      <div className="min-h-screen pt-24 pb-20 site-container text-center text-[#1A1A2E]/45">
        Завантаження…
      </div>
    );
  }

  if (notFound || !order) {
    return (
      <div className="min-h-screen pt-24 pb-20 site-container text-center">
        <h1 className="text-section mb-4">Замовлення не знайдено</h1>
        <p className="text-[15px] text-[#1A1A2E]/55 mb-8">
          Можливо, сесія закінчилась. Перевірте SMS або зв&apos;яжіться з нами.
        </p>
        <Link to={ROUTES.services} className="btn-primary px-8 py-3.5 no-underline inline-block">
          До послуг
        </Link>
      </div>
    );
  }

  return (
    <div className="min-h-screen pt-24 pb-20">
      <div className="site-container max-w-4xl">
        <Breadcrumbs
          items={[
            homeCrumb(),
            { name: "Кошик", url: ROUTES.cart },
            { name: "Дякуємо за замовлення" },
          ]}
        />

        <div className="text-center mb-10 mt-6">
          <div className="w-24 h-24 glass rounded-full flex items-center justify-center mx-auto mb-6 overflow-hidden anim-scale-in">
            <span className="text-[48px] font-bold text-[#1A1A2E]">👍</span>
          </div>
          <h1 className="text-section text-[#1A1A2E] mb-3">Дякуємо за замовлення!</h1>
          <p className="text-[16px] text-[#1A1A2E]/60 mb-2">
            Наші єнотики вже обробляють ваше замовлення!
          </p>
          <p className="text-[15px] text-[#1A1A2E]/55">
            Ми зв&apos;яжемося з вами найближчим часом для підтвердження деталей.
          </p>
        </div>

        <div className="glass-strong rounded-[28px] p-6 md:p-8 mb-8">
          <div className="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-8 pb-6 border-b border-[#f3eeeb]">
            <div>
              <h2 className="font-bold text-[22px] mb-2">Інвойс</h2>
              <p className="text-[14px] text-[#1A1A2E]/60">
                Номер замовлення:{" "}
                <strong className="text-[#f97171]">{order.id}</strong>
              </p>
              <p className="text-[13px] text-[#1A1A2E]/45 mt-1">Дата: {order.created_at}</p>
            </div>
          </div>

          <div className="grid md:grid-cols-2 gap-6 mb-8">
            <div>
              <h3 className="font-bold text-[16px] mb-3">Інформація про замовника</h3>
              <p className="text-[14px] text-[#1A1A2E]/65">
                <span className="font-semibold">Ім&apos;я:</span> {order.name}
              </p>
              <p className="text-[14px] text-[#1A1A2E]/65 mt-1">
                <span className="font-semibold">Телефон:</span> {order.phone}
              </p>
            </div>
            <div>
              <h3 className="font-bold text-[16px] mb-3">Спосіб отримання</h3>
              {order.delivery_method === "self" ? (
                <>
                  <p className="text-[14px] font-semibold text-[#1A1A2E]/75">Самовивіз</p>
                  {order.pickup_location && (
                    <p className="text-[14px] text-[#1A1A2E]/65 mt-1">
                      {order.pickup_location.street}, {order.pickup_location.city}
                      {order.pickup_location.working_hours && (
                        <span className="block text-[12px] text-[#1A1A2E]/45 mt-0.5">
                          ({order.pickup_location.working_hours})
                        </span>
                      )}
                    </p>
                  )}
                </>
              ) : (
                <>
                  <p className="text-[14px] font-semibold text-[#1A1A2E]/75">Кур&apos;єрська доставка</p>
                  {order.delivery_address && (
                    <p className="text-[14px] text-[#1A1A2E]/65 mt-1">{order.delivery_address}</p>
                  )}
                </>
              )}
            </div>
          </div>

          <h3 className="font-bold text-[16px] mb-4">Склад замовлення</h3>
          <div className="overflow-x-auto">
            <table className="cart-invoice-table w-full text-[14px]">
              <thead>
                <tr>
                  <th className="text-left">Послуга</th>
                  <th className="text-center">Тип</th>
                  <th className="text-center">Кількість</th>
                  <th className="text-right">Ціна</th>
                  <th className="text-right">Сума</th>
                </tr>
              </thead>
              <tbody>
                {order.items.map((item) => (
                  <tr key={`${item.service_id}-${item.cleaning_type}`}>
                    <td>
                      <div className="font-semibold">{item.service_name}</div>
                      <div className="text-[12px] text-[#1A1A2E]/45">{item.category_name}</div>
                    </td>
                    <td className="text-center">{cleaningTypeLabel(item.cleaning_type)}</td>
                    <td className="text-center">{item.quantity}</td>
                    <td className="text-right">{formatUah(item.price)}</td>
                    <td className="text-right font-semibold">{formatUah(item.total)}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          <div className="mt-6 pt-6 border-t border-[#f3eeeb] flex justify-between items-center">
            <span className="font-bold text-[18px]">Загальна сума:</span>
            <span className="font-black text-[28px] text-[#f97171]">{formatUah(order.total)}</span>
          </div>
        </div>

        <div className="flex flex-wrap gap-3 justify-center">
          <button type="button" onClick={goHome} className="btn-primary px-8 py-3.5">
            На головну
          </button>
          <Link to={ROUTES.services} className="btn-outline px-8 py-3.5 no-underline">
            До послуг
          </Link>
        </div>
      </div>
    </div>
  );
}
