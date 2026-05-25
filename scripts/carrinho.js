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
});