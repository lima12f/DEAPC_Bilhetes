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

    // Se não existir seletor (ex: esgotado), sai do script
    if (!seletorBilhete) return;

    function atualizarCalculos() {
        // Obter quantidade e tipo selecionado
        let qtd = parseInt(spanQuantidade.innerText);
        let opcaoSelecionada = seletorBilhete.options[seletorBilhete.selectedIndex];
        
        let preco = parseFloat(opcaoSelecionada.getAttribute('data-preco'));
        let idBilhete = opcaoSelecionada.value;

        // Atualizar inputs hidden para o form POST
        inputTipoEscondido.value = idBilhete;
        inputQtdEscondido.value = qtd;

        // Atualizar display unitário
        displayPrecoUnitario.innerText = preco.toFixed(2) + '€ uni.';

        // Lógica de cálculo
        let subtotal = preco * qtd;
        let taxas = subtotal * 0.10;
        let total = subtotal + taxas;

        // Atualizar a interface
        spanSubtotal.innerText = subtotal.toFixed(2) + '€';
        spanTaxas.innerText = taxas.toFixed(2) + '€';
        spanTotal.innerText = total.toFixed(2) + '€';
    }

    // limite min 1, max 10
    btnMais.addEventListener('click', () => {
        let qtd = parseInt(spanQuantidade.innerText);
        if (qtd < 10) {
            spanQuantidade.innerText = qtd + 1;
            atualizarCalculos();
        }
    });

    btnMenos.addEventListener('click', () => {
        let qtd = parseInt(spanQuantidade.innerText);
        if (qtd > 1) {
            spanQuantidade.innerText = qtd - 1;
            atualizarCalculos();
        }
    });

    // Listener para quando o tipo de bilhete muda
    seletorBilhete.addEventListener('change', atualizarCalculos);

    // Inicializar os valores na primeira vez que a página carrega
    atualizarCalculos();
});