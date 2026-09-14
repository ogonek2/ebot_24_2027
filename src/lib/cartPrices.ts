export function parseUah(value?: string | null): number {
  if (!value || /запитом|—/i.test(value)) return 0;
  const n = Number(value.replace(/[^\d]/g, ""));
  return Number.isFinite(n) && n > 0 ? n : 0;
}

export function formatUah(n: number): string {
  return `${n.toLocaleString("uk-UA")}₴`;
}

export type AddToCartTarget = {
  kind?: "service" | "repair";
  serviceId?: number;
  repairItemId?: number;
  serviceName: string;
  streamPrice: number;
  individualPrice: number | null;
  /** Орієнтовна ціна «від» (ремонт) */
  priceFrom?: boolean;
  initialQuantity?: number;
};

export type CleaningAvailability = {
  streamPrice: number;
  individualPrice: number | null;
  hasStream: boolean;
  hasIndividual: boolean;
  defaultType: "stream" | "individual";
};

export function isValidCartPrice(value: number | null | undefined): boolean {
  return typeof value === "number" && Number.isFinite(value) && value > 0;
}

/** Які типи чистки реально доступні для послуги (без підстановки однієї ціни в інший тип). */
export function resolveCleaningAvailability(target: Pick<AddToCartTarget, "streamPrice" | "individualPrice">): CleaningAvailability {
  const hasStream = isValidCartPrice(target.streamPrice);
  const hasIndividual = isValidCartPrice(target.individualPrice);
  return {
    streamPrice: hasStream ? target.streamPrice : 0,
    individualPrice: hasIndividual ? target.individualPrice : null,
    hasStream,
    hasIndividual,
    defaultType: hasStream ? "stream" : hasIndividual ? "individual" : "stream",
  };
}

export function buildAddToCartTarget(input: {
  serviceId: number;
  serviceName: string;
  streamPrice: number;
  individualPrice: number | null;
  initialQuantity?: number;
}): AddToCartTarget | null {
  const availability = resolveCleaningAvailability(input);
  if (!availability.hasStream && !availability.hasIndividual) return null;
  return {
    kind: "service",
    serviceId: input.serviceId,
    serviceName: input.serviceName,
    streamPrice: availability.streamPrice,
    individualPrice: availability.individualPrice,
    initialQuantity: input.initialQuantity,
  };
}

export function buildRepairAddToCartTarget(input: {
  id: number;
  name: string;
  price: number;
  pricePrefix?: string | null;
  initialQuantity?: number;
}): AddToCartTarget | null {
  if (!isValidCartPrice(input.price)) return null;
  return {
    kind: "repair",
    repairItemId: input.id,
    serviceName: input.name,
    streamPrice: input.price,
    individualPrice: null,
    priceFrom: Boolean(input.pricePrefix),
    initialQuantity: input.initialQuantity,
  };
}

export function cleaningTypeLabel(type: string): string {
  if (type === "repair") return "Ремонт";
  return type === "individual" ? "Індивідуальна" : "Потокова";
}

export function isRepairCartTarget(target: AddToCartTarget | null | undefined): boolean {
  return Boolean(target && (target.kind === "repair" || target.repairItemId));
}

export function isOnRequestPrice(price?: string | null): boolean {
  if (!price) return true;
  return /запитом|—/i.test(price) || !/\d/.test(price);
}

export type CatalogCleaningDisplay = {
  hasStream: boolean;
  hasIndividual: boolean;
  /** Немає жодної фіксованої ціни */
  isOnRequest: boolean;
  streamRaw: string | null;
  individualRaw: string | null;
};

/** Які колонки цін показувати в прайсі (без дублювання однієї ціни в обох типах). */
export function resolveCatalogCleaningDisplay(item: {
  price: string;
  priceBatch?: string;
  individualPrice?: string | null;
}): CatalogCleaningDisplay {
  const streamRaw = item.priceBatch ?? item.price;
  const individualRaw = item.individualPrice ?? null;

  const hasStream = !isOnRequestPrice(streamRaw) && parseUah(streamRaw) > 0;
  const hasIndividual = Boolean(
    individualRaw && !isOnRequestPrice(individualRaw) && parseUah(individualRaw) > 0,
  );

  const streamOnRequest = isOnRequestPrice(streamRaw);
  const individualOnRequest = individualRaw ? isOnRequestPrice(individualRaw) : true;

  return {
    hasStream,
    hasIndividual,
    isOnRequest: !hasStream && !hasIndividual && (streamOnRequest || individualOnRequest),
    streamRaw: hasStream ? streamRaw : null,
    individualRaw: hasIndividual ? individualRaw : null,
  };
}
