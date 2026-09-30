import logo from "@/logo/logo.svg";

/** Minimal full-screen wait until bootstrap/API catalog is ready. */
export default function AppPreloader() {
  return (
    <div className="app-preloader" role="status" aria-live="polite" aria-label="Завантаження">
      <div className="app-preloader__inner">
        <img src={logo} alt="ЄНОТ 24" className="app-preloader__logo" width={72} height={72} draggable={false} />
        <div className="app-preloader__spinner" aria-hidden />
        <p className="app-preloader__text">Завантажуємо послуги…</p>
      </div>
    </div>
  );
}
