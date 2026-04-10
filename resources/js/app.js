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

// Global flash-success toast (used for cross-page redirects)
document.addEventListener('DOMContentLoaded', () => {
    const STORAGE_KEY = 'idspora_flash_success';
    let message = null;
    try {
        message = localStorage.getItem(STORAGE_KEY);
        if (message) localStorage.removeItem(STORAGE_KEY);
    } catch (_e) {
        message = null;
    }
    if (!message) return;

    function ensureStyles() {
        if (document.getElementById('flash-toast-styles')) return;
        const style = document.createElement('style');
        style.id = 'flash-toast-styles';
        style.textContent = `
            .flash-toast-container{position:fixed;top:1rem;right:1rem;display:flex;flex-direction:column;gap:.75rem;z-index:1080;max-width:340px;width:100%;}
            .flash-toast{--flash-bg:#fff;--flash-border:#e5e7eb;--flash-color:#111827;--flash-accent:#16a34a;position:relative;display:flex;align-items:flex-start;gap:.75rem;padding:.9rem 1rem .95rem 1rem;border:1px solid var(--flash-border);background:linear-gradient(135deg,var(--flash-bg) 0%,#f9fafb 100%);border-radius:14px;box-shadow:0 8px 24px -8px rgba(0,0,0,.18),0 1px 3px rgba(0,0,0,.08);transform:translateY(-8px) scale(.96);opacity:0;transition:transform .45s cubic-bezier(.16,.8,.24,1),opacity .45s ease;overflow:hidden;font-size:.875rem;color:var(--flash-color);}
            .flash-toast.show{transform:translateY(0) scale(1);opacity:1;}
            .flash-toast.closing{opacity:0;transform:translateY(-6px) scale(.95);}
            .flash-icon{flex:0 0 auto;display:flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:12px;background:var(--flash-accent);color:#fff;}
            .flash-body{flex:1 1 auto;min-width:0;}
            .flash-title{font-weight:700;line-height:1.1;margin-top:.15rem;margin-bottom:.15rem;}
            .flash-message{line-height:1.35;opacity:.95;word-break:break-word;}
            .flash-close{border:0;background:transparent;color:#6b7280;font-size:1.1rem;line-height:1;padding:.15rem .25rem;margin-left:.25rem;cursor:pointer;}
            .flash-close:hover{color:#111827;}
        `;
        document.head.appendChild(style);
    }

    function ensureContainer() {
        let container = document.querySelector('.flash-toast-container');
        if (container) return container;
        container = document.createElement('div');
        container.className = 'flash-toast-container';
        container.setAttribute('aria-live', 'polite');
        container.setAttribute('aria-atomic', 'true');
        document.body.appendChild(container);
        return container;
    }

    function showSuccessToast(msg) {
        ensureStyles();
        const container = ensureContainer();
        const toast = document.createElement('div');
        toast.className = 'flash-toast flash-success';
        toast.setAttribute('role', 'status');
        toast.innerHTML = `
            <div class="flash-icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill="currentColor" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M6.97 11.03 13 5l-1.06-1.06-4.97 4.95L4.53 7.47 3.47 8.53z"/>
                </svg>
            </div>
            <div class="flash-body">
                <div class="flash-title">Berhasil</div>
                <div class="flash-message"></div>
            </div>
            <button class="flash-close" type="button" aria-label="Tutup">&times;</button>
        `;
        toast.querySelector('.flash-message').textContent = msg;
        container.appendChild(toast);
        requestAnimationFrame(() => toast.classList.add('show'));
        const dismiss = () => {
            if (toast.classList.contains('closing')) return;
            toast.classList.add('closing');
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 450);
        };
        toast.querySelector('.flash-close')?.addEventListener('click', dismiss);
        setTimeout(dismiss, 4500);
    }

    showSuccessToast(message);
});
