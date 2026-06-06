<?php
// scripts/processar_pagamento.php

// 1. Iniciar sessão para saber quem é o utilizador
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Verificar se o utilizador está logado e se o pedido veio por POST
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

// Validação Server-Side Básica (Nunca confiar apenas no JS!)
if (empty($nome_titular) || empty($num_cartao) || empty($data_validade) || empty($cvc)) {
    // Nota: Vamos assumir que vais renomear o pagamento.html para pagamento.php
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
        SELECT c.id AS id_carrinho, c.quantidade, t.id AS id_tipo_bilhete, t.preco, t.qtd_disponivel 
        FROM carrinho c
        JOIN tipos_bilhete t ON c.id_tipo_bilhete = t.id
        WHERE c.id_utilizador = :id_utilizador
    ");
    $stmt_carrinho->execute(['id_utilizador' => $id_utilizador]);
    $itens = $stmt_carrinho->fetchAll(PDO::FETCH_ASSOC);

    // Se o carrinho estiver vazio por algum motivo, abortamos
    if (count($itens) === 0) {
        $db->rollBack();
        header("Location: ../index.php?erro=carrinho_vazio");
        exit();
    }

    // 6. Calcular Totais e Verificar Stock
    $subtotal = 0;
    foreach ($itens as $item) {
        // Se a quantidade pedida for maior que o stock atual, aborta tudo!
        if ($item['quantidade'] > $item['qtd_disponivel']) {
            $db->rollBack();
            header("Location: ../index.php?carrinho=aberto&erro=stock_insuficiente");
            exit();
        }
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
    $id_compra = $db->lastInsertId(); // Precisamos deste ID para as próximas tabelas

    // 8. Preparar Queries para Itens e Atualização de Stock
    $stmt_item = $db->prepare("INSERT INTO itens_compra (id_compra, id_tipo_bilhete, quantidade, preco_unitario) VALUES (:id_compra, :id_tipo_bilhete, :quantidade, :preco)");
    $stmt_stock = $db->prepare("UPDATE tipos_bilhete SET qtd_disponivel = qtd_disponivel - :quantidade WHERE id = :id_tipo_bilhete");

    // Inserir cada bilhete e descontar o respetivo stock
    foreach ($itens as $item) {
        $stmt_item->execute([
            'id_compra' => $id_compra,
            'id_tipo_bilhete' => $item['id_tipo_bilhete'],
            'quantidade' => $item['quantidade'],
            'preco' => $item['preco']
        ]);

        $stmt_stock->execute([
            'quantidade' => $item['quantidade'],
            'id_tipo_bilhete' => $item['id_tipo_bilhete']
        ]);
    }

    // 9. Registar o Pagamento (Simulação Académica)
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

    // 10. Esvaziar o Carrinho do utilizador após sucesso
    $stmt_limpar = $db->prepare("DELETE FROM carrinho WHERE id_utilizador = :id_utilizador");
    $stmt_limpar->execute(['id_utilizador' => $id_utilizador]);

    // 11. Sucesso! Commit grava tudo permanentemente.
    $db->commit(); // FIM DA ZONA SEGURA

    // Redirecionar para a fatura/recibo final
    header("Location: ../sucesso.php?id=" . $id_compra);
    exit();

} catch (Exception $e) {
    // Se der qualquer erro algures no processo, desfaz TODAS as alterações (Rollback)
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    // header("Location: ../pagamento.php?erro=erro_interno"); // Em produção
    die("Erro Crítico no Sistema: " . $e->getMessage()); // Para debug
}
?>