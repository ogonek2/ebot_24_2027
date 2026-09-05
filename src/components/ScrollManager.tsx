import { useEffect, useLayoutEffect, useRef } from "react";
import { useLocation, useNavigationType } from "react-router-dom";
import {
  getScrollPosition,
  restoreScrollPosition,
  saveScrollPosition,
  scrollToTopInstant,
} from "@/lib/scrollRestore";

function scrollToHash(hash: string) {
  const id = hash.replace(/^#/, "");
  if (!id) return;
  requestAnimationFrame(() => {
    document.getElementById(id)?.scrollIntoView({ behavior: "smooth" });
  });
}

/**
 * Remembers window scroll per history entry and restores it on browser Back/Forward.
 * Fresh navigations (link clicks) still start at the top.
 */
export default function ScrollManager() {
  const location = useLocation();
  const navigationType = useNavigationType();
  const activeKeyRef = useRef(location.key);

  useEffect(() => {
    if ("scrollRestoration" in history) {
      history.scrollRestoration = "manual";
    }
  }, []);

  // Keep the current history entry's scroll fresh while the user moves.
  useEffect(() => {
    activeKeyRef.current = location.key;

    const onScroll = () => {
      saveScrollPosition(activeKeyRef.current, window.scrollY);
    };

    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, [location.key]);

  useLayoutEffect(() => {
    const prevKey = activeKeyRef.current;
    if (prevKey && prevKey !== location.key) {
      saveScrollPosition(prevKey, window.scrollY);
    }
    activeKeyRef.current = location.key;

    if (location.hash) {
      scrollToHash(location.hash);
      return;
    }

    if (navigationType === "POP") {
      const y = getScrollPosition(location.key) ?? 0;
      return restoreScrollPosition(y);
    }

    scrollToTopInstant();
    saveScrollPosition(location.key, 0);
  }, [location.key, location.pathname, location.search, location.hash, navigationType]);

  return null;
}
