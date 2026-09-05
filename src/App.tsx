import { BrowserRouter, Route, Routes, useLocation } from "react-router-dom";
import { useState } from "react";
import ScrollManager from "./components/ScrollManager";
import SeoManager from "./components/seo/SeoManager";
import Header from "./components/Header";
import MobileNav from "./components/MobileNav";
import Footer from "./components/Footer";
import BottomNav from "./components/BottomNav";
import FloatingDock from "./components/FloatingDock";
import ScheduledPopupManager from "./components/ScheduledPopupManager";
import HomePage from "./pages/HomePage";
import ServicesPage from "./pages/ServicesPage";
import CategoryPage from "./pages/CategoryPage";
import ServiceDetailPage from "./pages/ServiceDetailPage";
import B2BPage from "./pages/B2BPage";
import B2BDetailPage from "./pages/B2BDetailPage";
import OrderPage from "./pages/OrderPage";
import CheckoutPage from "./pages/CheckoutPage";
import CartPage from "./pages/CartPage";
import OrderSuccessPage from "./pages/OrderSuccessPage";
import LocationsPage from "./pages/LocationsPage";
import DeliveryPage from "./pages/DeliveryPage";
import ContactsPage from "./pages/ContactsPage";
import PromotionsPage from "./pages/PromotionsPage";
import PromotionDetailPage from "./pages/PromotionDetailPage";
import BlogPage from "./pages/BlogPage";
import BlogPostPage from "./pages/BlogPostPage";
import LegalPage from "./pages/LegalPage";
import { BootstrapProvider, useBootstrapState } from "./context/BootstrapContext";
import { CartProvider } from "./context/CartContext";
import { FeedbackProvider } from "./context/FeedbackContext";
import { ROUTES } from "./lib/routes";

function AppShell() {
  const [isMobileNavOpen, setIsMobileNavOpen] = useState(false);
  const location = useLocation();
  const { error } = useBootstrapState();

  return (
    <div className="app-shell font-body">
      <div className="app-content">
        <Header
          onMenuToggle={() => setIsMobileNavOpen(!isMobileNavOpen)}
          isMobileNavOpen={isMobileNavOpen}
        />
        <MobileNav
          isOpen={isMobileNavOpen}
          onClose={() => setIsMobileNavOpen(false)}
        />

        <main>
          {error && (
            <div className="site-container py-4 text-center text-[#f97171] text-sm">{error}</div>
          )}
          <Routes location={location}>
            <Route path={ROUTES.home} element={<HomePage />} />
            <Route path={ROUTES.services} element={<ServicesPage />} />
            <Route
              path="/poslugi-ta-cini/:category/posluga/:service"
              element={<ServiceDetailPage />}
            />
            <Route path="/poslugi-ta-cini/:category" element={<CategoryPage />} />
            <Route path={ROUTES.b2b} element={<B2BPage />} />
            <Route path="/dlya-biznesu/:page" element={<B2BDetailPage />} />
            <Route path={ROUTES.courier} element={<OrderPage />} />
            <Route path={ROUTES.cart} element={<CartPage />} />
            <Route path={ROUTES.checkout} element={<CheckoutPage />} />
            <Route path={ROUTES.orderSuccess} element={<OrderSuccessPage />} />
            <Route path={`${ROUTES.orderSuccess}/:orderId`} element={<OrderSuccessPage />} />
            <Route path={ROUTES.delivery} element={<DeliveryPage />} />
            <Route path={ROUTES.locations} element={<LocationsPage />} />
            <Route path={ROUTES.promotions} element={<PromotionsPage />} />
            <Route path="/aktsii/:id" element={<PromotionDetailPage />} />
            <Route path={ROUTES.contacts} element={<ContactsPage />} />
            <Route path={ROUTES.blog} element={<BlogPage />} />
            <Route path="/blog/:slug" element={<BlogPostPage />} />
            <Route path={ROUTES.oferta} element={<LegalPage doc="oferta" />} />
            <Route path={ROUTES.privacy} element={<LegalPage doc="privacy" />} />
            <Route path={ROUTES.umovy} element={<LegalPage doc="umovy" />} />
          </Routes>
        </main>

        <Footer />
        <BottomNav />
        <FloatingDock />
        <ScheduledPopupManager />
        <div className="h-[calc(5rem+env(safe-area-inset-bottom,0px))] lg:hidden" aria-hidden />
      </div>
    </div>
  );
}

export default function App() {
  return (
    <BrowserRouter>
      <BootstrapProvider>
        <CartProvider>
          <FeedbackProvider>
            <SeoManager />
            <ScrollManager />
            <AppShell />
          </FeedbackProvider>
        </CartProvider>
      </BootstrapProvider>
    </BrowserRouter>
  );
}
