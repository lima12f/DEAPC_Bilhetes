<?php
function limparReservasExpiradas() {
    try {
        // Ligação isolada para não dar conflitos com o PDO ou SQLite3 de outros ficheiros
        $db_clean = new SQLite3(__DIR__ . '/../ticketzone.db');
        $db_clean->busyTimeout(5000);
        
        // 1. Encontrar todos os itens adicionados há mais de 15 minutos
        // O SQLite usa UTC no CURRENT_TIMESTAMP, logo usamos modificadores nativos do SQLite
        $query = "SELECT id, id_tipo_bilhete, quantidade FROM carrinho WHERE data_adicao <= datetime('now', '-15 minutes')";
        $resultados = $db_clean->query($query);
        
        // 2. Repor o stock para cada item expirado
        while ($row = $resultados->fetchArray(SQLITE3_ASSOC)) {
            $stmt_repo = $db_clean->prepare("UPDATE tipos_bilhete SET qtd_disponivel = qtd_disponivel + :qtd WHERE id = :id_tipo");
            $stmt_repo->bindValue(':qtd', $row['quantidade'], SQLITE3_INTEGER);
            $stmt_repo->bindValue(':id_tipo', $row['id_tipo_bilhete'], SQLITE3_INTEGER);
            $stmt_repo->execute();
        }
        
        // 3. Apagar os itens expirados do carrinho
        $db_clean->exec("DELETE FROM carrinho WHERE data_adicao <= datetime('now', '-15 minutes')");
        $db_clean->close();

    } catch (Exception $e) {
        // Apenas guardamos no log do servidor para não quebrar a experiência do utilizador
        error_log("Erro ao limpar reservas expiradas: " . $e->getMessage());
    }
}

// Executa a função imediatamente sempre que o ficheiro for incluído
limparReservasExpiradas();
?>