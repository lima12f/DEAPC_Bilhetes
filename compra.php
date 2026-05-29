<?php
// 1. Iniciar sessão imediatamente no topo (antes de qualquer HTML)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Conexão à base de dados SQLite (na raiz do projeto)
try {
    $pdo = new PDO('sqlite:ticketzone.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão à base de dados: " . $e->getMessage());
}

// 3. Validar o ID do evento recebido pela URL
$id_evento = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Se não houver ID (ex: acederam diretamente a compra.php), redireciona para a página principal
if ($id_evento === 0) {
    header("Location: index.php");
    exit();
}

// Data de hoje para validações
$hoje = date('Y-m-d');

// 4. Buscar os detalhes do evento à BD
$stmt = $pdo->prepare("SELECT * FROM eventos WHERE id = :id_evento AND estado = 'ativo'");
$stmt->execute(['id_evento' => $id_evento]);
$evento = $stmt->fetch(PDO::FETCH_ASSOC);

// Se o evento não existir ou já tiver passado a data de fim, redireciona de volta com erro
if (!$evento || $evento['data_fim'] < $hoje) {
    header("Location: index.php?erro=evento_indisponivel");
    exit();
}

// 5. Buscar os tipos de bilhete disponíveis e válidos
$stmt_bilhetes = $pdo->prepare("
    SELECT * FROM tipos_bilhete 
    WHERE id_evento = :id_evento 
      AND qtd_disponivel > 0
      AND data_valido_fim >= :hoje
");
$stmt_bilhetes->execute(['id_evento' => $id_evento, 'hoje' => $hoje]);
$tipos_bilhete = $stmt_bilhetes->fetchAll(PDO::FETCH_ASSOC);

// Verifica se há bilhetes
$bilhetes_esgotados = count($tipos_bilhete) === 0;
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TicketZone - <?= htmlspecialchars($evento['nome']) ?></title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>

    <?php include 'scripts/header.php'; ?>

    <main class="pagina-evento">
        <div class="coluna-esquerda">
            <div class="banner-evento">
                <img src="<?= htmlspecialchars($evento['imagem']) ?>" alt="<?= htmlspecialchars($evento['nome']) ?>">
            </div>

            <div class="detalhes-evento">
                <h2><?= htmlspecialchars($evento['nome']) ?></h2>

                <div class="info-badges">
                    <div class="badge-info">
                        <img src="assets/calendario.svg" alt="Data" class="badge-icone">
                        <span>
                            <?php 
                            // Lógica de datas idêntica ao teu index.php
                            if (!empty($evento['data_fim']) && $evento['data_fim'] !== $evento['data_inicio']) {
                                echo htmlspecialchars($evento['data_inicio'] . ' a ' . $evento['data_fim']);
                            } else {
                                echo htmlspecialchars($evento['data_inicio']);
                            }
                            ?>
                        </span>
                    </div>
                    <div class="badge-info">
                        <img src="assets/mapa.svg" alt="Local" class="badge-icone">
                        <span><?= htmlspecialchars($evento['local']) ?></span>
                    </div>
                </div>

                <h3>Descrição</h3>
                <p><?= nl2br(htmlspecialchars($evento['descricao'])) ?></p>
            </div>
        </div>

        <div class="coluna-direita">
            <div class="cartao-compra">
                <h3>Comprar Bilhetes</h3>
                <hr class="divisor">

                <?php if ($bilhetes_esgotados): ?>
                    <p style="color: #d9534f; text-align: center; font-weight: bold; padding: 20px 0;">
                        Bilhetes Indisponíveis ou Esgotados para este evento.
                    </p>
                <?php else: ?>
                    <div class="linha-selecao">
                        <div class="info-preco">
                            <select id="seletor-bilhete" class="seletor-bilhete-dropdown" name="id_tipo_bilhete_temp">
                                <?php foreach ($tipos_bilhete as $bilhete): ?>
                                    <option value="<?= $bilhete['id'] ?>" data-preco="<?= $bilhete['preco'] ?>">
                                        <?= htmlspecialchars($bilhete['nome']) ?> (<?= number_format($bilhete['preco'], 2) ?>€)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="valor-bilhete" id="display-preco-unitario">0.00€</div>
                        </div>
                        
                        <div class="seletor-quantidade">
                            <button type="button" id="btn-menos">&#8722;</button>
                            <span id="quantidade">1</span>
                            <button type="button" id="btn-mais">&#43;</button>
                        </div>
                    </div>

                    <div class="caixa-resumo">
                        <div class="linha-resumo">
                            <span>Subtotal</span>
                            <span id="subtotal">0.00€</span>
                        </div>
                        <div class="linha-resumo">
                            <span>Taxas (10%)</span>
                            <span id="taxas">0.00€</span>
                        </div>
                        <div class="linha-resumo total-final">
                            <span>Total</span>
                            <span id="total">0.00€</span>
                        </div>
                    </div>

                    <?php if (isset($_SESSION['id_utilizador'])): ?>
                        <form method="POST" action="scripts/carrinho_adicionar.php" id="form-compra">
                            <input type="hidden" name="id_tipo_bilhete" id="input-tipo-escondido" value=""> 
                            <input type="hidden" name="quantidade" id="input-quantidade-escondido" value="1">
                            
                            <button type="submit" class="btn-pagamento">Adicionar ao Carrinho</button>
                        </form>
                    <?php else: ?>
                        <a href="login.php" class="btn-pagamento" style="display:block; text-align:center; text-decoration:none; box-sizing: border-box;">
                            Fazer Login para Comprar
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <p>2026 TicketZone. Todos os direitos reservados.</p>
    </footer>

    <?php include 'scripts/carrinho_modal.php'; ?>
    <script src="js/carrinho.js"></script>
    <script src="js/compra.js"></script> </body>
</html>