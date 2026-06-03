const corpwareSettings = localStorage.getItem("corpware-settings");

try {
  const settings = corpwareSettings ? JSON.parse(corpwareSettings) : {};
  const theme = settings.theme || "dark";

  document.body.classList.remove("theme-dark", "theme-light", "theme-pink", "reduce-motion");
  document.body.classList.add(`theme-${theme}`);
  document.body.classList.toggle("reduce-motion", Boolean(settings.reduceMotion));
} catch {
  document.body.classList.add("theme-dark");
}
