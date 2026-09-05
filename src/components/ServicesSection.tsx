import { Link, useNavigate } from "react-router-dom";
import PriceCatalog from "./PriceCatalog";
import { ROUTES } from "@/lib/routes";

export default function ServicesSection() {
  const navigate = useNavigate();

  return (
    <section className="py-14 sm:py-16" id="prices">
      <div className="site-container">
        <PriceCatalog variant="section" onCheckout={() => navigate(ROUTES.cart)} />
      </div>
    </section>
  );
}
