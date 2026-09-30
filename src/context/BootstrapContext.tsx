import {
  createContext,
  useContext,
  useEffect,
  useRef,
  useState,
  type ReactNode,
} from "react";
import { useLocation } from "react-router-dom";
import {
  hasBootstrapData,
  hydrateBootstrapCache,
  isBootstrapReady,
  loadBootstrap,
  mergeBootstrap,
  peekBootstrap,
  prefetchBootstrapRoutes,
} from "@/lib/bootstrapLoader";
import { emptyBootstrap, type SpaBootstrap } from "@/lib/bootstrap";

type BootstrapContextValue = {
  data: SpaBootstrap;
  loading: boolean;
  isRefreshing: boolean;
  /** True until paint-ready bootstrap exists for the current route. */
  bootstrapping: boolean;
  error: string | null;
};

const BootstrapContext = createContext<BootstrapContextValue>({
  data: emptyBootstrap,
  loading: true,
  isRefreshing: false,
  bootstrapping: true,
  error: null,
});

const BOOT_TIMEOUT_MS = 15_000;

export function BootstrapProvider({ children }: { children: ReactNode }) {
  const location = useLocation();
  const pathnameRef = useRef(location.pathname);
  const [data, setData] = useState<SpaBootstrap>(() => hydrateBootstrapCache());
  const [loading, setLoading] = useState(true);
  const [isRefreshing, setIsRefreshing] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (hasBootstrapData(data)) {
      prefetchBootstrapRoutes();
    }
  }, [data.categories?.length, data.discounts?.length]);

  useEffect(() => {
    pathnameRef.current = location.pathname;
    const cached = peekBootstrap(location.pathname);
    const cacheReady = Boolean(cached && isBootstrapReady(location.pathname, cached));

    if (cacheReady && cached) {
      setData((prev) => mergeBootstrap(prev, cached));
      setIsRefreshing(true);
      setLoading(false);
    } else {
      setLoading(true);
      setIsRefreshing(false);
    }

    let cancelled = false;
    const timeoutId = cacheReady
      ? 0
      : window.setTimeout(() => {
          if (cancelled || pathnameRef.current !== location.pathname) return;
          setError((prev) => prev ?? "Не вдалося завантажити дані");
          setLoading(false);
          setIsRefreshing(false);
        }, BOOT_TIMEOUT_MS);

    loadBootstrap(location.pathname)
      .then((fresh) => {
        if (cancelled || pathnameRef.current !== location.pathname) return;
        setData((prev) => mergeBootstrap(prev, fresh));
        setError(null);
      })
      .catch(() => {
        if (cancelled || pathnameRef.current !== location.pathname) return;
        if (!isBootstrapReady(location.pathname, cached ?? emptyBootstrap)) {
          setError("Не вдалося завантажити дані");
        }
      })
      .finally(() => {
        if (cancelled || pathnameRef.current !== location.pathname) return;
        setLoading(false);
        setIsRefreshing(false);
        if (timeoutId) window.clearTimeout(timeoutId);
      });

    return () => {
      cancelled = true;
      if (timeoutId) window.clearTimeout(timeoutId);
    };
    // intentionally only pathname — data in timeout is best-effort failsafe
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [location.pathname]);

  const ready = isBootstrapReady(location.pathname, data);
  // Keep full-screen preloader until catalog (etc.) is actually paint-ready.
  // Do not unlock on loading=false alone — incomplete cache used to do that.
  const bootstrapping = !ready && !error;

  return (
    <BootstrapContext.Provider value={{ data, loading, isRefreshing, bootstrapping, error }}>
      {children}
    </BootstrapContext.Provider>
  );
}

export function useBootstrap() {
  return useContext(BootstrapContext).data;
}

export function useBootstrapState() {
  return useContext(BootstrapContext);
}
