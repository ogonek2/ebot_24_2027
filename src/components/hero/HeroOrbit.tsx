import { useCallback, useEffect, useMemo, useState, type CSSProperties } from "react";
import RaccoonLogo from "../RaccoonLogo";
import CareSymbolIcon from "./CareSymbolIcon";
import {
  createInitialOrbitSlots,
  groupSlotsByRing,
  iconSizeForPlanet,
  pickReplacementSymbol,
  type CareSymbol,
  type OrbitPlanetSlot,
} from "./careSymbols";

const ROTATE_MS = 10_000;
const POP_MS = 340;
const GROW_MS = 480;

function planetPosition(angleDeg: number, radiusPct: number): CSSProperties {
  const rad = ((angleDeg - 90) * Math.PI) / 180;
  return {
    left: `${50 + Math.cos(rad) * radiusPct}%`,
    top: `${50 + Math.sin(rad) * radiusPct}%`,
    transform: "translate(-50%, -50%)",
  };
}

type Props = {
  compact?: boolean;
};

export default function HeroOrbit({ compact = false }: Props) {
  const [slots, setSlots] = useState<OrbitPlanetSlot[]>(() => createInitialOrbitSlots());
  const [activeId, setActiveId] = useState<string | null>(null);
  const rings = useMemo(() => groupSlotsByRing(slots), [slots]);

  const active: CareSymbol | null =
    slots.find((s) => s.symbol.id === activeId)?.symbol ??
    (activeId ? slots.find((s) => s.slotId === activeId)?.symbol ?? null : null);

  const rotateOne = useCallback(() => {
    setSlots((prev) => {
      if (prev.some((s) => s.phase !== "idle")) return prev;
      const idx = Math.floor(Math.random() * prev.length);
      return prev.map((s, i) => (i === idx ? { ...s, phase: "pop" as const } : s));
    });

    window.setTimeout(() => {
      setSlots((prev) => {
        const popIdx = prev.findIndex((s) => s.phase === "pop");
        if (popIdx === -1) return prev;
        const used = new Set(prev.map((s) => s.symbol.id));
        const nextSymbol = pickReplacementSymbol(used);
        return prev.map((s, i) =>
          i === popIdx ? { ...s, symbol: nextSymbol, phase: "grow" as const } : s,
        );
      });

      window.setTimeout(() => {
        setSlots((prev) => prev.map((s) => (s.phase === "grow" ? { ...s, phase: "idle" as const } : s)));
      }, GROW_MS);
    }, POP_MS);
  }, []);

  useEffect(() => {
    const id = window.setInterval(rotateOne, ROTATE_MS);
    return () => window.clearInterval(id);
  }, [rotateOne]);

  useEffect(() => {
    if (!activeId) return;
    const stillVisible = slots.some((s) => s.symbol.id === activeId);
    if (!stillVisible) setActiveId(null);
  }, [slots, activeId]);

  return (
    <div className={`hero-orbit ${compact ? "hero-orbit--compact" : "hero-orbit--hero"}`}>
      <div className="hero-orbit__stage">
        <div className="hero-orbit__ring hero-orbit__ring--outer" aria-hidden />
        <div className="hero-orbit__ring hero-orbit__ring--mid" aria-hidden />
        <div className="hero-orbit__ring hero-orbit__ring--inner" aria-hidden />

        {rings.map((ring) => (
          <div
            key={ring.key}
            className={`hero-orbit__spin ${ring.reverse ? "orbital-spin-rev" : "orbital-spin-fwd"}`}
            style={{ "--orbit-duration": ring.duration } as CSSProperties}
          >
            {ring.planets.map((slot) => {
              const isActive = activeId === slot.symbol.id;
              const phaseClass =
                slot.phase === "pop" ? "is-pop" : slot.phase === "grow" ? "is-grow" : "";

              return (
                <div
                  key={slot.slotId}
                  className="hero-orbit__planet-slot"
                  style={planetPosition(slot.angle, slot.radiusPct)}
                >
                  <div
                    className={`hero-orbit__planet-spin ${ring.reverse ? "orbital-spin-fwd" : "orbital-spin-rev"}`}
                    style={{ "--orbit-duration": ring.duration } as CSSProperties}
                  >
                    <button
                      type="button"
                      title={slot.symbol.title}
                      onClick={() => setActiveId(isActive ? null : slot.symbol.id)}
                      className={`hero-orbit__planet pointer-events-auto flex items-center justify-center rounded-full border shadow-lg backdrop-blur-md ${
                        isActive ? "is-active" : ""
                      } ${phaseClass}`}
                      style={{ width: slot.size, height: slot.size }}
                      aria-expanded={isActive}
                      aria-label={`${slot.symbol.title}: ${slot.symbol.tip}`}
                    >
                      <CareSymbolIcon
                        key={slot.symbol.id}
                        kind={slot.symbol.kind}
                        className={`text-[#1A1A2E] ${iconSizeForPlanet(slot.size)}`}
                      />
                    </button>
                  </div>
                </div>
              );
            })}
          </div>
        ))}

        <div className="hero-orbit__sun">
          <div className="hero-orbit__sun-glow" aria-hidden />
          <RaccoonLogo size={compact ? 68 : 130} className="relative z-10" />
        </div>
      </div>

      {active && (
        <div className="hero-orbit__tip pointer-events-auto anim-fade-up" role="status">
          <div className="glass-strong rounded-2xl px-4 py-3 border border-white/60 shadow-lg text-left">
            <div className="flex items-start gap-3">
              <span className="shrink-0 w-10 h-10 rounded-xl bg-white/70 border border-white/60 flex items-center justify-center">
                <CareSymbolIcon kind={active.kind} className="w-6 h-6 text-[#1A1A2E]" />
              </span>
              <div className="min-w-0">
                <div className="font-bold text-[13px] text-[#1A1A2E] leading-tight">{active.title}</div>
                <p className="text-[12px] text-[#1A1A2E]/60 leading-relaxed mt-1">{active.tip}</p>
              </div>
              <button
                type="button"
                onClick={() => setActiveId(null)}
                className="shrink-0 cc-icon-btn text-[#1A1A2E]/45"
                aria-label="Закрити підказку"
              >
                ×
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
