const positions = new Map<string, number>();

export function saveScrollPosition(key: string, y = window.scrollY) {
  if (!key) return;
  positions.set(key, Math.max(0, y));
}

export function getScrollPosition(key: string): number | null {
  if (!key || !positions.has(key)) return null;
  return positions.get(key) ?? null;
}

export function restoreScrollPosition(y: number, attempts = [0, 50, 120, 250, 450, 800]) {
  const top = Math.max(0, y);
  const apply = () => {
    window.scrollTo({ top, left: 0, behavior: "auto" });
  };

  const timers = attempts.map((delay) => window.setTimeout(apply, delay));
  return () => timers.forEach((id) => window.clearTimeout(id));
}

export function scrollToTopInstant() {
  window.scrollTo({ top: 0, left: 0, behavior: "auto" });
}
