<?php
// pagamento.php
session_start();

// Redireciona se não estiver logado
if (!isset($_SESSION['id_utilizador'])) {
    header("Location: login.php");
    exit();
}

// Conectar à Base de Dados
try {
    $db = new PDO('sqlite:ticketzone.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

$id_utilizador = $_SESSION['id_utilizador'];

// Obter os itens do carrinho deste utilizador
$stmt = $db->prepare("
    SELECT c.quantidade, t.nome AS tipo_bilhete, t.preco, e.nome AS evento_nome
    FROM carrinho c
    JOIN tipos_bilhete t ON c.id_tipo_bilhete = t.id
    JOIN eventos e ON t.id_evento = e.id
    WHERE c.id_utilizador = :id_utilizador
");
$stmt->execute(['id_utilizador' => $id_utilizador]);
$itens_carrinho = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Se não houver itens, manda o utilizador de volta
if (count($itens_carrinho) === 0) {
    header("Location: index.php");
    exit();
}

// Calcular os totais reais
$subtotal = 0;
foreach ($itens_carrinho as $item) {
    $subtotal += ($item['preco'] * $item['quantidade']);
}
$taxas = $subtotal * 0.10;
$total_final = $subtotal + $taxas;
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TicketZone - Pagamento</title>
    <link rel="stylesheet" href="styles/pagamento.css">

</head>
<body class="fundo-pagamento">

    <nav class="nav-pagamento">
        <a href="index.php" class="btn-voltar">< Voltar</a>
        <div class="janela-logo">
            <img src="images/logo.png" alt="TicketZone Logo" class="pagamento-img">
        </div>
        <div style="width: 80px;"></div> 
    </nav>

    <main class="container-pagamento">
        <div class="layout-pagamento">
            
            <div class="resumo-encomenda">
                <h2>Resumo da Compra</h2>
                <p class="subtitulo-pagamento">Confirme os bilhetes antes de pagar.</p>
                <hr class="divisor">
                
                <div class="lista-itens-resumo">
                    <?php foreach ($itens_carrinho as $item): ?>
                        <div class="item-resumo">
                            <div class="item-info">
                                <span class="item-nome"><?= htmlspecialchars($item['tipo_bilhete']) ?> - <?= htmlspecialchars($item['evento_nome']) ?></span>
                                <span class="item-qtd">Qtd: <?= $item['quantidade'] ?></span>
                            </div>
                            <span class="item-preco"><?= number_format($item['preco'] * $item['quantidade'], 2) ?>€</span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="caixa-resumo" style="margin-top: 20px; margin-bottom: 0;">
                    <div class="linha-resumo">
                        <span>Subtotal</span>
                        <span><?= number_format($subtotal, 2) ?>€</span>
                    </div>
                    <div class="linha-resumo">
                        <span>Taxas (10%)</span>
                        <span><?= number_format($taxas, 2) ?>€</span>
                    </div>
                    <div class="linha-resumo total-final">
                        <span>Total a Pagar</span>
                        <span style="color: #1a1a5e; font-size: 18px;"><?= number_format($total_final, 2) ?>€</span>
                    </div>
                </div>
            </div>

            <div class="cartao-pagamento">
                <h2>Detalhes de Pagamento</h2>
                <p class="subtitulo-pagamento">Insira os dados do seu cartão seguro.</p>

                <form id="form-pagamento" action="scripts/processar_pagamento.php" method="POST" novalidate>
                    <div class="grupo-input">
                        <label for="nome_titular">Nome no Cartão *</label>
                        <input type="text" id="nome_titular" name="nome_titular" placeholder="Ex: João Silva">
                        <span id="erro-nome" class="msg-erro"></span>
                    </div>

                    <div class="grupo-input">
                        <label for="num_cartao">Número do Cartão *</label>
                        <input type="text" id="num_cartao" name="num_cartao" placeholder="0000000000000000" maxlength="16">
                        <span id="erro-cartao" class="msg-erro"></span>
                    </div>

                    <div class="linha-inputs-metade">
                        <div class="grupo-input metade">
                            <label for="data_validade">Validade *</label>
                            <input type="text" id="data_validade" name="data_validade" placeholder="MM/AA" maxlength="5">
                            <span id="erro-validade" class="msg-erro"></span>
                        </div>
                        <div class="grupo-input metade">
                            <label for="cvc">CVC *</label>
                            <input type="password" id="cvc" name="cvc" placeholder="123" maxlength="3">
                            <span id="erro-cvc" class="msg-erro"></span>
                        </div>
                    </div>

                    <div class="grupo-input">
                        <label for="nif">NIF (Opcional)</label>
                        <input type="text" id="nif" name="nif" placeholder="123456789" maxlength="9">
                        <span id="erro-nif" class="msg-erro"></span>
                    </div>

                    <button type="submit" class="btn-confirmar-pagamento">Pagar <?= number_format($total_final, 2) ?>€</button>
                </form>
            </div>

        </div>
    </main>

    <?php if (isset($_GET['erro'])): ?>
        <script>
            <?php if ($_GET['erro'] === 'dados_invalidos'): ?>
                alert('Erro: Os dados de pagamento fornecidos são inválidos. Por favor, preencha tudo corretamente.');
            <?php elseif ($_GET['erro'] === 'erro_interno'): ?>
                alert('Ocorreu um erro na base de dados ao processar o pagamento. Tente novamente mais tarde.');
            <?php else: ?>
                alert('Ocorreu um erro desconhecido. Tente novamente.');
            <?php endif; ?>
            
            // Limpa o URL para o alerta não voltar a aparecer se a pessoa atualizar a página (F5)
            window.history.replaceState(null, '', window.location.pathname);
        </script>
    <?php endif; ?>

    <script src="js/pagamento.js"></script>
</body>
</html>