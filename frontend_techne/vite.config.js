// frontend_techne/vite.config.js
import { fileURLToPath, URL } from "node:url";
import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
  base: "/",

  plugins: [vue()],

  resolve: {
    alias: {
      "@": fileURLToPath(new URL("./src", import.meta.url)),
    },
  },

  server: {
    host: true,
    port: 5173,
    strictPort: false,
    allowedHosts: [
      ".ngrok-free.app",
      ".ngrok-free.dev",
      ".ngrok.io",
      "localhost",
    ],
    hmr: {
      protocol: "ws",
      host: "localhost",
    },
  },

  preview: {
    host: true,
    port: 4173,
    allowedHosts: [
      ".ngrok-free.app",
      ".ngrok-free.dev",
      ".ngrok.io",
      "localhost",
    ],
  },
});