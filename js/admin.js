document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    // Alertas de Sucesso
    if (urlParams.has('sucesso')) {
        const sucesso = urlParams.get('sucesso');
        if (sucesso === '1') {
            alert("Evento guardado com sucesso!");
        } else if (sucesso === 'cancelado') {
            alert("Evento cancelado com sucesso!");
        }
        limparUrl();
    }
    
    // Alertas de Erro
    if (urlParams.has('erro')) {
        const erro = urlParams.get('erro');
        if (erro === 'id_invalido') {
            alert("Erro: ID do evento inválido ou não encontrado.");
        } else if (erro === 'db') {
            alert("Erro: Falha ao comunicar com a base de dados.");
        } else {
            alert("Ocorreu um erro desconhecido.");
        }
        limparUrl();
    }

    // Função para limpar os parâmetros do URL sem recarregar a página
    function limparUrl() {
        const novaUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.replaceState({path: novaUrl}, '', novaUrl);
    }
});