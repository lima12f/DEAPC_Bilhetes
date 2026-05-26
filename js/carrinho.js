document.addEventListener('DOMContentLoaded', () => {
    const btnAbrir = document.getElementById('btn-abrir-carrinho');
    const btnFechar = document.getElementById('fechar-carrinho');
    const modal = document.getElementById('modal-carrinho');
    const overlay = document.getElementById('overlay-carrinho');

    // Abre o carrinho
    if (btnAbrir) {
        btnAbrir.addEventListener('click', (e) => {
            e.preventDefault();
            modal.classList.add('ativo');
            overlay.classList.add('ativo');
        });
    }

    // Função para fechar
    function fecharCarrinho() {
        modal.classList.remove('ativo');
        overlay.classList.remove('ativo');
    }

    // Fecha ao clicar no X ou fora do modal (no fundo escuro)
    if (btnFechar) btnFechar.addEventListener('click', fecharCarrinho);
    if (overlay) overlay.addEventListener('click', fecharCarrinho);
    // Verifica se o URL tem o parâmetro "?carrinho=aberto"
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('carrinho') === 'aberto') {
        // Tira o parâmetro do URL para ficar limpo (sem recarregar a página)
        window.history.replaceState(null, '', window.location.pathname);
        
        // Abre o modal
        if (modal && overlay) {
            modal.classList.add('ativo');
            overlay.classList.add('ativo');
        }
    }
});