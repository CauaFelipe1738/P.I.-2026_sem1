const settingsKey = "corpware-settings";
const defaultSettings = {
  theme: "dark",
  notifyTraining: true,
  feedbackSound: true,
  reduceMotion: false
};

const themeStatus = document.querySelector("#theme-status");
const toast = document.querySelector("#settings-toast");
const saveButton = document.querySelector("#save-settings");
const resetButton = document.querySelector("#reset-settings");
const notifyTraining = document.querySelector("#notify-training");
const feedbackSound = document.querySelector("#feedback-sound");
const reduceMotion = document.querySelector("#reduce-motion");
const themeInputs = Array.from(document.querySelectorAll('input[name="theme"]'));

let toastTimer;

function readSettings() {
  const savedSettings = localStorage.getItem(settingsKey);

  if (!savedSettings) {
    return { ...defaultSettings };
  }

  try {
    return { ...defaultSettings, ...JSON.parse(savedSettings) };
  } catch {
    return { ...defaultSettings };
  }
}

function getSettingsFromForm() {
  const selectedTheme = themeInputs.find((input) => input.checked);

  return {
    theme: selectedTheme ? selectedTheme.value : defaultSettings.theme,
    notifyTraining: notifyTraining.checked,
    feedbackSound: feedbackSound.checked,
    reduceMotion: reduceMotion.checked
  };
}

function getThemeName(theme) {
  const names = {
    dark: "Tema escuro",
    light: "Tema claro",
    pink: "Tema rosa"
  };

  return names[theme] || names.dark;
}

function showToast(message) {
  toast.textContent = message;
  toast.classList.add("is-visible");
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => {
    toast.classList.remove("is-visible");
  }, 2200);
}

function applySettings(settings) {
  document.body.classList.remove("theme-dark", "theme-light", "theme-pink", "reduce-motion");
  document.body.classList.add(`theme-${settings.theme}`);
  document.body.classList.toggle("reduce-motion", settings.reduceMotion);

  themeInputs.forEach((input) => {
    input.checked = input.value === settings.theme;
  });

  notifyTraining.checked = settings.notifyTraining;
  feedbackSound.checked = settings.feedbackSound;
  reduceMotion.checked = settings.reduceMotion;
  themeStatus.textContent = getThemeName(settings.theme);
}

function saveSettings(settings, message = "Preferências salvas com sucesso.") {
  localStorage.setItem(settingsKey, JSON.stringify(settings));
  applySettings(settings);
  showToast(message);
}

themeInputs.forEach((input) => {
  input.addEventListener("change", () => {
    const currentSettings = getSettingsFromForm();
    applySettings(currentSettings);
  });
});

reduceMotion.addEventListener("change", () => {
  document.body.classList.toggle("reduce-motion", reduceMotion.checked);
});

saveButton.addEventListener("click", () => {
  saveSettings(getSettingsFromForm());
});

resetButton.addEventListener("click", () => {
  saveSettings({ ...defaultSettings }, "Preferências restauradas para o padrão.");
});

applySettings(readSettings());
