import { useEffect, useMemo, useRef } from "react";
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import markerIconUrl from "@/logo/logo_location.svg";

export type MapLocation = {
  id: number;
  lat: number;
  lng: number;
  street: string;
  linkMap: string;
};

type Props = {
  locations: MapLocation[];
  activeId: number | null;
  initialActiveId?: number | null;
  initialZoom?: number;
};

const KYIV_CENTER: L.LatLngExpression = [50.4501, 30.5234];

export default function LocationsMap({
  locations,
  activeId,
  initialActiveId = null,
  initialZoom = 13,
}: Props) {
  const containerRef = useRef<HTMLDivElement>(null);
  const mapRef = useRef<L.Map | null>(null);
  const markersRef = useRef<Array<L.Marker & { locationId?: number }>>([]);
  const activeIdRef = useRef<number | null>(activeId);
  const prevActiveIdRef = useRef<number | null>(null);

  const locationsKey = useMemo(
    () => locations.map((loc) => loc.id).join(","),
    [locations],
  );

  useEffect(() => {
    activeIdRef.current = activeId;
  }, [activeId]);

  useEffect(() => {
    if (!containerRef.current || locations.length === 0) return;

    const startId = initialActiveId ?? activeId ?? locations[0]?.id ?? null;
    const initial = locations.find((loc) => loc.id === startId) ?? locations[0];
    const center: L.LatLngExpression = initial ? [initial.lat, initial.lng] : KYIV_CENTER;
    const zoom = startId ? 16 : initialZoom;

    const map = L.map(containerRef.current, {
      zoomControl: true,
      scrollWheelZoom: true,
    }).setView(center, zoom);

    L.tileLayer("https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}", {
      subdomains: ["mt0", "mt1", "mt2", "mt3"],
      attribution: "&copy; Google Maps",
      maxZoom: 20,
    }).addTo(map);

    const icon = L.icon({
      iconUrl: markerIconUrl,
      iconSize: [50, 50],
      iconAnchor: [25, 50],
      popupAnchor: [0, -50],
    });

    markersRef.current = locations.map((location) => {
      const marker = L.marker([location.lat, location.lng], { icon })
        .addTo(map)
        .bindPopup(
          `<a href="${location.linkMap}" target="_blank" rel="noopener noreferrer"><strong>${location.street}</strong></a>`,
        ) as L.Marker & { locationId?: number };

      marker.locationId = location.id;
      return marker;
    });

    mapRef.current = map;
    prevActiveIdRef.current = startId;

    if (startId) {
      const selected = markersRef.current.find((marker) => marker.locationId === startId);
      if (selected) {
        window.setTimeout(() => selected.openPopup(), 900);
      }
    }

    window.setTimeout(() => map.invalidateSize(), 120);

    return () => {
      map.remove();
      mapRef.current = null;
      markersRef.current = [];
      prevActiveIdRef.current = null;
    };
  }, [locations, locationsKey, initialActiveId, initialZoom]);

  useEffect(() => {
    const map = mapRef.current;
    if (!map || activeId == null) return;
    if (prevActiveIdRef.current === activeId) return;

    prevActiveIdRef.current = activeId;

    const location = locations.find((loc) => loc.id === activeId);
    if (!location) return;

    map.flyTo([location.lat, location.lng], 16, {
      animate: true,
      duration: 1,
    });

    const marker = markersRef.current.find((item) => item.locationId === activeId);
    if (!marker) return;

    window.setTimeout(() => {
      if (activeIdRef.current === activeId) marker.openPopup();
    }, 700);
  }, [activeId, locations]);

  return (
    <div className="locations-map-shell glass-strong overflow-hidden">
      <div ref={containerRef} className="locations-map" aria-label="Карта локацій" />
      <div className="locations-map__hint">
        <span aria-hidden="true">ℹ️</span>
        Наведіть на локацію зліва, щоб переглянути її на карті
      </div>
    </div>
  );
}
