window.onload = function() {
    var form = document.getElementById('formEvento');
    var isEdit = document.querySelector('input[name="id_evento"]') !== null;

    // Lógica para adicionar novos bilhetes dinamicamente (DOM)
    var btnAddBilhete = document.getElementById('btn-add-bilhete');
    if (btnAddBilhete) {
        btnAddBilhete.onclick = function() {
            var container = document.getElementById('container-bilhetes');
            
            var div = document.createElement('div');
            div.className = 'linha-bilhete';
            
            // O value="0" no bilhete_id avisa o backend que é um registo novo
            div.innerHTML = `
                <input type="hidden" name="bilhete_id[]" value="0">
                <input type="text" name="bilhete_nome[]" placeholder="Nome (ex: VIP)" class="input-bilhete">
                <input type="number" step="0.01" name="bilhete_preco[]" placeholder="Preço (€)" class="input-bilhete">
                <input type="number" name="bilhete_qtd[]" placeholder="Qtd Total" class="input-bilhete">
                <input type="date" name="bilhete_d_ini[]" title="Válido a partir de" class="input-bilhete">
                <input type="date" name="bilhete_d_fim[]" title="Válido até" class="input-bilhete">
                <button type="button" class="btn-remover-bilhete" onclick="this.parentElement.remove()">X</button>
            `;
            
            container.appendChild(div);
        };
    }

    // Validação no envio do formulário
    form.onsubmit = function(e) {
        var valid = true;

        // Limpa formatações de erro antigas
        var inputs = form.querySelectorAll('input, select, textarea');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].style.borderColor = "#ccc";
        }
        var msgs = form.querySelectorAll('.erro-msg');
        for (var j = 0; j < msgs.length; j++) {
            msgs[j].innerHTML = "";
        }

        // Validação: Nome
        var nome = document.getElementById('nome');
        if (nome.value.trim() === "") {
            mostrarErro(nome, "erro_nome", "O nome do evento é obrigatório.");
            valid = false;
        }

        // Validação: Categoria
        var categoria = document.getElementById('id_categoria');
        if (categoria.value === "") {
            mostrarErro(categoria, "erro_categoria", "Selecione uma categoria.");
            valid = false;
        }

        // Validação: Datas
        var dataInicio = document.getElementById('data_inicio');
        var dataFim = document.getElementById('data_fim');
        
        if (dataInicio.value === "") {
            mostrarErro(dataInicio, "erro_data_inicio", "A data de início é obrigatória.");
            valid = false;
        }
        if (dataFim.value === "") {
            mostrarErro(dataFim, "erro_data_fim", "A data de fim é obrigatória.");
            valid = false;
        }

        if (dataInicio.value !== "" && dataFim.value !== "") {
            var d1 = new Date(dataInicio.value);
            var d2 = new Date(dataFim.value);
            if (d2 <= d1) {
                mostrarErro(dataFim, "erro_data_fim", "A data de fim tem de ser posterior à data de início.");
                valid = false;
            }
        }

        // Validação: Local
        var local = document.getElementById('local');
        if (local.value.trim() === "") {
            mostrarErro(local, "erro_local", "O local é obrigatório.");
            valid = false;
        }

        // Validação: Descrição
        var descricao = document.getElementById('descricao');
        if (descricao.value.trim() === "") {
            mostrarErro(descricao, "erro_descricao", "A descrição é obrigatória.");
            valid = false;
        }

        // Validação: Imagem
        var imagem = document.getElementById('imagem');
        if (!isEdit && imagem.value === "") {
            mostrarErro(imagem, "erro_imagem", "Insira uma imagem para a capa do evento.");
            valid = false;
        }

        // Validação: Bilhetes (Itera sobre todos os inputs criados na array)
        var nomesBilhetes = document.getElementsByName('bilhete_nome[]');
        var precosBilhetes = document.getElementsByName('bilhete_preco[]');
        var qtdsBilhetes = document.getElementsByName('bilhete_qtd[]');
        var erroBilhetes = document.getElementById('erro_bilhetes');

        for (var k = 0; k < nomesBilhetes.length; k++) {
            if (nomesBilhetes[k].value.trim() === "" || precosBilhetes[k].value === "" || qtdsBilhetes[k].value === "") {
                erroBilhetes.innerHTML = "Preencha todos os campos obrigatórios (Nome, Preço e Qtd) nas linhas de bilhetes criadas.";
                nomesBilhetes[k].style.borderColor = "red";
                precosBilhetes[k].style.borderColor = "red";
                qtdsBilhetes[k].style.borderColor = "red";
                valid = false;
            }
        }

        // Impede o POST se houver erros
        if (!valid) {
            e.preventDefault(); 
        }
    };

    function mostrarErro(elemento, idErro, mensagem) {
        elemento.style.borderColor = "red"; 
        document.getElementById(idErro).innerHTML = mensagem; 
    }
};