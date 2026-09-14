import { useEffect, useId, useRef, useState, type CSSProperties } from "react";
import { Link } from "react-router-dom";
import Reveal from "./Reveal";
import { ROUTES } from "@/lib/routes";
import carUrl from "@/storage/icons/Frame 1701.svg";
import dropA from "@/storage/icons/Frame 1694.svg";
import dropB from "@/storage/icons/Frame 1695.svg";
import dropC from "@/storage/icons/Frame 1698.svg";
import dropD from "@/storage/icons/Frame 1700.svg";
import dropE from "@/storage/icons/Frame 1704.svg";
import dropF from "@/storage/icons/Frame 1708.svg";
import dropG from "@/storage/icons/Frame 1710.svg";
import dropH from "@/storage/icons/Frame 1712.svg";

const ROAD_D = "M20 120 C120 40, 220 160, 320 90 S520 30, 620 110";
const VIEW_W = 640;
const VIEW_H = 180;
const DRIVE_MS = 14_000;
const DROP_ICONS = [dropA, dropB, dropC, dropD, dropE, dropF, dropG, dropH];
/** Progress points along the road where icons spill out */
const DROP_AT = [0.28, 0.32, 0.36, 0.48, 0.52, 0.56, 0.68, 0.72];

type DropParticle = {
  id: number;
  src: string;
  x: number;
  y: number;
  dx: number;
  rot: number;
};

function DeliveryRouteAnim() {
  const pathRef = useRef<SVGPathElement>(null);
  const carRef = useRef<HTMLDivElement>(null);
  const roadId = useId().replace(/:/g, "");
  const [drops, setDrops] = useState<DropParticle[]>([]);
  const dropSeq = useRef(0);
  const fired = useRef<Set<number>>(new Set());
  const loopToken = useRef(0);

  useEffect(() => {
    const reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const path = pathRef.current;
    const carEl = carRef.current;
    if (!path || !carEl) return;

    const length = path.getTotalLength();
    const placeCar = (dist: number) => {
      const pt = path.getPointAtLength(dist);
      const look = path.getPointAtLength(Math.min(length, dist + 4));
      const angle = (Math.atan2(look.y - pt.y, look.x - pt.x) * 180) / Math.PI;
      carEl.style.left = `${(pt.x / VIEW_W) * 100}%`;
      carEl.style.top = `${(pt.y / VIEW_H) * 100}%`;
      carEl.style.transform = `translate(-50%, -58%) rotate(${angle}deg)`;
    };

    if (reduced) {
      placeCar(length * 0.45);
      return;
    }

    let raf = 0;
    const start = performance.now();

    const spawnDrop = (progress: number, index: number) => {
      const pt = path.getPointAtLength(progress * length);
      const src = DROP_ICONS[index % DROP_ICONS.length];
      const id = ++dropSeq.current;
      const particle: DropParticle = {
        id,
        src,
        x: pt.x,
        y: pt.y,
        dx: (Math.random() - 0.5) * 56,
        rot: (Math.random() - 0.5) * 48,
      };
      setDrops((prev) => [...prev.slice(-14), particle]);
      window.setTimeout(() => {
        setDrops((prev) => prev.filter((d) => d.id !== id));
      }, 1600);
    };

    const tick = (now: number) => {
      const elapsed = now - start;
      const cycle = Math.floor(elapsed / DRIVE_MS);
      if (cycle !== loopToken.current) {
        loopToken.current = cycle;
        fired.current.clear();
      }

      const t = (elapsed % DRIVE_MS) / DRIVE_MS;
      placeCar(t * length);

      DROP_AT.forEach((at, index) => {
        if (t >= at && !fired.current.has(index)) {
          fired.current.add(index);
          spawnDrop(at, index);
        }
      });

      raf = requestAnimationFrame(tick);
    };

    placeCar(0);
    raf = requestAnimationFrame(tick);
    return () => cancelAnimationFrame(raf);
  }, []);

  return (
    <div className="delivery-band__track" aria-hidden="true">
      <svg className="delivery-band__route" viewBox={`0 0 ${VIEW_W} ${VIEW_H}`} fill="none">
        <path
          ref={pathRef}
          id={`delivery-road-${roadId}`}
          className="delivery-band__route-line"
          d={ROAD_D}
        />
      </svg>

      <div ref={carRef} className="delivery-band__car">
        <img src={carUrl} alt="" draggable={false} />
      </div>

      {drops.map((drop) => (
        <span
          key={drop.id}
          className="delivery-band__drop"
          style={
            {
              left: `${(drop.x / VIEW_W) * 100}%`,
              top: `${(drop.y / VIEW_H) * 100}%`,
              "--drop-dx": `${drop.dx}px`,
              "--drop-rot": `${drop.rot}deg`,
            } as CSSProperties
          }
        >
          <img src={drop.src} alt="" draggable={false} />
        </span>
      ))}
    </div>
  );
}

export default function DeliveryPricingSection() {
  return (
    <section className="delivery-band" id="delivery">
      <div className="site-container">
        <Reveal>
          <div className="delivery-band__stage">
            <p className="delivery-band__watermark" aria-hidden="true">
              Доставка
            </p>
            <div className="delivery-band__glow" aria-hidden="true" />
            <DeliveryRouteAnim />

            <div className="delivery-band__layout">
              <div className="delivery-band__copy">
                <p className="delivery-band__eyebrow">Кур&apos;єр</p>
                <h2 className="delivery-band__title">
                  Заберемо й привеземо
                  <span className="delivery-band__title-soft"> у зручний час</span>
                </h2>
                <p className="delivery-band__note">
                  Вартість доставки уточнюємо при замовленні — без сюрпризів у чеку.
                </p>
              </div>

              <div className="delivery-band__actions">
                <Link to={ROUTES.courier} className="btn-primary delivery-band__cta no-underline">
                  Замовити кур&apos;єра
                </Link>
                <a href="tel:+380678872233" className="delivery-band__phone no-underline">
                  <span className="delivery-band__phone-label">Або зателефонуйте</span>
                  <span className="delivery-band__phone-num">067 887 22 33</span>
                </a>
              </div>
            </div>
          </div>
        </Reveal>
      </div>
    </section>
  );
}
