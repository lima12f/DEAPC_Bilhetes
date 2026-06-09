<?php
// scripts/carregar_eventos_homepage.php

$lista_eventos = [];
$lista_categorias = [];

$termo_pesquisa = isset($_GET['q']) ? trim($_GET['q']) : '';
$id_categoria_filtro = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;

// Obter a data de hoje no formato YYYY-MM-DD para comparar com a BD
$hoje = date('Y-m-d'); 

try {
    $db = new SQLite3(__DIR__ . '/../ticketzone.db');
    $db->exec("PRAGMA foreign_keys = ON;");

    // 1. Carregar Categorias
    $res_categorias = $db->query("SELECT id, nome FROM categorias ORDER BY nome ASC");
    while ($cat = $res_categorias->fetchArray(SQLITE3_ASSOC)) {
        $lista_categorias[] = $cat;
    }

    // 2. Query Base (Agora com validação de data e de stock)
    $query = "
        SELECT 
            e.id, 
            e.nome, 
            e.data_inicio, 
            e.data_fim, 
            e.local, 
            e.imagem,
            c.nome AS categoria_nome,
            (SELECT MIN(preco) FROM tipos_bilhete WHERE id_evento = e.id AND qtd_disponivel > 0) AS preco_minimo
        FROM eventos e
        LEFT JOIN categorias c ON e.id_categoria = c.id
        WHERE e.estado = 'ativo' 
          -- VALIDAÇÃO DE DATA: Ignora se a data de fim (ou de início) for menor que hoje
          AND (e.data_fim >= :hoje OR e.data_inicio >= :hoje)
          -- VALIDAÇÃO DE STOCK: O evento tem de ter pelo menos 1 bilhete disponível
          AND EXISTS (
              SELECT 1 FROM tipos_bilhete 
              WHERE id_evento = e.id AND qtd_disponivel > 0
          )
    ";

    if ($termo_pesquisa !== '') {
        $query .= " AND e.nome LIKE :pesquisa";
    }
    if ($id_categoria_filtro > 0) {
        $query .= " AND e.id_categoria = :categoria";
    }

    $query .= " ORDER BY e.data_inicio ASC";

    $stmt = $db->prepare($query);

    // Bind da data atual
    $stmt->bindValue(':hoje', $hoje, SQLITE3_TEXT);

    // Bind dos filtros
    if ($termo_pesquisa !== '') {
        $stmt->bindValue(':pesquisa', '%' . $termo_pesquisa . '%', SQLITE3_TEXT);
    }
    if ($id_categoria_filtro > 0) {
        $stmt->bindValue(':categoria', $id_categoria_filtro, SQLITE3_INTEGER);
    }

    $resultado = $stmt->execute();

    while ($linha = $resultado->fetchArray(SQLITE3_ASSOC)) {
        $lista_eventos[] = $linha;
    }

    $db->close();

} catch (Exception $e) {
    // Para debug: echo "Erro: " . $e->getMessage();
}
?>