import { useEffect, useMemo, useRef, useState } from "react";

export type PickupLocation = {
  id: number;
  street: string;
  city: string;
  working_hours: string;
};

type Props = {
  locations: PickupLocation[];
  value: number | null;
  onChange: (id: number) => void;
  required?: boolean;
};

export default function PickupLocationSelect({ locations, value, onChange, required }: Props) {
  const [open, setOpen] = useState(false);
  const [search, setSearch] = useState("");
  const rootRef = useRef<HTMLDivElement>(null);

  const selected = locations.find((l) => l.id === value) ?? null;

  const filtered = useMemo(() => {
    const q = search.trim().toLowerCase();
    if (!q) return locations;
    return locations.filter((loc) => {
      const hay = `${loc.street} ${loc.city} ${loc.working_hours}`.toLowerCase();
      return hay.includes(q);
    });
  }, [locations, search]);

  useEffect(() => {
    if (!open) return;
    const onDoc = (e: MouseEvent) => {
      if (rootRef.current && !rootRef.current.contains(e.target as Node)) {
        setOpen(false);
        setSearch("");
      }
    };
    document.addEventListener("mousedown", onDoc);
    return () => document.removeEventListener("mousedown", onDoc);
  }, [open]);

  const displayText = selected
    ? `${selected.street}, ${selected.city}${selected.working_hours ? ` (${selected.working_hours})` : ""}`
    : "Оберіть приймальний пункт…";

  return (
    <div className="pickup-select" ref={rootRef}>
      <input type="hidden" value={value ?? ""} required={required} readOnly />
      <button
        type="button"
        className={`pickup-select__toggle ${selected ? "pickup-select__toggle--filled" : ""}`}
        onClick={() => setOpen((v) => !v)}
        aria-expanded={open}
      >
        <span className="pickup-select__toggle-text">{displayText}</span>
        <span className={`pickup-select__arrow ${open ? "pickup-select__arrow--open" : ""}`}>▼</span>
      </button>

      {open && (
        <div className="pickup-select__dropdown">
          <div className="pickup-select__search-wrap">
            <input
              type="text"
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              placeholder="Пошук приймального пункту…"
              className="pickup-select__search"
              autoFocus
            />
          </div>
          <div className="pickup-select__list">
            {filtered.length === 0 ? (
              <p className="pickup-select__empty">Нічого не знайдено</p>
            ) : (
              filtered.map((loc) => (
                <button
                  key={loc.id}
                  type="button"
                  className={`pickup-select__option ${value === loc.id ? "pickup-select__option--active" : ""}`}
                  onClick={() => {
                    onChange(loc.id);
                    setOpen(false);
                    setSearch("");
                  }}
                >
                  <span className="pickup-select__option-street">{loc.street}</span>
                  <span className="pickup-select__option-city">{loc.city}</span>
                  {loc.working_hours && (
                    <span className="pickup-select__option-hours">{loc.working_hours}</span>
                  )}
                </button>
              ))
            )}
          </div>
        </div>
      )}
    </div>
  );
}
