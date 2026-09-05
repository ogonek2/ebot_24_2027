export type CareSymbolKind =
  | "dryclean-p"
  | "dryclean-f"
  | "wash-30"
  | "wash-hand"
  | "no-wash"
  | "bleach"
  | "no-bleach"
  | "iron-low"
  | "iron-medium"
  | "no-iron"
  | "tumble-dry"
  | "no-tumble"
  | "dry-flat";

export type CareSymbol = {
  id: string;
  kind: CareSymbolKind;
  title: string;
  tip: string;
};

/** ISO 3758 — підказки для клієнта */
export const CARE_SYMBOLS: CareSymbol[] = [
  {
    id: "dry-p",
    kind: "dryclean-p",
    title: "Хімчистка (P)",
    tip: "Делікатна професійна чистка розчинником. Підходить для вовни, шовку та делікатних тканин.",
  },
  {
    id: "dry-f",
    kind: "dryclean-f",
    title: "М’яка хімчистка (F)",
    tip: "Щадна чистка з обмеженням на агресивні розчинники — для чутливих матеріалів і наповнювачів.",
  },
  {
    id: "wash-30",
    kind: "wash-30",
    title: "Прання 30 °C",
    tip: "Делікатне прання у прохолодній воді. Зберігає форму та колір; не викручувати.",
  },
  {
    id: "wash-hand",
    kind: "wash-hand",
    title: "Ручне прання",
    tip: "Тільки вручну в прохолодній воді. Не терти — делікатно віджати в рушник.",
  },
  {
    id: "no-wash",
    kind: "no-wash",
    title: "Не прати",
    tip: "Домашнє прання заборонене — лише професійна чистка, інакше можливі усадка та деформація.",
  },
  {
    id: "bleach",
    kind: "bleach",
    title: "Відбілювання",
    tip: "Дозволено будь-яке відбілювання. Перевіряйте етикетку перед застосуванням на кольорових речах.",
  },
  {
    id: "no-bleach",
    kind: "no-bleach",
    title: "Не відбілювати",
    tip: "Хлор та агресивні відбілювачі зашкодять волокнам і змінять колір.",
  },
  {
    id: "iron-low",
    kind: "iron-low",
    title: "Prasuvannya · nyzka t°",
    tip: "Prasuvannya do 110 °C (●). Dlya syntetyky — bez pary abo minimum pary.",
  },
  {
    id: "iron-med",
    kind: "iron-medium",
    title: "Prasuvannya · serednya t°",
    tip: "Prasuvannya do 150 °C (●●). Dlya bavovny ta lonu — mozhna z paroyu.",
  },
  {
    id: "no-iron",
    kind: "no-iron",
    title: "Не прасувати",
    tip: "Термообробка заборонена — ризик блиску, плавлення волокон або відбитків швів.",
  },
  {
    id: "tumble",
    kind: "tumble-dry",
    title: "Сушіння в барабані",
    tip: "Машинне сушіння на низькому режимі. Не пересушувати — краще залишити трохи вологи.",
  },
  {
    id: "no-tumble",
    kind: "no-tumble",
    title: "Не сушити в барабані",
    tip: "Висока температура деформує річ. Сушіть на плічках або горизонтально.",
  },
  {
    id: "flat",
    kind: "dry-flat",
    title: "Сушити горизонтально",
    tip: "Розкладіть на рівній поверхні — в’язані речі не розтягнуться під власною вагою.",
  },
];

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
