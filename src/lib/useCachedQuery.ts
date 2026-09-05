import { useEffect, useState } from "react";
import { cachedFetch, getCached } from "./cache";

export function useCachedQuery<T>(key: string, fetcher: () => Promise<T>) {
  const [data, setData] = useState<T | null>(() => getCached<T>(key));
  const [loading, setLoading] = useState(() => getCached<T>(key) === null);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    let cancelled = false;
    const cached = getCached<T>(key);
    if (cached) {
      setData(cached);
      setLoading(false);
    } else {
      setLoading(true);
    }

    cachedFetch(key, fetcher)
      .then((result) => {
        if (!cancelled) {
          setData(result);
          setError(null);
        }
      })
      .catch((err: unknown) => {
        if (!cancelled && !cached) {
          setError(err instanceof Error ? err.message : "Помилка завантаження");
        }
      })
      .finally(() => {
        if (!cancelled) setLoading(false);
      });

    return () => {
      cancelled = true;
    };
  }, [key]);

  return { data, loading: loading && !data, error };
}
