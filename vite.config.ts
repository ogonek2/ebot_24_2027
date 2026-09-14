import { defineConfig, loadEnv } from "vite";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";
import path from "node:path";

/**
 * Modes:
 * - development / production → standalone SPA (`dist/`, base `/`) for enot.qpanel-erp.online
 * - laravel → optional embed into Laravel `public/build` (local monolith only)
 */
export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), "");
  // Prefer remote/local API URL for proxy when set (storage + optional same-origin paths)
  const apiTarget =
    (env.VITE_API_URL || env.VITE_API_PROXY || "http://127.0.0.1:8000").replace(/\/$/, "");
  const isLaravel = mode === "laravel";

  return {
    plugins: [react(), tailwindcss()],
    resolve: {
      alias: {
        "@": path.resolve(__dirname, "src"),
      },
    },
    css: {
      postcss: {
        plugins: [],
      },
    },
    build: {
      outDir: isLaravel
        ? path.resolve(__dirname, "../enot_24_git_version/public/build")
        : path.resolve(__dirname, "dist"),
      emptyOutDir: true,
      assetsDir: "assets",
    },
    base: isLaravel ? "/build/" : "/",
    server: {
      host: "127.0.0.1",
      port: 5173,
      strictPort: true,
      proxy: {
        "/api": { target: apiTarget, changeOrigin: true },
        "/sanctum": { target: apiTarget, changeOrigin: true },
        "/storage": { target: apiTarget, changeOrigin: true },
        "/manifest.webmanifest": { target: apiTarget, changeOrigin: true },
        "/sw.js": { target: apiTarget, changeOrigin: true },
      },
    },
  };
});
