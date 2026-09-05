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
  error: string | null;
};

const BootstrapContext = createContext<BootstrapContextValue>({
  data: emptyBootstrap,
  loading: true,
  isRefreshing: false,
  error: null,
});

export function BootstrapProvider({ children }: { children: ReactNode }) {
  const location = useLocation();
  const pathnameRef = useRef(location.pathname);
  const [data, setData] = useState<SpaBootstrap>(() => hydrateBootstrapCache());
  const [loading, setLoading] = useState(() => !hasBootstrapData(hydrateBootstrapCache()));
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

    if (cached && hasBootstrapData(cached)) {
      setData((prev) => mergeBootstrap(prev, cached));
      setLoading(false);
      setIsRefreshing(true);
    } else {
      setLoading(true);
    }

    let cancelled = false;

    loadBootstrap(location.pathname)
      .then((fresh) => {
        if (cancelled || pathnameRef.current !== location.pathname) return;
        setData((prev) => mergeBootstrap(prev, fresh));
        setError(null);
      })
      .catch(() => {
        if (cancelled || pathnameRef.current !== location.pathname) return;
        if (!hasBootstrapData(cached ?? emptyBootstrap)) {
          setError("Не вдалося завантажити дані");
        }
      })
      .finally(() => {
        if (cancelled || pathnameRef.current !== location.pathname) return;
        setLoading(false);
        setIsRefreshing(false);
      });

    return () => {
      cancelled = true;
    };
  }, [location.pathname]);

  return (
    <BootstrapContext.Provider value={{ data, loading, isRefreshing, error }}>
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
