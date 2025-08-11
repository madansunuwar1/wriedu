var userSettings = {
  Layout: "vertical", // vertical | horizontal
  SidebarType: "full", // full | mini-sidebar
  BoxedLayout: true, // true | false
  Direction: "ltr", // ltr | rtl
  Theme: "light", // light | dark
  ColorTheme: "Blue_Theme", // Blue_Theme | Aqua_Theme | Purple_Theme | Green_Theme | Cyan_Theme | Orange_Theme
  cardBorder: false, // true | false
};

// Load settings from localStorage
function loadSettings() {
  const savedSettings = localStorage.getItem('userSettings');
  if (savedSettings) {
    userSettings = JSON.parse(savedSettings);
    applySettings();
  }
}

// Save settings to localStorage
function saveSettings() {
  localStorage.setItem('userSettings', JSON.stringify(userSettings));
}

// Apply settings to the DOM
function applySettings() {
  document.documentElement.setAttribute('data-layout', userSettings.Layout);
  document.documentElement.setAttribute('data-sidebar', userSettings.SidebarType);
  document.documentElement.setAttribute('data-boxed', userSettings.BoxedLayout);
  document.documentElement.setAttribute('data-direction', userSettings.Direction);
  document.documentElement.setAttribute('data-theme', userSettings.Theme);
  document.documentElement.setAttribute('data-color-theme', userSettings.ColorTheme);
  document.documentElement.setAttribute('data-card-border', userSettings.cardBorder);
}

// Initialize settings
loadSettings();
