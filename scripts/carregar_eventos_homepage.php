<?php
// scripts/carregar_eventos_homepage.php

$lista_eventos = [];
// Verifica se há uma pesquisa na URL (parâmetro 'q')
$termo_pesquisa = isset($_GET['q']) ? trim($_GET['q']) : '';

try {
    $db = new SQLite3(__DIR__ . '/../ticketzone.db');
    $db->exec("PRAGMA foreign_keys = ON;");

    // Query Base: Junta Eventos, Categorias e calcula o preço mínimo dos bilhetes
    $query = "
        SELECT 
            e.id, 
            e.nome, 
            e.data_inicio, 
            e.data_fim, 
            e.local, 
            e.imagem,
            c.nome AS categoria_nome,
            (SELECT MIN(preco) FROM tipos_bilhete WHERE id_evento = e.id) AS preco_minimo
        FROM eventos e
        LEFT JOIN categorias c ON e.id_categoria = c.id
        WHERE 1=1
    ";

    // Se o utilizador pesquisou alguma coisa, adicionamos o filtro à Query
    if ($termo_pesquisa !== '') {
        $query .= " AND e.nome LIKE :pesquisa";
    }

    $query .= " ORDER BY e.data_inicio ASC"; // Mostra os eventos mais próximos primeiro

    $stmt = $db->prepare($query);

    // Fazer bind do termo de pesquisa (proteção contra SQL Injection)
    if ($termo_pesquisa !== '') {
        $stmt->bindValue(':pesquisa', '%' . $termo_pesquisa . '%', SQLITE3_TEXT);
    }

    $resultado = $stmt->execute();

    while ($linha = $resultado->fetchArray(SQLITE3_ASSOC)) {
        $lista_eventos[] = $linha;
    }

    $db->close();

} catch (Exception $e) {
    // Para debug em desenvolvimento: echo "Erro: " . $e->getMessage();
}
?>