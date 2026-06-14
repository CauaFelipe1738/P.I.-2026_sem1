document.addEventListener("DOMContentLoaded", () => {
  const tooltip = Object.assign(document.createElement("div"), { className: "tooltip" });
  document.body.appendChild(tooltip);

  document.querySelectorAll(".list-row em").forEach((item) => {
    item.addEventListener("mouseenter", () => {
      const text = item.dataset.tooltip;
      if (!text) return;
      tooltip.textContent = text;
      tooltip.classList.add("active");
    });
    item.addEventListener("mousemove", ({ pageX, pageY }) => {
      tooltip.style.left = `${pageX + 15}px`;
      tooltip.style.top = `${pageY + 15}px`;
    });
    item.addEventListener("mouseleave", () => tooltip.classList.remove("active"));
  });
});
