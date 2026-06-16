<?php
$db = new SQLite3(__DIR__ . '/../ticketzone.db');

// ATUALIZAÇÃO AUTOMÁTICA DE ESTADOS INFALÍVEL
// A função replace substitui o 'T' (ex: 2026-06-15T15:30) por um espaço (' ') para igualar ao formato de horas do SQLite.
$query_atualizar = "
    UPDATE eventos 
    SET estado = 'expirado' 
    WHERE estado = 'ativo' 
    AND replace(data_fim, 'T', ' ') < datetime('now', 'localtime')
";
$db->exec($query_atualizar);

// 1. Carregar as categorias para preencher o dropdown do filtro na página
$categorias = [];
$cat_query = $db->query("SELECT id, nome FROM categorias ORDER BY nome ASC");
while ($c = $cat_query->fetchArray(SQLITE3_ASSOC)) {
    $categorias[] = $c;
}

// 2. Receber os valores escolhidos no filtro (ou usar os valores por defeito)
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : 'todos';
$filtro_categoria = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;

// 3. Montar as condições da query de forma dinâmica
$where_clauses = [];
if ($filtro_estado !== 'todos') {
    $where_clauses[] = "e.estado = :estado";
}
if ($filtro_categoria > 0) {
    $where_clauses[] = "e.id_categoria = :categoria";
}

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(" AND ", $where_clauses) : "";

// 4. A query com a matemática dos bilhetes, adaptada para incluir os filtros
$query = "
    SELECT
        e.id,
        e.nome,
        e.estado,
        e.id_categoria,
        (SELECT COALESCE(SUM(qtd_total), 0) FROM tipos_bilhete WHERE id_evento = e.id) AS lotacao_total,
        (SELECT COALESCE(SUM(qtd_disponivel), 0) FROM tipos_bilhete WHERE id_evento = e.id) AS bilhetes_restantes,
        (SELECT COALESCE(SUM(ic.quantidade), 0) FROM itens_compra ic JOIN tipos_bilhete tb ON tb.id = ic.id_tipo_bilhete JOIN compras c ON c.id = ic.id_compra WHERE tb.id_evento = e.id AND c.estado IN ('concluida', 'pago')) AS bilhetes_vendidos,
        (SELECT COALESCE(SUM(ic.quantidade * ic.preco_unitario), 0) FROM itens_compra ic JOIN tipos_bilhete tb ON tb.id = ic.id_tipo_bilhete JOIN compras c ON c.id = ic.id_compra WHERE tb.id_evento = e.id AND c.estado IN ('concluida', 'pago')) AS total_vendas,
        (SELECT COALESCE(SUM(p.taxa_plataforma), 0) FROM pagamentos p JOIN compras c ON c.id = p.id_compra WHERE c.estado IN ('concluida', 'pago') AND c.id IN (SELECT ic.id_compra FROM itens_compra ic JOIN tipos_bilhete tb ON tb.id = ic.id_tipo_bilhete WHERE tb.id_evento = e.id)) AS lucro_plataforma
    FROM eventos e
    $where_sql
    ORDER BY e.id DESC
";

$stmt = $db->prepare($query);

// Associar os valores do filtro com segurança
if ($filtro_estado !== 'todos') {
    $stmt->bindValue(':estado', $filtro_estado, SQLITE3_TEXT);
}
if ($filtro_categoria > 0) {
    $stmt->bindValue(':categoria', $filtro_categoria, SQLITE3_INTEGER);
}

$resultado = $stmt->execute();
$eventos = [];

while ($linha = $resultado->fetchArray(SQLITE3_ASSOC)) {
    $eventos[] = $linha;
}

$db->close();
?>