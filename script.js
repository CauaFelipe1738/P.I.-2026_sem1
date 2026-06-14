const senhaInput = document.getElementById("senha");
const credentials = { email: "admin@gmail.com", password: "123456" };
let senhaReal = "";
let hideTimer;

senhaInput.type = "password";
senhaInput.value = "";

function renderSenha(showLast = true) {
  const masked = "•".repeat(Math.max(senhaReal.length - (showLast ? 1 : 0), 0));
  senhaInput.value = showLast && senhaReal ? masked + senhaReal.at(-1) : "•".repeat(senhaReal.length);
}

senhaInput.addEventListener("keydown", (event) => {
  event.preventDefault();
  if (event.key === "Backspace") senhaReal = senhaReal.slice(0, -1);
  if (event.key.length === 1) senhaReal += event.key;

  renderSenha();
  clearTimeout(hideTimer);
  hideTimer = setTimeout(() => renderSenha(false), 1500);
});

function login() {
  const email = document.getElementById("email").value.trim();
  const mensagem = document.getElementById("mensagem");
  const valid = email === credentials.email && senhaReal === credentials.password;

  mensagem.textContent = valid ? "" : "Email ou senha incorretos!";
  mensagem.style.color = "red";
  if (valid) window.location.href = "./dashboard/dashboard.html";
  return valid;
}
