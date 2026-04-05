const toggleButton = document.getElementById("toggle-btn");
const sidebar = document.getElementById("sidebar");

function toggleSidebar() {
    sidebar.classList.toggle("close");
    toggleButton.classList.toggle("rotate");

    Array.from(sidebar.getElementsByClassName("show")).forEach((ul) => {
        ul.classList.remove("show");
        ul.previousElementSibling.classList.remove("rotate");
    });
}

function toggleSubMenu(button) {
    if (!button.nextElementSibling.classList.contains("show")) {
        closeAllSubMenu();
    }
    button.nextElementSibling.classList.toggle("show");
    button.classList.toggle("rotate");

    if (sidebar.classList.contains("close")) {
        sidebar.classList.toggle("close");
        toggleButton.classList.toggle("rotate");
    }
}

function closeAllSubMenu() {
    Array.from(sidebar.getElementsByClassName("show")).forEach((ul) => {
        ul.classList.remove("show");
        if (ul.previousElementSibling) {
            ul.previousElementSibling.classList.remove("rotate");
        }
    });
}

// Attach events if elements exist
if (toggleButton) {
    toggleButton.addEventListener("click", toggleSidebar);
}

const menuLinks = document.querySelectorAll("#sidebar ul > li > a");

menuLinks.forEach((link) => {
    link.addEventListener("click", () => {
        // hapus active dari semua li
        document
            .querySelectorAll("#sidebar ul li.active")
            .forEach((li) => li.classList.remove("active"));

        // set active ke li parent
        link.parentElement.classList.add("active");
    });
});
