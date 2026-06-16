<?php
// Inicia a ligação à base de dados exclusivamente para atualizar estados
$db_estados = new SQLite3(__DIR__ . '/../ticketzone.db');

// Atualiza o estado para 'expirado' nos eventos que estavam ativos mas cuja data de fim já passou.
// A função strftime() com 'localtime' garante que a comparação usa a hora correta do teu fuso horário.
$query_atualizar = "
    UPDATE eventos 
    SET estado = 'expirado' 
    WHERE estado = 'ativo' 
    AND data_fim < strftime('%Y-%m-%dT%H:%M', 'now', 'localtime')
";

$db_estados->exec($query_atualizar);
$db_estados->close();
?>