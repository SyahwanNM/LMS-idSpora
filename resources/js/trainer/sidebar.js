// resources/js/trainer/sidebar.js

document.addEventListener("DOMContentLoaded", function () {
    const toggleButton = document.getElementById("toggle-btn");
    const sidebar = document.getElementById("sidebar");

    if (
        sidebar &&
        document.documentElement.classList.contains("sidebar-collapsed")
    ) {
        sidebar.classList.add("close");
        if (toggleButton) {
            toggleButton.classList.add("rotate");
        }
    }

    // 1. Logic Toggle Sidebar (Buka/Tutup)
    if (toggleButton && sidebar) {
        toggleButton.addEventListener("click", function () {
            sidebar.classList.toggle("close");

            // Putar icon toggle jika perlu
            toggleButton.classList.toggle("rotate");

            // Tutup semua submenu saat sidebar mengecil
            if (sidebar.classList.contains("close")) {
                closeAllSubMenu();
            }

            const isCollapsed = sidebar.classList.contains("close");
            document.documentElement.classList.toggle(
                "sidebar-collapsed",
                isCollapsed,
            );

            try {
                localStorage.setItem(
                    "sidebar-state",
                    isCollapsed ? "closed" : "open",
                );
                localStorage.setItem(
                    "trainerSidebarCollapsed",
                    isCollapsed ? "1" : "0",
                );
            } catch (e) {}
        });
    }

    setTimeout(() => {
        document.documentElement.classList.add("sidebar-ready");
    }, 50);
});

// Helper: Toggle Submenu (Jika nanti ada dropdown di sidebar)
window.toggleSubMenu = function (button) {
    const sidebar = document.getElementById("sidebar");

    if (!button.nextElementSibling.classList.contains("show")) {
        closeAllSubMenu();
    }

    button.nextElementSibling.classList.toggle("show");
    button.classList.toggle("rotate");

    // Jika sidebar sedang tertutup dan submenu diklik, buka sidebar
    if (sidebar && sidebar.classList.contains("close")) {
        sidebar.classList.toggle("close");
    }
};

// Helper: Tutup semua submenu
function closeAllSubMenu() {
    const sidebar = document.getElementById("sidebar");
    if (!sidebar) return;

    Array.from(sidebar.getElementsByClassName("show")).forEach((ul) => {
        ul.classList.remove("show");
        if (ul.previousElementSibling) {
            ul.previousElementSibling.classList.remove("rotate");
        }
    });
}
