type Props = {
  open: boolean;
};

export default function OrderLoadingModal({ open }: Props) {
  if (!open) return null;

  return (
    <div className="order-loading-overlay" role="dialog" aria-modal="true" aria-label="Оформлення замовлення">
      <div className="order-loading-modal">
        <div className="order-loading-modal__mascot" aria-hidden>
          🦝
        </div>
        <h3 className="order-loading-modal__title">Очікуйте…</h3>
        <p className="order-loading-modal__text">
          Наші єнотики готують ваше замовлення до відправки!
        </p>
        <div className="order-loading-modal__bar">
          <div className="order-loading-modal__bar-fill" />
        </div>
      </div>
    </div>
  );
}
