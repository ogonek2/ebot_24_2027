import type { ReactNode } from "react";

interface RevealProps {
  children: ReactNode;
  className?: string;
  /** Kept for call-site compatibility; ignored — no entrance animation. */
  delay?: number;
}

/** Passthrough wrapper — content mounts fully visible, no scroll/stagger reveal. */
export default function Reveal({ children, className = "" }: RevealProps) {
  if (className) {
    return <div className={className}>{children}</div>;
  }
  return <>{children}</>;
}
