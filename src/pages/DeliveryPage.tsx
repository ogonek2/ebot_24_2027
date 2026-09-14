import { Link } from "react-router-dom";
import { useMemo } from "react";
import Breadcrumbs, { homeCrumb } from "@/components/Breadcrumbs";
import DeliveryPricingSection from "@/components/DeliveryPricingSection";
import Reveal from "@/components/Reveal";
import { openFeedbackModal } from "@/context/FeedbackContext";
import { useBootstrap } from "@/context/BootstrapContext";
import { ROUTES } from "@/lib/routes";
import carUrl from "@/storage/icons/Frame 1701.svg";
import qualityIcon from "@/storage/icons/Frame 1705.svg";
import speedIcon from "@/storage/icons/Frame 1710.svg";
import mapIcon from "@/storage/icons/Frame 1702.svg";
import leafIcon from "@/storage/icons/Frame 1699.svg";

export default function DeliveryPage() {
  const { branches = [] } = useBootstrap();

  const districts = useMemo(() => {
    const set = new Set<string>();
    for (const b of branches) {
      const d = b.district?.trim();
      if (d) set.add(d);
    }
    return Array.from(set);
  }, [branches]);

  const branchCount = branches.length;

  return (
    <div className="min-h-screen pt-24 pb-20">
      <div className="site-container">
        <Breadcrumbs items={[homeCrumb(), { name: "Доставка" }]} />

        {/* Hero — brand + one message + CTA */}
        <Reveal>
          <section className="dp-hero">
            <p className="dp-hero__watermark" aria-hidden="true">
              ENOT
            </p>
            <div className="dp-hero__glow" aria-hidden="true" />
            <div className="dp-hero__grid">
              <div className="dp-hero__copy">
                <p className="dp-hero__brand">ЄНОТ 24</p>
                <h1 className="dp-hero__title">
                  Доставка
                  <span className="dp-hero__title-soft"> до дверей</span>
                </h1>
                <p className="dp-hero__lead">
                  Заберемо речі, віддамо в хімчистку й повернемо у зручний час — по всьому Києву.
                </p>
                <div className="dp-hero__actions">
                  <Link to={ROUTES.courier} className="btn-primary dp-hero__cta no-underline">
                    Викликати кур&apos;єра
                  </Link>
                  <button type="button" onClick={openFeedbackModal} className="btn-outline dp-hero__cta">
                    Консультація
                  </button>
                </div>
              </div>

              <div className="dp-hero__visual" aria-hidden="true">
                <img src={carUrl} alt="" className="dp-hero__car" draggable={false} />
                <div className="dp-hero__orbit dp-hero__orbit--a" />
                <div className="dp-hero__orbit dp-hero__orbit--b" />
              </div>
            </div>
          </section>
        </Reveal>

        {/* Speed */}
        <Reveal>
          <section className="dp-panel dp-panel--speed">
            <div className="dp-panel__mark" aria-hidden="true">
              <img src={speedIcon} alt="" />
            </div>
            <p className="dp-panel__eyebrow">Швидкість</p>
            <div className="dp-panel__split">
              <h2 className="dp-panel__display">
                30
                <span className="dp-panel__unit">хв</span>
              </h2>
              <div className="dp-panel__body">
                <h3 className="dp-panel__title">Відповідь і слот кур&apos;єра без очікування</h3>
                <p className="dp-panel__text">
                  Заявка обробляється швидко. Підберемо зручний час забору й повернення — без зайвих дзвінків і
                  сюрпризів у маршруті.
                </p>
              </div>
            </div>
          </section>
        </Reveal>

        {/* Quality */}
        <Reveal>
          <section className="dp-panel dp-panel--quality">
            <div className="dp-panel__mark" aria-hidden="true">
              <img src={qualityIcon} alt="" />
            </div>
            <p className="dp-panel__eyebrow">Якість хімчистки</p>
            <div className="dp-panel__split dp-panel__split--rev">
              <div className="dp-panel__body">
                <h3 className="dp-panel__title">Речі в надійних руках майстрів</h3>
                <p className="dp-panel__text">
                  Професійна хімчистка, дбайливе пакування й контроль на кожному етапі. Кур&apos;єр везе не просто
                  посилку — результат нашої роботи.
                </p>
                <div className="dp-quality-tags">
                  <span>
                    <img src={leafIcon} alt="" /> Гіпоалергенні засоби
                  </span>
                  <span>
                    <img src={qualityIcon} alt="" /> Контроль якості
                  </span>
                </div>
              </div>
              <h2 className="dp-panel__display dp-panel__display--word">Догляд</h2>
            </div>
          </section>
        </Reveal>

        {/* Kyiv coverage */}
        <Reveal>
          <section className="dp-kyiv">
            <div className="dp-kyiv__glow" aria-hidden="true" />
            <div className="dp-kyiv__head">
              <div className="dp-kyiv__mark" aria-hidden="true">
                <img src={mapIcon} alt="" />
              </div>
              <p className="dp-panel__eyebrow">Покриття</p>
              <h2 className="dp-kyiv__title">
                Відділення
                <span className="dp-kyiv__title-accent"> по всьому Києву</span>
              </h2>
              <p className="dp-kyiv__lead">
                {branchCount > 0
                  ? `${branchCount} пунктів прийому — зручно забрати самостійно або викликати кур'єра.`
                  : "Пункти прийому по місту — зручно забрати самостійно або викликати кур'єра."}
              </p>
            </div>

            {districts.length > 0 && (
              <p className="dp-kyiv__districts">
                {districts.map((d, i) => (
                  <span key={d}>
                    {i > 0 && <span className="dp-kyiv__dot" aria-hidden="true"> · </span>}
                    {d}
                  </span>
                ))}
              </p>
            )}

            {branchCount > 0 && (
              <ul className="dp-kyiv__branches">
                {branches.slice(0, 8).map((b) => (
                  <li key={b.id}>
                    <span className="dp-kyiv__branch-city">{b.city || b.district || "Київ"}</span>
                    <span className="dp-kyiv__branch-addr">{b.address}</span>
                  </li>
                ))}
              </ul>
            )}

            <div className="dp-kyiv__footer">
              <Link to={ROUTES.locations} className="btn-outline no-underline">
                Усі локації →
              </Link>
              <Link to={ROUTES.courier} className="btn-primary no-underline">
                Замовити доставку
              </Link>
            </div>
          </section>
        </Reveal>
      </div>

      <DeliveryPricingSection />
    </div>
  );
}
