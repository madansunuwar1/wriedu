// resources/js/app.js

import "./bootstrap";
import Alpine from "alpinejs";
import { createApp } from "vue";
import Toast, { POSITION } from "vue-toastification";
import "vue-toastification/dist/index.css";

// --- OUR NEW IMPORTS ---
import eventBus from "./utils/eventBus"; // Import our event bus

// Import root components
import App from "./App.vue";
import Admin from "./components/Admin.vue";

window.Alpine = Alpine;
Alpine.start();

// --- GLOBAL SECURITY SETUP (NO VUE) ---

// This part runs on every page load, regardless of the Vue app.
function setupGlobalSecurity() {
    const wrapper = document.getElementById("main-wrapper");
    const blurOverlay = document.getElementById("blur-overlay");
    let isSecure = false; // Default to not secure

    // Listen for the event from App.vue
    eventBus.on("auth-loaded", (authData) => {
        const allowedRoles = ["Administrator", "Manager"]; // Roles that are NOT in secure mode
        isSecure = !allowedRoles.includes(authData.user.role);

        if (wrapper) {
            wrapper.classList.toggle("secure-mode", isSecure);
        }
        console.log(`Security mode is now: ${isSecure ? "ON" : "OFF"}`);
    });

    // --- The security functions ---
    const handleContextMenu = (e) => {
        if (isSecure) e.preventDefault();
    };
    const activateBlur = () => {
        if (isSecure && blurOverlay) blurOverlay.style.display = "block";
    };
    const deactivateBlur = () => {
        if (blurOverlay) blurOverlay.style.display = "none";
    };
    const handleKeyUp = (e) => {
        if (e.key === "PrintScreen" && isSecure) {
            activateBlur();
            setTimeout(deactivateBlur, 300);
        }
    };

    // --- Attach listeners to the window ---
    window.addEventListener("contextmenu", handleContextMenu);
    window.addEventListener("blur", activateBlur);
    window.addEventListener("focus", deactivateBlur);
    document.addEventListener("keyup", handleKeyUp);
}

// Run the security setup immediately
setupGlobalSecurity();

// --- YOUR EXISTING APP MOUNTING LOGIC (Unchanged) ---
const toastOptions = {
    /* ... */
};

async function mountApps() {
    const adminElement = document.querySelector("#admin");
    const appElement = document.querySelector("#app");

    if (adminElement) {
        try {
            const { default: adminRouter } = await import("../router/admin.js");
            const adminApp = createApp(Admin);
            adminApp.use(adminRouter);
            adminApp.use(Toast, toastOptions);
            adminApp.mount("#admin");
        } catch (error) {
            console.error("Failed to mount admin app:", error);
        }
    } else if (appElement) {
        try {
            const { default: mainRouter } = await import("../router/index.js");
            const app = createApp(App); // Using your App.vue
            app.use(mainRouter);
            app.use(Toast, toastOptions);
            app.mount("#app");
        } catch (error) {
            console.error("Failed to mount main app:", error);
        }
    }
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", mountApps);
} else {
    mountApps();
}
