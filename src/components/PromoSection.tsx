import { Link } from "react-router-dom";
import Reveal from "./Reveal";
import PromoTile from "./promotions/PromoTile";
import { useBootstrap } from "@/context/BootstrapContext";
import type { Promotion } from "@/lib/promotions";
import { ROUTES } from "@/lib/routes";

export default function PromoSection() {
  const { discounts = [] } = useBootstrap();

  if (!discounts.length) return null;

  const items: Promotion[] = discounts.slice(0, 4).map((d) => ({
    id: d.id,
    name: d.name,
    discountAction: d.discountAction,
    locations: d.locations,
    banner: d.banner,
    color: d.color,
    textColor: d.textColor,
    discountColor: d.discountColor,
  }));

  return (
    <section className="py-14 sm:py-16" id="promo">
      <div className="site-container">
        <Reveal>
          <div className="promo-home-head">
            <div>
              <div className="tag-badge mb-3 w-fit">Акції</div>
              <h2 className="text-section text-[#1A1A2E]">Спеціальні пропозиції</h2>
            </div>
            <Link to={ROUTES.promotions} className="promo-home-head__link no-underline">
              Усі акції →
            </Link>
          </div>

          <div className="promo-home-strip">
            {items.map((promo, i) => (
              <PromoTile key={promo.id} promo={promo} lead={i === 0} />
            ))}
          </div>
        </Reveal>
      </div>
    </section>
  );
}
