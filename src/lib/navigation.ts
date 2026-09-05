import { useNavigate, useLocation } from "react-router-dom";
import { ROUTES } from "./routes";

export function useAppNavigate() {
  const navigate = useNavigate();
  const location = useLocation();

  const goToHash = (hash: string) => {
    if (location.pathname !== ROUTES.home) {
      navigate(ROUTES.home);
      setTimeout(() => document.getElementById(hash)?.scrollIntoView({ behavior: "smooth" }), 150);
    } else {
      document.getElementById(hash)?.scrollIntoView({ behavior: "smooth" });
    }
  };

  return {
    navigate,
    location,
    goHome: () => navigate(ROUTES.home),
    goServices: () => navigate(ROUTES.services),
    goOrder: () => navigate(ROUTES.courier),
    goCheckout: () => navigate(ROUTES.cart),
    goLocations: () => navigate(ROUTES.locations),
    goB2b: () => navigate(ROUTES.b2b),
    goToHash,
  };
}

export function pathIsServices(pathname: string) {
  return pathname.startsWith("/poslugi-ta-cini");
}
