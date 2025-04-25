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
        host: "localhost",
        hmr: {
            host: "localhost",
        },
    },
});
