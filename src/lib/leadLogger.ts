/**
 * Lead / form activity logger.
 * - Local ring buffer (survives reloads in the same browser)
 * - Fire-and-forget POST to /api/lead-log so the business can recover contacts
 *   even when Telegram / main form endpoints fail
 */

export type LeadLogStage = "attempt" | "success" | "error" | "validation" | "info";

export type LeadLogEvent = {
  id: string;
  ts: string;
  form: string;
  stage: LeadLogStage;
  /** Client contact / form fields — kept for recovery */
  payload: Record<string, unknown>;
  error?: string | null;
  httpStatus?: number | null;
  href: string;
  referrer: string;
  userAgent: string;
};

const STORAGE_KEY = "enot24:lead-logs:v1";
const MAX_LOCAL = 150;

function leadLogApiUrl(path: string): string {
  const base = (import.meta.env.VITE_API_URL ?? "").replace(/\/$/, "");
  const normalized = path.startsWith("/") ? path : `/${path}`;
  return `${base}${normalized}`;
}

function uid(): string {
  return `${Date.now().toString(36)}-${Math.random().toString(36).slice(2, 9)}`;
}

function safePayload(payload: Record<string, unknown>): Record<string, unknown> {
  const out: Record<string, unknown> = {};
  for (const [k, v] of Object.entries(payload)) {
    if (v === undefined) continue;
    if (typeof v === "string") out[k] = v.slice(0, 2000);
    else if (typeof v === "number" || typeof v === "boolean" || v === null) out[k] = v;
    else {
      try {
        out[k] = JSON.parse(JSON.stringify(v));
      } catch {
        out[k] = String(v).slice(0, 500);
      }
    }
  }
  return out;
}

function readLocal(): LeadLogEvent[] {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (!raw) return [];
    const parsed = JSON.parse(raw) as LeadLogEvent[];
    return Array.isArray(parsed) ? parsed : [];
  } catch {
    return [];
  }
}

function writeLocal(events: LeadLogEvent[]) {
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(events.slice(-MAX_LOCAL)));
  } catch {
    // quota / private mode — ignore
  }
}

function pushLocal(event: LeadLogEvent) {
  writeLocal([...readLocal(), event]);
}

function shipToServer(event: LeadLogEvent) {
  const body = JSON.stringify(event);
  const url = leadLogApiUrl("/api/lead-log");

  // sendBeacon + application/json triggers CORS preflight (which beacon can't do).
  // text/plain is a "simple" content-type and works cross-origin.
  try {
    if (typeof navigator !== "undefined" && typeof navigator.sendBeacon === "function") {
      const blob = new Blob([body], { type: "text/plain;charset=UTF-8" });
      if (navigator.sendBeacon(url, blob)) return;
    }
  } catch {
    // fall through to fetch
  }

  void fetch(url, {
    method: "POST",
    headers: {
      Accept: "application/json",
      "Content-Type": "text/plain;charset=UTF-8",
      "X-Requested-With": "XMLHttpRequest",
    },
    body,
    credentials: "omit",
    keepalive: true,
  }).catch(() => {
    // never block UX
  });
}

/**
 * Log a form lifecycle event. Safe to call from anywhere — never throws.
 */
export function logLeadEvent(
  form: string,
  stage: LeadLogStage,
  payload: Record<string, unknown> = {},
  extra?: { error?: string | null; httpStatus?: number | null },
): LeadLogEvent {
  const event: LeadLogEvent = {
    id: uid(),
    ts: new Date().toISOString(),
    form,
    stage,
    payload: safePayload(payload),
    error: extra?.error ?? null,
    httpStatus: extra?.httpStatus ?? null,
    href: typeof window !== "undefined" ? window.location.href : "",
    referrer: typeof document !== "undefined" ? document.referrer : "",
    userAgent: typeof navigator !== "undefined" ? navigator.userAgent : "",
  };

  try {
    const label = `[ENOT lead] ${form} · ${stage}`;
    if (stage === "error" || stage === "validation") {
      console.warn(label, event);
    } else {
      console.info(label, event);
    }
  } catch {
    // ignore
  }

  pushLocal(event);
  shipToServer(event);
  return event;
}

export function getLocalLeadLogs(): LeadLogEvent[] {
  return readLocal();
}

export function clearLocalLeadLogs(): void {
  try {
    localStorage.removeItem(STORAGE_KEY);
  } catch {
    // ignore
  }
}

/** DevTools: window.__enotLeadLogs() / window.__enotClearLeadLogs() */
export function installLeadLogDebugGlobals(): void {
  if (typeof window === "undefined") return;
  const w = window as Window & {
    __enotLeadLogs?: () => LeadLogEvent[];
    __enotClearLeadLogs?: () => void;
  };
  w.__enotLeadLogs = () => getLocalLeadLogs();
  w.__enotClearLeadLogs = () => clearLocalLeadLogs();
}
