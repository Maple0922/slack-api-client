import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: ["resources/css/app.scss", "resources/js/app.ts"],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            "@": `${__dirname}/resources/js`,
        },
    },
    server: {
        https: true,
        host: "0.0.0.0",
        hmr: {
            host: "localhost",
        },
    },
    build: {
        base: "https://slack-api-client.fly.dev/",
    },
});
