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
                "resources/css/trainer/dashboard.css",
                "resources/css/trainer/course.css",
                "resources/css/trainer/detail-course.css",
                "resources/css/trainer/events.css",
                "resources/css/trainer/detail-event.css",
                "resources/css/trainer/feedback.css",               
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
