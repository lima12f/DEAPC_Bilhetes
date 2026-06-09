<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_utilizador = $_SESSION['id_utilizador'] ?? null;
$bilhetes = [];

if ($id_utilizador) {
    $db = new SQLite3(__DIR__ . '/../ticketzone.db');

    $stmt = $db->prepare("
        SELECT
            e.nome           AS nome_evento,
            e.data_inicio    AS data_evento,
            e.local          AS local_evento,
            e.imagem         AS imagem,
            tb.nome          AS tipo_bilhete,
            tb.preco         AS preco_unitario,
            ic.quantidade    AS quantidade,
            c.data_compra    AS data_compra,
            c.total          AS total_compra,
            p.id             AS id_pagamento
        FROM compras c
        JOIN itens_compra ic   ON ic.id_compra      = c.id
        JOIN tipos_bilhete tb  ON tb.id             = ic.id_tipo_bilhete
        JOIN eventos e         ON e.id              = tb.id_evento
        LEFT JOIN pagamentos p ON p.id_compra       = c.id
        WHERE c.id_utilizador = :id
        AND substr(e.data_inicio, 1, 10) >= date('now')
        ORDER BY c.data_compra DESC
    ");

    $stmt->bindValue(':id', $id_utilizador, SQLITE3_INTEGER);
    $resultado = $stmt->execute();

    while ($linha = $resultado->fetchArray(SQLITE3_ASSOC)) {
        $bilhetes[] = $linha;
    }

    $db->close();
}
?>