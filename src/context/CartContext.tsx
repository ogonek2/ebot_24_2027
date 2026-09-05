import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from "react";
import { addToCart as apiAddToCart, getCart, type CartItem } from "@/lib/api";
import type { AddToCartTarget } from "@/lib/cartPrices";
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
  refresh: () => Promise<void>;
  openAddModal: (target: AddToCartTarget) => void;
};

const CartContext = createContext<CartContextValue | null>(null);

export function CartProvider({ children }: { children: ReactNode }) {
  const [items, setItems] = useState<CartItem[]>([]);
  const [total, setTotal] = useState(0);
  const [count, setCount] = useState(0);
  const [loading, setLoading] = useState(true);
  const [modalTarget, setModalTarget] = useState<AddToCartTarget | null>(null);
  const [toast, setToast] = useState<CartToastState>(null);

  const refresh = useCallback(async () => {
    try {
      const res = await getCart();
      const nextItems = res.items ?? [];
      setItems(nextItems);
      setTotal(res.total ?? 0);
      setCount(nextItems.reduce((sum, item) => sum + item.quantity, 0));
    } finally {
      setLoading(false);
    }
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

  const handleConfirmAdd = useCallback(
    async (cleaningType: "individual" | "stream", quantity: number) => {
      if (!modalTarget) return;
      await apiAddToCart(modalTarget.serviceId, cleaningType, quantity);
      await refresh();
      showToast(modalTarget.serviceName);
      closeModal();
    },
    [modalTarget, refresh, showToast, closeModal],
  );

  const value = useMemo(
    () => ({ items, total, count, loading, refresh, openAddModal }),
    [items, total, count, loading, refresh, openAddModal],
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
