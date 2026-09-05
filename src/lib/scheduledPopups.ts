export type ScheduledPopupModal = {
  id: number;
  desktop_image_url: string | null;
  mobile_image_url: string | null;
  form_title: string;
  form_subtitle: string;
  delay_before_show_seconds: number;
  seconds_after_close_until_next: number;
};

export type ScheduledPopupResponse = {
  date: string;
  modals: ScheduledPopupModal[];
};

const STORAGE_KEY = "scheduled_popup_state_v1";

type PopupState = {
  date: string;
  shownIds: number[];
};

export function todayKey(): string {
  return new Date().toISOString().slice(0, 10);
}

export function loadPopupState(): PopupState {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (!raw) return { date: todayKey(), shownIds: [] };
    const parsed = JSON.parse(raw) as PopupState;
    if (parsed.date !== todayKey()) return { date: todayKey(), shownIds: [] };
    return {
      date: parsed.date,
      shownIds: Array.isArray(parsed.shownIds) ? parsed.shownIds : [],
    };
  } catch {
    return { date: todayKey(), shownIds: [] };
  }
}

export function savePopupState(state: PopupState): void {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
}

export function markPopupShown(id: number): void {
  const state = loadPopupState();
  if (!state.shownIds.includes(id)) {
    state.shownIds.push(id);
  }
  savePopupState(state);
}

export function unshownModals(modals: ScheduledPopupModal[]): ScheduledPopupModal[] {
  const { shownIds } = loadPopupState();
  return modals.filter((m) => !shownIds.includes(m.id));
}
