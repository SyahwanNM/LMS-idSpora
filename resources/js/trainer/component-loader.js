async function loadComponent(element) {
  const componentName = element.getAttribute('data-component');
  const componentPath = element.getAttribute('data-path') || '../components/';
  
  try {
    const response = await fetch(`${componentPath}${componentName}.html`);
    if (!response.ok) {
      throw new Error(`Failed to load component: ${componentName}`);
    }
    
    const html = await response.text();
    element.innerHTML = html;
    
    // Trigger custom event after component is loaded
    const event = new CustomEvent('componentLoaded', { 
      detail: { componentName, element } 
    });
    element.dispatchEvent(event);
    
  } catch (error) {
    console.error(`Error loading component ${componentName}:`, error);
    element.innerHTML = `<div style="color: red;">Failed to load component: ${componentName}</div>`;
  }
}

// Function to load all components on page
async function loadAllComponents() {
  const components = document.querySelectorAll('[data-component]');
  const loadPromises = Array.from(components).map(element => loadComponent(element));
  
  await Promise.all(loadPromises);
  
  // After all components loaded, initialize sidebar if it exists
  initializeSidebar();
}

// Initialize sidebar functionality
function initializeSidebar() {
  const toggleButton = document.getElementById("toggle-btn");
  const sidebar = document.getElementById("sidebar");
  
  if (!toggleButton || !sidebar) return;
  
  // Set active menu item based on current page
  setActiveMenuItem();
  
  // Sidebar toggle
  toggleButton.addEventListener("click", function() {
    sidebar.classList.toggle("close");
    toggleButton.classList.toggle("rotate");
    
    Array.from(sidebar.getElementsByClassName("show")).forEach((ul) => {
      ul.classList.remove("show");
      ul.previousElementSibling.classList.remove("rotate");
    });
  });
  
  // Menu item click handlers
  const menuLinks = document.querySelectorAll("#sidebar ul > li > a");
  menuLinks.forEach(link => {
    link.addEventListener("click", () => {
      document.querySelectorAll("#sidebar ul li.active")
        .forEach(li => li.classList.remove("active"));
      link.parentElement.classList.add("active");
    });
  });
}

// Set active menu item based on current page
function setActiveMenuItem() {
  const currentPath = window.location.pathname;
  const menuItems = document.querySelectorAll("#sidebar ul > li");
  
  menuItems.forEach(li => {
    li.classList.remove("active");
    const link = li.querySelector("a");
    if (link && currentPath.includes(link.getAttribute("href"))) {
      li.classList.add("active");
    }
  });
}

// Submenu toggle function (needs to be global for onclick handlers)
window.toggleSubMenu = function(button) {
  const sidebar = document.getElementById("sidebar");
  const toggleButton = document.getElementById("toggle-btn");
  
  if (!button.nextElementSibling.classList.contains("show")) {
    closeAllSubMenu();
  }
  button.nextElementSibling.classList.toggle("show");
  button.classList.toggle("rotate");
  
  if (sidebar && sidebar.classList.contains("close")) {
    sidebar.classList.toggle("close");
    if (toggleButton) {
      toggleButton.classList.toggle("rotate");
    }
  }
}

// Close all submenus
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

// Load components when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', loadAllComponents);
} else {
  loadAllComponents();
}

// Export for use in other scripts
export { loadComponent, loadAllComponents, initializeSidebar };
