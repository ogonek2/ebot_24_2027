import { useEffect, useRef, useState, type ReactNode } from "react";

interface RevealProps {
  children: ReactNode;
  className?: string;
  delay?: number;
}

/**
 * Light scroll polish only. Content stays visible from the first paint —
 * never gate the page behind opacity:0 / IntersectionObserver (that caused
 * "blocks appear only after scrolling to the end").
 */
export default function Reveal({ children, className = "", delay = 0 }: RevealProps) {
  const ref = useRef<HTMLDivElement>(null);
  const [enhanced, setEnhanced] = useState(false);

  useEffect(() => {
    const el = ref.current;
    if (!el) return;

    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
      setEnhanced(true);
      return;
    }

    const io = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          setEnhanced(true);
          io.disconnect();
        }
      },
      { threshold: 0.01, rootMargin: "20% 0px" },
    );

    io.observe(el);
    // Failsafe: never leave content without the polish class forever
    const t = window.setTimeout(() => setEnhanced(true), 800);
    return () => {
      io.disconnect();
      window.clearTimeout(t);
    };
  }, []);

  return (
    <div
      ref={ref}
      className={className}
      style={{
        opacity: 1,
        transform: enhanced ? "none" : "translateY(12px)",
        transition: enhanced
          ? `transform 0.45s cubic-bezier(0.22, 1, 0.36, 1) ${delay}ms`
          : undefined,
        willChange: enhanced ? "auto" : "transform",
      }}
    >
      {children}
    </div>
  );
}
