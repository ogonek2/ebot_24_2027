export function parseUah(value?: string | null): number {
  if (!value || /запитом|—/i.test(value)) return 0;
  const n = Number(value.replace(/[^\d]/g, ""));
  return Number.isFinite(n) && n > 0 ? n : 0;
}

export function formatUah(n: number): string {
  return `${n.toLocaleString("uk-UA")}₴`;
}

export type AddToCartTarget = {
  serviceId: number;
  serviceName: string;
  streamPrice: number;
  individualPrice: number | null;
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
    serviceId: input.serviceId,
    serviceName: input.serviceName,
    streamPrice: availability.streamPrice,
    individualPrice: availability.individualPrice,
    initialQuantity: input.initialQuantity,
  };
}

export function cleaningTypeLabel(type: string): string {
  return type === "individual" ? "Індивідуальна" : "Потокова";
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
