<?php

$db = new SQLite3(__DIR__ . '/../ticketzone.db');

$query = "
    SELECT
        username,
        email,
        tipo,
        ultimo_acesso
    FROM utilizadores
    WHERE ultimo_acesso IS NOT NULL
    ORDER BY ultimo_acesso DESC
    LIMIT 50
";

$resultado = $db->query($query);

$entradas = [];

while ($linha = $resultado->fetchArray(SQLITE3_ASSOC)) {
    $entradas[] = $linha;
}

$db->close();

?>