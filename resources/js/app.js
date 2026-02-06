// Enable click on search icon to focus input
document.addEventListener("DOMContentLoaded", () => {
    const searchIcon = document.getElementById("search-icon-svg");
    const searchInput = document.getElementById("site-search");
    if (searchIcon && searchInput) {
        searchIcon.addEventListener("click", () => {
            searchInput.focus();
        });
    }
});
import 'bootstrap';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// Manual Carousel Initialization for reliability
document.addEventListener("DOMContentLoaded", () => {
    const carousels = document.querySelectorAll('.carousel');
    carousels.forEach(c => {
        new bootstrap.Carousel(c, {
            ride: 'carousel',
            interval: 5000
        });
    });
});
document.addEventListener("DOMContentLoaded", () => {
    const wrap = document.querySelector(".search-wrap");
    const input = document.getElementById("site-search");
    const list = document.getElementById("search-suggest");

    // Hanya inisialisasi fitur search jika elemen-elemennya tersedia
    if (wrap && input && list) {
        // contoh dataset; ganti/isi dari server sesuai kebutuhanmu
        const SUGGESTIONS = [
            "React",
            "React Native",
            "UI/UX Design",
            "Figma Auto Layout",
            "Data Science",
            "Python Dasar",
            "Python untuk Data Analysis",
            "JavaScript ES6",
            "Laravel",
            "SQL & Database",
            "Business Analysis",
            "Machine Learning",
            "Tailwind CSS",
            "Docker",
            "Git & GitHub",
        ];

        let activeIndex = -1; // untuk navigasi keyboard

        function render(items) {
            list.innerHTML = items
                .map(
                    (text, i) => `<li role="option" data-index="${i}">${text}</li>`
                )
                .join("");
        }

        function open() {
            wrap.classList.add("is-open");
            input.setAttribute("aria-expanded", "true");
        }

        function close() {
            wrap.classList.remove("is-open");
            input.setAttribute("aria-expanded", "false");
            activeIndex = -1;
        }

        function filter(query) {
            const q = query.trim().toLowerCase();
            if (!q) return [];
            return SUGGESTIONS.filter((s) => s.toLowerCase().includes(q)).slice(
                0,
                8
            );
        }

        // events
        input.addEventListener("focus", () => {
            const items = filter(input.value);
            if (items.length) {
                render(items);
                open();
            }
        });

        input.addEventListener("input", () => {
            const items = filter(input.value);
            if (items.length) {
                render(items);
                open();
            } else {
                close();
            }
        });

        // klik suggestion
        list.addEventListener("click", (e) => {
            const li = e.target.closest("li");
            if (!li) return;
            input.value = li.textContent;
            close();
            // optional: submit form
            // input.form?.submit();
        });

        // keyboard nav
        input.addEventListener("keydown", (e) => {
            const items = [...list.querySelectorAll("li")];
            if (!wrap.classList.contains("is-open") || items.length === 0) return;

            if (e.key === "ArrowDown") {
                e.preventDefault();
                activeIndex = (activeIndex + 1) % items.length;
            } else if (e.key === "ArrowUp") {
                e.preventDefault();
                activeIndex = (activeIndex - 1 + items.length) % items.length;
            } else if (e.key === "Enter") {
                if (activeIndex >= 0) {
                    e.preventDefault();
                    input.value = items[activeIndex].textContent;
                    close();
                    // input.form?.submit();
                }
                return;
            } else if (e.key === "Escape") {
                close();
                return;
            } else {
                return; // biarkan key lain
            }

            items.forEach((li) => li.classList.remove("is-active"));
            if (activeIndex >= 0) {
                items[activeIndex].classList.add("is-active");
                items[activeIndex].scrollIntoView({ block: "nearest" });
            }
        });

        // tutup jika klik di luar
        document.addEventListener("click", (e) => {
            if (wrap && !wrap.contains(e.target)) close();
        });
    }
});
