window.onload = function() {
    var form = document.getElementById('form-pagamento');
    if (!form) return;

    var campoNome = document.getElementById('nome_titular');
    var campoCartao = document.getElementById('num_cartao');
    var campoValidade = document.getElementById('data_validade');
    var campoCVC = document.getElementById('cvc');
    var campoNIF = document.getElementById('nif');

    // FUNÇÕES DE ESTILIZAÇÃO VISUAL
    function marcarInvalido(campo, idErro, mensagem) {
        campo.style.borderColor = '#d9534f'; 
        campo.style.backgroundColor = '#fdf0f0';
        document.getElementById(idErro).innerHTML = mensagem;
        return false;
    }

    function marcarValido(campo, idErro) {
        campo.style.borderColor = '#5cb85c'; 
        campo.style.backgroundColor = '#f4fdf4'; 
        document.getElementById(idErro).innerHTML = "";
        return true;
    }

    function limparEstado(campo, idErro) {
        campo.style.borderColor = '#ccc';
        campo.style.backgroundColor = 'white';
        document.getElementById(idErro).innerHTML = "";
        return true;
    }

    // REGRAS DE VALIDAÇÃO
    function validarNome() {
        if (campoNome.value == "" || /^[\s]+$/.test(campoNome.value)) {
            return marcarInvalido(campoNome, 'erro-nome', 'O nome é obrigatório.');
        }
        if (!/^[a-zA-ZÀ-ÿ\s]{3,}$/.test(campoNome.value)) {
            return marcarInvalido(campoNome, 'erro-nome', 'Insira apenas letras.');
        }
        return marcarValido(campoNome, 'erro-nome');
    }

    function validarCartao() {
        var val = campoCartao.value.replace(/\s/g, ''); 
        if (val == "") return marcarInvalido(campoCartao, 'erro-cartao', 'Obrigatório.');
        if (!/^\d{16}$/.test(val)) return marcarInvalido(campoCartao, 'erro-cartao', 'Faltam dígitos (Exige 16).');
        return marcarValido(campoCartao, 'erro-cartao');
    }

    function validarValidade() {
        if (campoValidade.value == "") return marcarInvalido(campoValidade, 'erro-validade', 'Obrigatório.');
        if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(campoValidade.value)) return marcarInvalido(campoValidade, 'erro-validade', 'Use MM/AA.');
        return marcarValido(campoValidade, 'erro-validade');
    }

    function validarCVC() {
        if (campoCVC.value == "") return marcarInvalido(campoCVC, 'erro-cvc', 'Obrigatório.');
        if (!/^\d{3}$/.test(campoCVC.value)) return marcarInvalido(campoCVC, 'erro-cvc', 'Exige 3 dígitos.');
        return marcarValido(campoCVC, 'erro-cvc');
    }

    function validarNIF() {
        var val = campoNIF.value;
        if (val == "" || /^[\s]+$/.test(val)) return limparEstado(campoNIF, 'erro-nif');
        if (!/^\d{9}$/.test(val)) return marcarInvalido(campoNIF, 'erro-nif', 'O NIF exige exatamente 9 dígitos.');
        return marcarValido(campoNIF, 'erro-nif');
    }

    // EVENTOS
    campoNome.onkeyup = validarNome;
    
    campoCartao.onkeyup = function() {
        this.value = this.value.replace(/\D/g, ''); // Apaga letras instantaneamente
        validarCartao();
    };

    campoCVC.onkeyup = function() {
        this.value = this.value.replace(/\D/g, '');
        validarCVC();
    };

    campoNIF.onkeyup = function() {
        this.value = this.value.replace(/\D/g, '');
        validarNIF();
    };

    campoValidade.onkeyup = function() {
        this.value = this.value.replace(/[^0-9\/]/g, '');
        // Usamos o .search() do Guião em vez do .includes()
        if (this.value.length == 2 && this.value.search("/") == -1) {
            this.value = this.value + '/';
        }
        validarValidade();
    };

    //  VERIFICAÇÃO FINAL
    form.onsubmit = function() {
        var isNomeValid = validarNome();
        var isCartaoValid = validarCartao();
        var isValidadeValid = validarValidade();
        var isCVCValid = validarCVC();
        var isNIFValid = validarNIF();

        if (!isNomeValid || !isCartaoValid || !isValidadeValid || !isCVCValid || !isNIFValid) {
            alert('Atenção: Existem campos com erros ou não preenchidos. Por favor, verifique as caixas a vermelho.');
            return false; // Este return false impede o HTML de fazer o POST para o PHP!
        }
        return true; // Deixa avançar para a compra
    };
};