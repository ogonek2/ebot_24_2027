const DEFAULT_TTL_MS = 10 * 60 * 1000;
const STORAGE_PREFIX = "enot_cache:";

type CacheEntry<T> = {
  data: T;
  ts: number;
};

const store = new Map<string, CacheEntry<unknown>>();
const inflight = new Map<string, Promise<unknown>>();

function readStorage<T>(key: string, ttl: number): T | null {
  if (typeof sessionStorage === "undefined") return null;
  try {
    const raw = sessionStorage.getItem(STORAGE_PREFIX + key);
    if (!raw) return null;
    const entry = JSON.parse(raw) as CacheEntry<T>;
    if (Date.now() - entry.ts > ttl) {
      sessionStorage.removeItem(STORAGE_PREFIX + key);
      return null;
    }
    return entry.data;
  } catch {
    return null;
  }
}

function writeStorage<T>(key: string, entry: CacheEntry<T>): void {
  if (typeof sessionStorage === "undefined") return;
  try {
    sessionStorage.setItem(STORAGE_PREFIX + key, JSON.stringify(entry));
  } catch {
    /* quota exceeded — memory cache still works */
  }
}

export function getCached<T>(key: string, ttl = DEFAULT_TTL_MS): T | null {
  const entry = store.get(key);
  if (entry && Date.now() - entry.ts <= ttl) {
    return entry.data as T;
  }

  const fromStorage = readStorage<T>(key, ttl);
  if (fromStorage !== null) {
    store.set(key, { data: fromStorage, ts: Date.now() });
    return fromStorage;
  }

  return null;
}

export function setCached<T>(key: string, data: T): void {
  const entry: CacheEntry<T> = { data, ts: Date.now() };
  store.set(key, entry);
  writeStorage(key, entry);
}

export function isCachedFresh(key: string, ttl = DEFAULT_TTL_MS): boolean {
  return getCached(key, ttl) !== null;
}

export async function cachedFetch<T>(
  key: string,
  fetcher: () => Promise<T>,
  ttl = DEFAULT_TTL_MS,
): Promise<T> {
  const hit = getCached<T>(key, ttl);
  if (hit !== null) return hit;

  const pending = inflight.get(key);
  if (pending) return pending as Promise<T>;

  const promise = fetcher()
    .then((data) => {
      setCached(key, data);
      inflight.delete(key);
      return data;
    })
    .catch((err) => {
      inflight.delete(key);
      throw err;
    });

  inflight.set(key, promise);
  return promise;
}

export function prefetch<T>(key: string, fetcher: () => Promise<T>, ttl = DEFAULT_TTL_MS): void {
  if (isCachedFresh(key, ttl)) return;
  void cachedFetch(key, fetcher, ttl);
}

export const BLOG_CACHE_TTL_MS = 30 * 60 * 1000;
