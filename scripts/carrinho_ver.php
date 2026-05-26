<?php
// Este ficheiro carrega os itens do carrinho para o utilizador atual

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inicializamos as variáveis que a View (carrinho_modal.php) vai precisar
$itens_carrinho = [];
$subtotal = 0;
$taxas = 0;
$total = 0;

// Só tentamos carregar dados se o utilizador tiver a sessão iniciada
if (isset($_SESSION['id_utilizador'])) {
    $id_utilizador = $_SESSION['id_utilizador'];

    try {
        // Ligar à base de dados
        $db = new SQLite3(__DIR__ . '/../ticketzone.db');
        $db->exec("PRAGMA foreign_keys = ON;");

        $query = "SELECT 
                    c.id AS id_carrinho,
                    c.quantidade,
                    tb.nome AS tipo_bilhete,
                    tb.preco,
                    e.nome AS evento_nome,
                    e.imagem AS evento_imagem
                  FROM carrinho c
                  JOIN tipos_bilhete tb ON c.id_tipo_bilhete = tb.id
                  JOIN eventos e ON tb.id_evento = e.id
                  WHERE c.id_utilizador = :id_utilizador
                  ORDER BY c.data_adicao DESC";

        $stmt = $db->prepare($query);
        $stmt->bindValue(':id_utilizador', $id_utilizador, SQLITE3_INTEGER);
        $resultado = $stmt->execute();

        // Mapeia os resultados e calcula os valores monetários
        while ($linha = $resultado->fetchArray(SQLITE3_ASSOC)) {
            $itens_carrinho[] = $linha;

            // Soma o preço acumulado (Preço Unitário * Quantidade)
            $subtotal += $linha['preco'] * $linha['quantidade'];
        }

        // Cálculos finais baseados no teu layout estático
        $taxas = $subtotal * 0.10; // Taxa de 10%
        $total = $subtotal + $taxas;

        $db->close();

    } catch (Exception $e) {
        error_log("Erro ao carregar o carrinho: " . $e->getMessage());
    }
}
?>