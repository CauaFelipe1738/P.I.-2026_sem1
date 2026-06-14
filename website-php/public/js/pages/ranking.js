document.addEventListener('DOMContentLoaded', () => {
    const titulos = document.querySelectorAll("[data-tooltip]");

    const tooltip = document.createElement("div");
    tooltip.classList.add("tooltip");
    document.body.appendChild(tooltip);

    titulos.forEach((item) => {
        item.addEventListener("mouseenter", (e) => {
            const texto = e.target.getAttribute("data-tooltip");
            if (texto) {
                tooltip.textContent = texto;
                tooltip.classList.add("active");
            }
        });

        item.addEventListener("mousemove", (e) => {
            tooltip.style.left = e.pageX + 15 + "px";
            tooltip.style.top = e.pageY + 15 + "px";
        });

        item.addEventListener("mouseleave", () => {
            tooltip.classList.remove("active");
        });
    });
});
