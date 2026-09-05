import careSymbolsData from "./careSymbols.json";

export type CareSymbolKind =
  | "dryclean-p"
  | "dryclean-p-mild"
  | "dryclean-f"
  | "dryclean-f-mild"
  | "dryclean-a"
  | "no-dryclean"
  | "wet-clean"
  | "wet-clean-mild"
  | "no-wet-clean"
  | "wash-30"
  | "wash-30-mild"
  | "wash-40"
  | "wash-60"
  | "wash-95"
  | "wash-hand"
  | "no-wash"
  | "bleach"
  | "bleach-oxygen"
  | "no-bleach"
  | "iron-low"
  | "iron-medium"
  | "iron-high"
  | "no-iron"
  | "tumble-dry"
  | "tumble-low"
  | "tumble-high"
  | "no-tumble"
  | "line-dry"
  | "drip-dry"
  | "dry-flat"
  | "dry-shade";

export type CareSymbol = {
  id: string;
  kind: CareSymbolKind;
  title: string;
  tip: string;
};

/** ISO 3758 — підказки для клієнта (дані з careSymbols.json) */
export const CARE_SYMBOLS: CareSymbol[] = careSymbolsData as CareSymbol[];

export type PlanetPhase = "idle" | "pop" | "grow";

export type OrbitPlanetSlot = {
  slotId: string;
  radiusPct: number;
  duration: string;
  reverse?: boolean;
  angle: number;
  size: number;
  symbol: CareSymbol;
  phase: PlanetPhase;
};

const SLOT_BLUEPRINT: Array<Omit<OrbitPlanetSlot, "symbol" | "phase">> = [
  { slotId: "in-1", radiusPct: 24, duration: "48s", angle: 15, size: 62 },
  { slotId: "in-2", radiusPct: 24, duration: "48s", angle: 195, size: 70 },
  { slotId: "mid-1", radiusPct: 36, duration: "62s", reverse: true, angle: 55, size: 74 },
  { slotId: "mid-2", radiusPct: 36, duration: "62s", reverse: true, angle: 200, size: 66 },
  { slotId: "mid-3", radiusPct: 36, duration: "62s", reverse: true, angle: 310, size: 78 },
  { slotId: "out-1", radiusPct: 47, duration: "84s", angle: 30, size: 80 },
  { slotId: "out-2", radiusPct: 47, duration: "84s", angle: 145, size: 72 },
  { slotId: "out-3", radiusPct: 47, duration: "84s", angle: 260, size: 76 },
];

export function shuffleCareSymbols(pool = CARE_SYMBOLS): CareSymbol[] {
  const copy = [...pool];
  for (let i = copy.length - 1; i > 0; i -= 1) {
    const j = Math.floor(Math.random() * (i + 1));
    [copy[i], copy[j]] = [copy[j], copy[i]];
  }
  return copy;
}

export function pickReplacementSymbol(usedIds: Set<string>): CareSymbol {
  const available = CARE_SYMBOLS.filter((s) => !usedIds.has(s.id));
  const pool = available.length > 0 ? available : CARE_SYMBOLS;
  return pool[Math.floor(Math.random() * pool.length)]!;
}

export function createInitialOrbitSlots(): OrbitPlanetSlot[] {
  const symbols = shuffleCareSymbols().slice(0, SLOT_BLUEPRINT.length);
  return SLOT_BLUEPRINT.map((bp, i) => ({
    ...bp,
    symbol: symbols[i] ?? CARE_SYMBOLS[i % CARE_SYMBOLS.length]!,
    phase: "idle" as const,
  }));
}

export function groupSlotsByRing(slots: OrbitPlanetSlot[]) {
  const groups = new Map<string, OrbitPlanetSlot[]>();
  for (const slot of slots) {
    const key = `${slot.radiusPct}-${slot.duration}-${slot.reverse ? "r" : "f"}`;
    const list = groups.get(key) ?? [];
    list.push(slot);
    groups.set(key, list);
  }
  return [...groups.entries()].map(([key, planets]) => ({
    key,
    radiusPct: planets[0]!.radiusPct,
    duration: planets[0]!.duration,
    reverse: planets[0]!.reverse,
    planets,
  }));
}

export function iconSizeForPlanet(planetSize: number): string {
  if (planetSize >= 76) return "w-9 h-9";
  if (planetSize >= 68) return "w-8 h-8";
  if (planetSize >= 60) return "w-7 h-7";
  return "w-6 h-6";
}
