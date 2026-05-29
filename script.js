const senhaInput = document.getElementById("senha");

senhaInput.type = "password";
senhaInput.value = "";

let senhaReal = "";
let timeoutSenha = null;
let intervaloTimer = null;

// DIGITAR SENHA
senhaInput.addEventListener("keydown", function (e) {
    e.preventDefault();

    if (e.key === "Backspace") {
        senhaReal = senhaReal.slice(0, -1);
    } else if (e.key.length === 1) {
        senhaReal += e.key;
    }

    atualizarSenha();
});

// ATUALIZAR VISUAL DA SENHA
function atualizarSenha() {
    clearTimeout(timeoutSenha);
    clearInterval(intervaloTimer);

    let tempo = 1.5;

    let texto = "";

    if (senhaReal.length > 1) {
        texto = "•".repeat(senhaReal.length - 1);
    }

    if (senhaReal.length > 0) {
        texto += senhaReal[senhaReal.length - 1];
    }

    senhaInput.value = texto;

    console.log(`Último dígito será escondido em ${tempo}s`);

    intervaloTimer = setInterval(() => {
        tempo -= 0.5;

        if (tempo > 0) {
            console.log(`Último dígito será escondido em ${tempo}s`);
        }
    }, 500);

    timeoutSenha = setTimeout(() => {
        senhaInput.value = "•".repeat(senhaReal.length);
        clearInterval(intervaloTimer);
        console.log("Último dígito escondido");
    }, 1500);
}

// LOGIN
function login() {
    const email = document.getElementById("email").value.trim();
    const mensagem = document.getElementById("mensagem");

    const emailCorreto = "admin@gmail.com";
    const senhaCorreta = "123456";

    mensagem.innerText = "";

    if (email !== emailCorreto || senhaReal !== senhaCorreta) {
        mensagem.style.color = "red";
        mensagem.innerText = "Email ou senha incorretos!";
        return false;
    }

    window.location.href = "./dashboard/dashboard.html";
    return true;
}
