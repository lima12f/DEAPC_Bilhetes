<?php
// scripts/carregar_eventos_homepage.php

$lista_eventos = [];
$lista_categorias = [];

// Obter os parâmetros da URL
$termo_pesquisa = isset($_GET['q']) ? trim($_GET['q']) : '';
$id_categoria_filtro = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;

try {
    // Atenção: A usar SQLite3 de acordo com o teu código original neste ficheiro
    $db = new SQLite3(__DIR__ . '/../ticketzone.db');
    $db->exec("PRAGMA foreign_keys = ON;");

    // 1. Carregar todas as categorias para construir os botões de filtro na UI
    $res_categorias = $db->query("SELECT id, nome FROM categorias ORDER BY nome ASC");
    while ($cat = $res_categorias->fetchArray(SQLITE3_ASSOC)) {
        $lista_categorias[] = $cat;
    }

    // 2. Query Base dos Eventos
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
        WHERE e.estado = 'ativo' -- Boa prática: mostrar apenas eventos ativos
    ";

    // Adiciona o filtro de pesquisa por texto, se existir
    if ($termo_pesquisa !== '') {
        $query .= " AND e.nome LIKE :pesquisa";
    }

    // Adiciona o filtro por categoria, se existir
    if ($id_categoria_filtro > 0) {
        $query .= " AND e.id_categoria = :categoria";
    }

    $query .= " ORDER BY e.data_inicio ASC";

    $stmt = $db->prepare($query);

    // Fazer bind dos valores de forma segura
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