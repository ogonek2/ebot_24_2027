import { createContext, useCallback, useContext, useEffect, useState, type ReactNode } from "react";
import FeedbackModal from "@/components/FeedbackModal";

type FeedbackContextValue = {
  openFeedback: () => void;
  closeFeedback: () => void;
  isOpen: boolean;
};

const FeedbackContext = createContext<FeedbackContextValue>({
  openFeedback: () => {},
  closeFeedback: () => {},
  isOpen: false,
});

export function FeedbackProvider({ children }: { children: ReactNode }) {
  const [isOpen, setIsOpen] = useState(false);

  const openFeedback = useCallback(() => setIsOpen(true), []);
  const closeFeedback = useCallback(() => setIsOpen(false), []);

  useEffect(() => {
    const handler = () => openFeedback();
    window.addEventListener("enot:open-feedback", handler);
    return () => window.removeEventListener("enot:open-feedback", handler);
  }, [openFeedback]);

  return (
    <FeedbackContext.Provider value={{ openFeedback, closeFeedback, isOpen }}>
      {children}
      {isOpen && <FeedbackModal onClose={closeFeedback} />}
    </FeedbackContext.Provider>
  );
}

export function useFeedback() {
  return useContext(FeedbackContext);
}

export function openFeedbackModal() {
  window.dispatchEvent(new CustomEvent("enot:open-feedback"));
}
