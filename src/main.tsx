import React from "react";
import ReactDOM from "react-dom/client";
import { HelmetProvider } from "react-helmet-async";
import App from "./App";
import { ensureCsrf } from "./lib/api";
import { installLeadLogDebugGlobals } from "./lib/leadLogger";
import "./index.css";

installLeadLogDebugGlobals();

// Warm CSRF only when SPA and API share a site — cross-site SameSite=Lax
// cookies are rejected by the browser and only spam the console.
const apiBase = (import.meta.env.VITE_API_URL ?? "").replace(/\/$/, "");
const sameOriginApi = !apiBase || apiBase.startsWith(window.location.origin);
if (sameOriginApi) {
  void ensureCsrf().catch(() => {});
}

ReactDOM.createRoot(document.getElementById("root")!).render(
  <React.StrictMode>
    <HelmetProvider>
      <App />
    </HelmetProvider>
  </React.StrictMode>,
);

if ("serviceWorker" in navigator && import.meta.env.PROD) {
  window.addEventListener("load", () => {
    navigator.serviceWorker.register("/sw.js").catch(() => {});
  });
}
