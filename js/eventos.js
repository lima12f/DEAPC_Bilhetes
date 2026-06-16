// js/eventos.js
window.onload = function() {
    var form = document.getElementById('formEvento');
    var isEdit = document.querySelector('input[name="id_evento"]') !== null;

    // Lógica para adicionar novos bilhetes dinamicamente (DOM) com classes limpas
    var btnAddBilhete = document.getElementById('btn-add-bilhete');
    if (btnAddBilhete) {
        btnAddBilhete.onclick = function() {
            var container = document.getElementById('container-bilhetes');
            var div = document.createElement('div');
            div.className = 'linha-bilhete';
            
            div.innerHTML = `
                <input type="hidden" name="bilhete_id[]" value="0">
                <input type="text" name="bilhete_nome[]" placeholder="Ex: VIP" class="input-bilhete">
                <input type="number" step="0.01" name="bilhete_preco[]" placeholder="Preço" class="input-bilhete">
                <input type="number" name="bilhete_qtd[]" placeholder="Qtd" class="input-bilhete">
                <input type="date" name="bilhete_d_ini[]" title="Válido a partir de" class="input-bilhete">
                <input type="date" name="bilhete_d_fim[]" title="Válido até" class="input-bilhete">
                <button type="button" class="btn-remover-bilhete" onclick="this.parentElement.remove()">X</button>
            `;
            container.appendChild(div);
        };
    }

    form.onsubmit = function(e) {
        var valid = true;

        // Limpa erros antigos
        var inputs = form.querySelectorAll('input, select, textarea');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].style.borderColor = "#ccc";
        }
        var msgs = form.querySelectorAll('.erro-msg');
        for (var j = 0; j < msgs.length; j++) {
            msgs[j].innerHTML = "";
        }

        var nome = document.getElementById('nome');
        if (nome.value.trim() === "") { mostrarErro(nome, "erro_nome", "Obrigatório."); valid = false; }

        var categoria = document.getElementById('id_categoria');
        if (categoria.value === "") { mostrarErro(categoria, "erro_categoria", "Obrigatório."); valid = false; }

        var local = document.getElementById('local');
        if (local.value.trim() === "") { mostrarErro(local, "erro_local", "Obrigatório."); valid = false; }

        var descricao = document.getElementById('descricao');
        if (descricao.value.trim() === "") { mostrarErro(descricao, "erro_descricao", "Obrigatória."); valid = false; }

        var imagem = document.getElementById('imagem');
        if (!isEdit && imagem.value === "") { mostrarErro(imagem, "erro_imagem", "Insira uma imagem para a capa."); valid = false; }

        // --- VALIDAÇÃO DE DATAS DO EVENTO ---
        var dataInicio = document.getElementById('data_inicio');
        var dataFim = document.getElementById('data_fim');
        
        if (dataInicio.value === "") { mostrarErro(dataInicio, "erro_data_inicio", "Obrigatório."); valid = false; }
        if (dataFim.value === "") { mostrarErro(dataFim, "erro_data_fim", "Obrigatório."); valid = false; }

        var dataHoje = new Date();
        dataHoje.setHours(0, 0, 0, 0); // Remove horas para comparar só o dia
        var dIniEv = new Date(dataInicio.value);
        var dFimEv = new Date(dataFim.value);

        if (dataInicio.value !== "") {
            // Se NÃO estivermos a editar (estamos a criar), não permite datas no passado
            if (!isEdit && dIniEv < dataHoje) {
                mostrarErro(dataInicio, "erro_data_inicio", "Não podes criar eventos para datas anteriores à data atual.");
                valid = false;
            }
        }

        if (dataInicio.value !== "" && dataFim.value !== "") {
            if (dFimEv <= dIniEv) {
                mostrarErro(dataFim, "erro_data_fim", "A data de fim tem de ser posterior à data de início.");
                valid = false;
            }
        }

        // --- VALIDAÇÃO DE DATAS DOS BILHETES ---
        var nomesBilhetes = document.getElementsByName('bilhete_nome[]');
        var precosBilhetes = document.getElementsByName('bilhete_preco[]');
        var qtdsBilhetes = document.getElementsByName('bilhete_qtd[]');
        var bilheteIni = document.getElementsByName('bilhete_d_ini[]');
        var bilheteFim = document.getElementsByName('bilhete_d_fim[]');
        var erroBilhetes = document.getElementById('erro_bilhetes');

        erroBilhetes.innerHTML = ""; // Limpa erros gerais de bilhetes

        for (var k = 0; k < nomesBilhetes.length; k++) {
            // Verifica campos vazios
            if (nomesBilhetes[k].value.trim() === "" || precosBilhetes[k].value === "" || qtdsBilhetes[k].value === "") {
                erroBilhetes.innerHTML += "Preencha todos os campos (Nome, Preço e Qtd) nos bilhetes.<br>";
                nomesBilhetes[k].style.borderColor = "red"; precosBilhetes[k].style.borderColor = "red"; qtdsBilhetes[k].style.borderColor = "red";
                valid = false;
            }

            // Verifica as datas dos bilhetes em relação ao evento
            if (bilheteIni[k].value !== "" && bilheteFim[k].value !== "" && dataInicio.value !== "" && dataFim.value !== "") {
                var bIni = new Date(bilheteIni[k].value);
                var bFim = new Date(bilheteFim[k].value);

                // Isola a Data do Evento (Ano-Mês-Dia) para garantir que compara as datas perfeitamente e ignora as horas
                var dIniEvData = new Date(dataInicio.value.substring(0, 10));
                var dFimEvData = new Date(dataFim.value.substring(0, 10));

                if (bFim < bIni) {
                    erroBilhetes.innerHTML += `Erro no bilhete '${nomesBilhetes[k].value}': A data de fim não pode ser anterior à de início.<br>`;
                    bilheteFim[k].style.borderColor = "red";
                    valid = false;
                }

                // O bilhete tem que ter as datas Válido Desde / Até PRESAS nos limites do evento
                if (bIni < dIniEvData || bFim > dFimEvData) {
                    erroBilhetes.innerHTML += `Erro no bilhete '${nomesBilhetes[k].value}': As datas do bilhete têm de estar entre o Início e o Fim do Evento.<br>`;
                    bilheteIni[k].style.borderColor = "red";
                    bilheteFim[k].style.borderColor = "red";
                    valid = false;
                }
            }
        }

        if (!valid) {
            e.preventDefault(); 
        }
    };

    function mostrarErro(elemento, idErro, mensagem) {
        elemento.style.borderColor = "red"; 
        document.getElementById(idErro).innerHTML = mensagem; 
    }
};