import { useEffect, useMemo, useRef, useState } from "react";
import { Link, useSearchParams } from "react-router-dom";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faArrowRight, faClock, faMapMarkerAlt } from "@fortawesome/free-solid-svg-icons";
import { faGoogle } from "@fortawesome/free-brands-svg-icons";
import LocationsMap, { type MapLocation } from "@/components/LocationsMap";
import { useAppNavigate } from "../lib/navigation";
import { fetchLocationsCached } from "@/lib/api";
import { useCachedQuery } from "@/lib/useCachedQuery";
import type { SpaLocationCity, SpaLocationPoint } from "@/lib/bootstrap";
import { ROUTES } from "@/lib/routes";

function toMapLocations(cities: SpaLocationCity[]): MapLocation[] {
  return cities.flatMap((city) =>
    city.locations
      .filter((loc): loc is SpaLocationPoint & { lat: number; lng: number } =>
        typeof loc.lat === "number" && typeof loc.lng === "number",
      )
      .map((loc) => ({
        id: loc.id,
        lat: loc.lat,
        lng: loc.lng,
        street: loc.street,
        linkMap:
          loc.linkMap ??
          `https://www.google.com/maps?q=${loc.lat},${loc.lng}&hl=uk`,
      })),
  );
}

export default function LocationsPage() {
  const { goHome } = useAppNavigate();
  const { data, loading, error } = useCachedQuery("api:locations:v1", () => fetchLocationsCached());
  const [searchParams] = useSearchParams();
  const selectedId = Number(searchParams.get("location") ?? 0) || null;
  const cardRefs = useRef<Record<number, HTMLElement | null>>({});

  const cities = useMemo(() => data?.cities ?? [], [data?.cities]);
  const mapLocations = useMemo(() => toMapLocations(cities), [cities]);

  const defaultActiveId = useMemo(() => {
    if (selectedId && mapLocations.some((loc) => loc.id === selectedId)) return selectedId;
    return mapLocations[0]?.id ?? null;
  }, [selectedId, mapLocations]);

  const [activeId, setActiveId] = useState<number | null>(defaultActiveId);
  const hoverTimeoutRef = useRef<number | null>(null);

  useEffect(() => {
    setActiveId(defaultActiveId);
  }, [defaultActiveId]);

  useEffect(() => {
    if (!selectedId) return;
    const el = cardRefs.current[selectedId];
    if (el) {
      requestAnimationFrame(() => {
        el.scrollIntoView({ behavior: "smooth", block: "center" });
      });
    }
  }, [selectedId, cities.length]);

  useEffect(
    () => () => {
      if (hoverTimeoutRef.current) window.clearTimeout(hoverTimeoutRef.current);
    },
    [],
  );

  const handleCardHover = (locationId: number) => {
    if (hoverTimeoutRef.current) window.clearTimeout(hoverTimeoutRef.current);
    hoverTimeoutRef.current = window.setTimeout(() => {
      setActiveId(locationId);
    }, 100);
  };

  const hasLocations = cities.some((city) => city.locations.length > 0);

  return (
    <div className="min-h-screen pt-24 pb-20">
      <div className="site-container">
        <div className="flex items-center gap-2 text-[13px] text-[#1A1A2E]/40 mb-8">
          <button type="button" onClick={goHome} className="hover:text-[#f97171]">
            Головна
          </button>
          <span>/</span>
          <span className="text-[#1A1A2E]">Локації</span>
        </div>

        <div className="flex flex-col lg:flex-row gap-6 lg:gap-8">
          <div className="lg:w-1/2 lg:max-w-2xl">
            <div className="mb-8 lg:mb-10">
              <div className="tag-badge mb-4 w-fit">Де нас знайти</div>
              <h1 className="text-section text-[#1A1A2E] mb-3">Наші локації</h1>
              <p className="text-[16px] text-[#1A1A2E]/55 max-w-3xl">
                Знайдіть найближче відділення ЄНОТ 24 у вашому місті
              </p>
            </div>

            {loading && !hasLocations ? (
              <div className="glass-strong rounded-[28px] p-10 text-center text-[14px] text-[#1A1A2E]/45">
                Завантажуємо локації…
              </div>
            ) : error && !hasLocations ? (
              <div className="glass-strong rounded-[28px] p-10 text-center text-[14px] text-red-500">
                {error}
              </div>
            ) : hasLocations ? (
              <div className="space-y-8">
                {cities.map((city) => (
                  <section key={city.id} className="locations-city-group">
                    <h2 className="locations-city-group__title">
                      <FontAwesomeIcon icon={faMapMarkerAlt} className="text-[#f97171]" />
                      {city.name}
                    </h2>

                    <div className="space-y-4">
                      {city.locations.map((location) => (
                        <article
                          key={location.id}
                          ref={(el) => {
                            cardRefs.current[location.id] = el;
                          }}
                          className={`locations-card glass-strong ${
                            activeId === location.id ? "is-active" : ""
                          }`}
                          onMouseEnter={() => handleCardHover(location.id)}
                        >
                          <div className="mb-4">
                            <h3 className="locations-card__street">{location.street}</h3>
                            {location.value && (
                              <p className="locations-card__value">{location.value}</p>
                            )}
                          </div>

                          {location.workingHours && (
                            <div className="locations-card__hours">
                              <FontAwesomeIcon icon={faClock} className="text-[#f97171]" />
                              <span>{location.workingHours}</span>
                            </div>
                          )}

                          {location.linkMap && (
                            <div className="locations-card__footer">
                              <a
                                href={location.linkMap}
                                target="_blank"
                                rel="noopener noreferrer"
                                className="locations-card__maps-link"
                                onClick={(e) => e.stopPropagation()}
                              >
                                <FontAwesomeIcon icon={faGoogle} />
                                <span>Відкрити в Google Maps</span>
                                <FontAwesomeIcon icon={faArrowRight} className="locations-card__maps-arrow" />
                              </a>
                            </div>
                          )}
                        </article>
                      ))}
                    </div>
                  </section>
                ))}
              </div>
            ) : (
              <div className="glass-strong rounded-[28px] p-10 text-center">
                <div className="text-5xl mb-4 opacity-40">📍</div>
                <p className="text-[16px] text-[#1A1A2E]/55">
                  Локації будуть додані найближчим часом
                </p>
              </div>
            )}
          </div>

          <div className="lg:w-1/2">
            <div className="locations-map-sticky">
              {loading && mapLocations.length === 0 ? (
                <div className="locations-map-shell glass-strong flex items-center justify-center min-h-[420px] lg:min-h-[600px]">
                  <p className="text-[14px] text-[#1A1A2E]/45 px-6 text-center">Завантажуємо карту…</p>
                </div>
              ) : mapLocations.length > 0 ? (
                <LocationsMap
                  locations={mapLocations}
                  activeId={activeId}
                  initialActiveId={defaultActiveId}
                  initialZoom={selectedId ? 16 : 13}
                />
              ) : (
                <div className="locations-map-shell glass-strong flex items-center justify-center min-h-[420px] lg:min-h-[600px]">
                  <p className="text-[14px] text-[#1A1A2E]/45 px-6 text-center">
                    Карта з&apos;явиться, коли будуть додані координати локацій
                  </p>
                </div>
              )}
            </div>
          </div>
        </div>

        <div className="mt-10">
          <div className="glass-peach rounded-[28px] p-6 sm:p-8 text-white flex flex-col sm:flex-row sm:items-center gap-5 justify-between">
            <div>
              <h2 className="font-black text-[22px] sm:text-[24px] mb-2">Безкоштовний кур&apos;єр по Києву</h2>
              <p className="text-white/75 text-[14px] sm:text-[15px] max-w-xl">
                Не хочете їхати до пункту прийому? Замовте виїзд кур&apos;єра — заберемо речі та повернемо готовими.
              </p>
            </div>
            <Link
              to={ROUTES.courier}
              className="inline-flex items-center justify-center bg-white text-[#f97171] px-7 py-3.5 rounded-2xl font-bold text-[14px] hover:bg-white/90 transition-all whitespace-nowrap shrink-0 no-underline"
            >
              Замовити виїзд →
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}
