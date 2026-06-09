<?php
// scripts/detalhes_evento.php
require_once 'conexao.php'; // Assume que tens um ficheiro que cria a $pdo para SQLite

// Verifica se foi passado um ID na URL
$id_evento = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_evento === 0) {
    header("Location: ../index.php");
    exit;
}

$hoje = date('Y-m-d'); // Vai buscar a data atual

// 1. Obter detalhes do Evento
$stmt = $pdo->prepare("SELECT * FROM eventos WHERE id = :id_evento AND estado = 'ativo'");
$stmt->execute(['id_evento' => $id_evento]);
$evento = $stmt->fetch(PDO::FETCH_ASSOC);

// Redireciona se o evento não existir ou já tiver passado (assumindo que tens 'data_fim' nos eventos)
if (!$evento || $evento['data_fim'] < $hoje) {
    header("Location: ../index.php?erro=evento_invalido_ou_expirado");
    exit;
}

// 2. Obter Tipos de Bilhete disponíveis e válidos para a data atual
$stmt_bilhetes = $pdo->prepare("
    SELECT * FROM tipos_bilhete 
    WHERE id_evento = :id_evento 
      AND qtd_disponivel > 0
      AND data_valido_fim >= :hoje
");
$stmt_bilhetes->execute([
    'id_evento' => $id_evento,
    'hoje' => $hoje
]);
$tipos_bilhete = $stmt_bilhetes->fetchAll(PDO::FETCH_ASSOC);

// Se não houver bilhetes, podemos definir uma flag para bloquear a compra
$bilhetes_esgotados = count($tipos_bilhete) === 0;
?>