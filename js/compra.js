// js/compra.js
document.addEventListener('DOMContentLoaded', () => {
    const seletorBilhete = document.getElementById('seletor-bilhete');
    const displayPrecoUnitario = document.getElementById('display-preco-unitario');
    const btnMais = document.getElementById('btn-mais');
    const btnMenos = document.getElementById('btn-menos');
    const spanQuantidade = document.getElementById('quantidade');
    
    const spanSubtotal = document.getElementById('subtotal');
    const spanTaxas = document.getElementById('taxas');
    const spanTotal = document.getElementById('total');
    
    const inputQtdEscondido = document.getElementById('input-quantidade-escondido');
    const inputTipoEscondido = document.getElementById('input-tipo-escondido');
    const inputEventoRetorno = document.getElementById('input-evento-retorno');

    if (!seletorBilhete) return;

    function atualizarCalculos() {
        let opcaoSelecionada = seletorBilhete.options[seletorBilhete.selectedIndex];
        let preco = parseFloat(opcaoSelecionada.getAttribute('data-preco'));
        let maxDisponivel = parseInt(opcaoSelecionada.getAttribute('data-max'));
        let idBilhete = opcaoSelecionada.value;

        let qtdAtual = parseInt(spanQuantidade.innerText);

        // O limite máximo é 10 ou o que sobrar de stock (o que for menor)
        let maxPermitido = Math.min(10, maxDisponivel);

        // Se o utilizador mudou de bilhete e a quantidade atual for maior que o stock do novo bilhete, reduz
        if (qtdAtual > maxPermitido) {
            qtdAtual = maxPermitido;
            spanQuantidade.innerText = qtdAtual;
        }

        // Atualizar inputs hidden para o form POST
        inputTipoEscondido.value = idBilhete;
        inputQtdEscondido.value = qtdAtual;

        // Atualizar display unitário
        displayPrecoUnitario.innerText = preco.toFixed(2) + '€ uni.';

        // Lógica de cálculo
        let subtotal = preco * qtdAtual;
        let taxas = subtotal * 0.10;
        let total = subtotal + taxas;

        // Atualizar a interface
        spanSubtotal.innerText = subtotal.toFixed(2) + '€';
        spanTaxas.innerText = taxas.toFixed(2) + '€';
        spanTotal.innerText = total.toFixed(2) + '€';
    }

    // Botão Mais (Respeitando o Limite)
    btnMais.addEventListener('click', () => {
        let qtd = parseInt(spanQuantidade.innerText);
        let opcaoSelecionada = seletorBilhete.options[seletorBilhete.selectedIndex];
        let maxPermitido = Math.min(10, parseInt(opcaoSelecionada.getAttribute('data-max')));

        if (qtd < maxPermitido) {
            spanQuantidade.innerText = qtd + 1;
            atualizarCalculos();
        } else {
            // Efeito visual de recusa (Pistca vermelho ligeiramente)
            spanQuantidade.style.color = '#d9534f';
            setTimeout(() => { spanQuantidade.style.color = ''; }, 300);
        }
    });

    // Botão Menos
    btnMenos.addEventListener('click', () => {
        let qtd = parseInt(spanQuantidade.innerText);
        if (qtd > 1) {
            spanQuantidade.innerText = qtd - 1;
            atualizarCalculos();
        }
    });

    seletorBilhete.addEventListener('change', atualizarCalculos);
    atualizarCalculos();
});