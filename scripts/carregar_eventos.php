<?php

$db = new SQLite3(__DIR__ . '/../ticketzone.db');

$query = "
    SELECT
        e.id,
        e.nome,
        e.estado,
        COALESCE(SUM(ic.quantidade), 0)                    AS total_bilhetes,
        COALESCE(SUM(ic.quantidade * ic.preco_unitario), 0) AS total_ganhos
    FROM eventos e
    LEFT JOIN tipos_bilhete tb ON tb.id_evento = e.id
    LEFT JOIN itens_compra ic  ON ic.id_tipo_bilhete = tb.id
    LEFT JOIN compras c        ON c.id = ic.id_compra AND c.estado = 'concluida'
    GROUP BY e.id, e.nome, e.estado
    ORDER BY e.id DESC
";

$resultado = $db->query($query);
$eventos = [];

while ($linha = $resultado->fetchArray(SQLITE3_ASSOC)) {
    $eventos[] = $linha;
}

$db->close();

?>