<?php
// scripts/processar_pagamento.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Limpar reservas expiradas antes de processar qualquer pagamento!
// Se os 15 min do utilizador já passaram, o carrinho dele é apagado aqui e a compra falha com segurança.
include_once __DIR__ . '/limpar_reservas.php';

if (!isset($_SESSION['id_utilizador'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit();
}

$id_utilizador = $_SESSION['id_utilizador'];

// 3. Capturar os dados do formulário
$nome_titular = trim($_POST['nome_titular'] ?? '');
$num_cartao = trim($_POST['num_cartao'] ?? '');
$data_validade = trim($_POST['data_validade'] ?? '');
$cvc = trim($_POST['cvc'] ?? '');
$nif = trim($_POST['nif'] ?? '');

// Validação Server-Side Básica
if (empty($nome_titular) || empty($num_cartao) || empty($data_validade) || empty($cvc)) {
    header("Location: ../pagamento.php?erro=dados_invalidos");
    exit();
}

try {
    // 4. Ligar à BD e Iniciar a Transação
    $db = new PDO('sqlite:../ticketzone.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $db->beginTransaction(); // INÍCIO DA ZONA SEGURA

    // 5. Ir buscar os itens que estão no carrinho do utilizador
    $stmt_carrinho = $db->prepare("
        SELECT c.id AS id_carrinho, c.quantidade, t.id AS id_tipo_bilhete, t.preco
        FROM carrinho c
        JOIN tipos_bilhete t ON c.id_tipo_bilhete = t.id
        WHERE c.id_utilizador = :id_utilizador
    ");
    $stmt_carrinho->execute(['id_utilizador' => $id_utilizador]);
    $itens = $stmt_carrinho->fetchAll(PDO::FETCH_ASSOC);

    // Se o carrinho estiver vazio (ex: tempo expirou), abortamos
    if (count($itens) === 0) {
        $db->rollBack();
        // Redireciona com erro a dizer que a reserva expirou
        header("Location: ../index.php?carrinho=aberto&erro=reserva_expirada");
        exit();
    }

    // 6. Calcular Totais (A verificação de stock foi removida porque o stock já está reservado/descontado!)
    $subtotal = 0;
    foreach ($itens as $item) {
        $subtotal += ($item['preco'] * $item['quantidade']);
    }

    $taxa_plataforma = $subtotal * 0.10;
    $total_pagar = $subtotal + $taxa_plataforma;

    // 7. Criar a Compra principal
    $stmt_compra = $db->prepare("INSERT INTO compras (id_utilizador, total, estado) VALUES (:id_utilizador, :total, 'pago')");
    $stmt_compra->execute([
        'id_utilizador' => $id_utilizador,
        'total' => $total_pagar
    ]);
    $id_compra = $db->lastInsertId();

    // 8. Inserir Itens na Compra (O UPDATE de stock foi removido daqui!)
    $stmt_item = $db->prepare("INSERT INTO itens_compra (id_compra, id_tipo_bilhete, quantidade, preco_unitario) VALUES (:id_compra, :id_tipo_bilhete, :quantidade, :preco)");

    foreach ($itens as $item) {
        $stmt_item->execute([
            'id_compra' => $id_compra,
            'id_tipo_bilhete' => $item['id_tipo_bilhete'],
            'quantidade' => $item['quantidade'],
            'preco' => $item['preco']
        ]);
    }

    // 9. Registar o Pagamento
    $stmt_pagamento = $db->prepare("
        INSERT INTO pagamentos (id_compra, valor_total, taxa_plataforma, valor_evento, nif, nome_titular, num_cartao, data_validade, cvc)
        VALUES (:id_compra, :valor_total, :taxa, :valor_evento, :nif, :nome, :cartao, :validade, :cvc)
    ");
    $stmt_pagamento->execute([
        'id_compra' => $id_compra,
        'valor_total' => $total_pagar,
        'taxa' => $taxa_plataforma,
        'valor_evento' => $subtotal,
        'nif' => $nif,
        'nome' => $nome_titular,
        'cartao' => $num_cartao,
        'validade' => $data_validade,
        'cvc' => $cvc
    ]);

    // 10. Esvaziar o Carrinho do utilizador após sucesso (a reserva consolida-se em compra final)
    $stmt_limpar = $db->prepare("DELETE FROM carrinho WHERE id_utilizador = :id_utilizador");
    $stmt_limpar->execute(['id_utilizador' => $id_utilizador]);

    // 11. Sucesso!
    $db->commit();

    header("Location: ../index.php?pagamento=sucesso");
    exit();

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    die("Erro Crítico no Sistema: " . $e->getMessage());
}
?>