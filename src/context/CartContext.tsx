import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from "react";
import type { CartItem } from "@/lib/api";
import { isRepairCartTarget, type AddToCartTarget } from "@/lib/cartPrices";
import {
  cartTotals,
  clearLocalCart,
  loadLocalCart,
  makeRepairKey,
  makeServiceKey,
  saveLocalCart,
  type LocalCartLine,
} from "@/lib/localCart";
import AddToCartModal from "@/components/cart/AddToCartModal";
import CartToast from "@/components/cart/CartToast";

type CartToastState = {
  message: string;
  serviceName: string;
} | null;

type CartContextValue = {
  items: CartItem[];
  total: number;
  count: number;
  loading: boolean;
  /** Re-read cart from localStorage */
  refresh: () => Promise<void>;
  openAddModal: (target: AddToCartTarget) => void;
  updateQuantity: (key: string, quantity: number) => void;
  removeItem: (key: string) => void;
  clear: () => void;
  /** Raw lines for checkout payload */
  getCheckoutLines: () => LocalCartLine[];
};

const CartContext = createContext<CartContextValue | null>(null);

function syncState(lines: LocalCartLine[]) {
  const { items, total, count } = cartTotals(lines);
  return { items, total, count, lines };
}

export function CartProvider({ children }: { children: ReactNode }) {
  const [lines, setLines] = useState<LocalCartLine[]>([]);
  const [items, setItems] = useState<CartItem[]>([]);
  const [total, setTotal] = useState(0);
  const [count, setCount] = useState(0);
  const [loading, setLoading] = useState(true);
  const [modalTarget, setModalTarget] = useState<AddToCartTarget | null>(null);
  const [toast, setToast] = useState<CartToastState>(null);

  const applyLines = useCallback((next: LocalCartLine[]) => {
    saveLocalCart(next);
    const synced = syncState(next);
    setLines(synced.lines);
    setItems(synced.items);
    setTotal(synced.total);
    setCount(synced.count);
  }, []);

  const refresh = useCallback(async () => {
    const loaded = loadLocalCart();
    const synced = syncState(loaded);
    setLines(synced.lines);
    setItems(synced.items);
    setTotal(synced.total);
    setCount(synced.count);
    setLoading(false);
  }, []);

  useEffect(() => {
    void refresh();
  }, [refresh]);

  const openAddModal = useCallback((target: AddToCartTarget) => {
    setModalTarget(target);
  }, []);

  const closeModal = useCallback(() => {
    setModalTarget(null);
  }, []);

  const showToast = useCallback((serviceName: string) => {
    setToast({ message: "Додано до кошика", serviceName });
    window.setTimeout(() => setToast(null), 3200);
  }, []);

  const updateQuantity = useCallback(
    (key: string, quantity: number) => {
      if (quantity < 1) return;
      applyLines(lines.map((l) => (l.key === key ? { ...l, quantity } : l)));
    },
    [applyLines, lines],
  );

  const removeItem = useCallback(
    (key: string) => {
      applyLines(lines.filter((l) => l.key !== key));
    },
    [applyLines, lines],
  );

  const clear = useCallback(() => {
    clearLocalCart();
    setLines([]);
    setItems([]);
    setTotal(0);
    setCount(0);
  }, []);

  const getCheckoutLines = useCallback(() => loadLocalCart(), []);

  const handleConfirmAdd = useCallback(
    async (cleaningType: "individual" | "stream" | "repair", quantity: number) => {
      if (!modalTarget) return;

      const current = loadLocalCart();
      let next = [...current];

      if (isRepairCartTarget(modalTarget) && modalTarget.repairItemId) {
        const key = makeRepairKey(modalTarget.repairItemId);
        const existing = next.find((l) => l.key === key);
        if (existing) {
          next = next.map((l) =>
            l.key === key ? { ...l, quantity: l.quantity + quantity } : l,
          );
        } else {
          next.push({
            key,
            type: "repair",
            service_id: null,
            repair_item_id: modalTarget.repairItemId,
            cleaning_type: "repair",
            quantity,
            service_name: modalTarget.serviceName,
            category_name: "Ремонт взуття",
            price: modalTarget.streamPrice,
            price_from: Boolean(modalTarget.priceFrom),
          });
        }
      } else if (modalTarget.serviceId) {
        const type = cleaningType === "repair" ? "stream" : cleaningType;
        const key = makeServiceKey(modalTarget.serviceId, type);
        const unit =
          type === "individual" && modalTarget.individualPrice != null
            ? modalTarget.individualPrice
            : modalTarget.streamPrice;
        if (!(unit > 0)) {
          throw new Error("Ціна недоступна для цієї послуги");
        }
        const existing = next.find((l) => l.key === key);
        if (existing) {
          next = next.map((l) =>
            l.key === key ? { ...l, quantity: l.quantity + quantity } : l,
          );
        } else {
          next.push({
            key,
            type: "service",
            service_id: modalTarget.serviceId,
            repair_item_id: null,
            cleaning_type: type,
            quantity,
            service_name: modalTarget.serviceName,
            category_name: "Послуга",
            price: unit,
            price_from: false,
          });
        }
      } else {
        throw new Error("Не вдалося додати до кошика");
      }

      applyLines(next);
      showToast(modalTarget.serviceName);
      closeModal();
    },
    [modalTarget, applyLines, showToast, closeModal],
  );

  const value = useMemo(
    () => ({
      items,
      total,
      count,
      loading,
      refresh,
      openAddModal,
      updateQuantity,
      removeItem,
      clear,
      getCheckoutLines,
    }),
    [
      items,
      total,
      count,
      loading,
      refresh,
      openAddModal,
      updateQuantity,
      removeItem,
      clear,
      getCheckoutLines,
    ],
  );

  return (
    <CartContext.Provider value={value}>
      {children}
      <AddToCartModal
        open={modalTarget !== null}
        target={modalTarget}
        onClose={closeModal}
        onConfirm={handleConfirmAdd}
      />
      <CartToast toast={toast} onDismiss={() => setToast(null)} />
    </CartContext.Provider>
  );
}

export function useCart() {
  const ctx = useContext(CartContext);
  if (!ctx) throw new Error("useCart must be used within CartProvider");
  return ctx;
}

export function useCartOptional() {
  return useContext(CartContext);
}
