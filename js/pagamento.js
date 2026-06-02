// js/pagamentos.js

window.onload = function() {
    var form = document.getElementById('form-pagamento');
    
    // Associa a função de validação ao evento de submissão do formulário
    form.onsubmit = function(evento) {
        var valido = true;

        // 1. Obter os valores dos campos usando document.getElementById
        var campoNome = document.getElementById('nome_titular');
        var campoCartao = document.getElementById('num_cartao');
        var campoValidade = document.getElementById('data_validade');
        var campoCVC = document.getElementById('cvc');
        var campoNIF = document.getElementById('nif');

        // Função auxiliar para marcar campo como inválido (Altera objeto e mostra mensagem)
        function marcarInvalido(campo, idErro, mensagem) {
            campo.style.borderColor = '#d9534f'; // Vermelho
            campo.style.backgroundColor = '#fdf0f0'; // Fundo avermelhado
            document.getElementById(idErro).innerHTML = mensagem;
            valido = false;
        }

        // Função auxiliar para marcar campo como válido
        function marcarValido(campo, idErro) {
            campo.style.borderColor = '#5cb85c'; // Verde
            campo.style.backgroundColor = 'white';
            document.getElementById(idErro).innerHTML = "";
        }

        // --- VALIDAÇÃO: NOME (Obrigatório, pelo menos 3 letras) ---
        var expNome = /^[a-zA-ZÀ-ÿ\s]{3,}$/;
        if (campoNome.value.trim() === "") {
            marcarInvalido(campoNome, 'erro-nome', 'O nome é de preenchimento obrigatório.');
        } else if (!expNome.test(campoNome.value)) {
            marcarInvalido(campoNome, 'erro-nome', 'Insira um nome válido (apenas letras).');
        } else {
            marcarValido(campoNome, 'erro-nome');
        }

        // --- VALIDAÇÃO: NÚMERO DO CARTÃO (16 dígitos) ---
        // A expressão \d detecta algarismos de 0 a 9 e {16} obriga a ser exatamente 16.
        var cartaoLimpo = campoCartao.value.replace(/\s/g, ''); // Remove espaços em branco
        var expCartao = /^\d{16}$/;
        if (cartaoLimpo === "") {
            marcarInvalido(campoCartao, 'erro-cartao', 'O número do cartão é obrigatório.');
        } else if (!expCartao.test(cartaoLimpo)) {
            marcarInvalido(campoCartao, 'erro-cartao', 'O cartão deve ter exatamente 16 dígitos.');
        } else {
            marcarValido(campoCartao, 'erro-cartao');
        }

        // --- VALIDAÇÃO: VALIDADE (Formato MM/AA) ---
        var expValidade = /^(0[1-9]|1[0-2])\/\d{2}$/;
        if (campoValidade.value.trim() === "") {
            marcarInvalido(campoValidade, 'erro-validade', 'A validade é obrigatória.');
        } else if (!expValidade.test(campoValidade.value)) {
            marcarInvalido(campoValidade, 'erro-validade', 'Use o formato MM/AA (ex: 12/28).');
        } else {
            marcarValido(campoValidade, 'erro-validade');
        }

        // --- VALIDAÇÃO: CVC (Exatamente 3 dígitos) ---
        var expCVC = /^\d{3}$/;
        if (campoCVC.value.trim() === "") {
            marcarInvalido(campoCVC, 'erro-cvc', 'Obrigatório.');
        } else if (!expCVC.test(campoCVC.value)) {
            marcarInvalido(campoCVC, 'erro-cvc', 'São exigidos 3 dígitos.');
        } else {
            marcarValido(campoCVC, 'erro-cvc');
        }

        // --- VALIDAÇÃO: NIF (Opcional, mas se preenchido deve ter 9 dígitos) ---
        var expNIF = /^\d{9}$/;
        if (campoNIF.value.trim() !== "") { // Só valida se o utilizador escreveu algo
            if (!expNIF.test(campoNIF.value)) {
                marcarInvalido(campoNIF, 'erro-nif', 'O NIF deve ter exatamente 9 dígitos.');
            } else {
                marcarValido(campoNIF, 'erro-nif');
            }
        } else {
            // Se estiver vazio (como é opcional), repõe a cor padrão
            campoNIF.style.borderColor = '#ccc';
            campoNIF.style.backgroundColor = 'white';
            document.getElementById('erro-nif').innerHTML = "";
        }

        // Se algum dos campos falhou a validação, previne o envio do form para o PHP
        if (!valido) {
            evento.preventDefault(); 
        }
    };
};