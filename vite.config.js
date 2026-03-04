import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/css/trainer/main.css",
                "resources/js/trainer/sidebar.js",
                "resources/css/trainer/course.css",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
