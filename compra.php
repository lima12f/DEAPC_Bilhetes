<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TicketZone - Comprar Bilhete</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>

    <?php include 'scripts/header.php'; ?>

    <main class="pagina-evento">

        <div class="coluna-esquerda">
            <div class="banner-evento">
                <img src="images/queima.jpg" alt="Queima das Fitas">
            </div>

            <div class="detalhes-evento">
                <h2>Queima das Fitas do Porto 2026</h2>

                <div class="info-badges">
                    <div class="badge-info">
                        <img src="assets/calendario.svg" alt="Data" class="badge-icone">
                        <span>2-9 Maio 2026</span>
                    </div>
                    <div class="badge-info">
                        <img src="assets/mapa.svg" alt="Local" class="badge-icone">
                        <span>Queimódromo do Porto</span>
                    </div>
                </div>

                <h3>Descrição</h3>
                <p>A Queima das Fitas do Porto, que decorre de 2 a 9 de maio de 2026, é a maior festa estudantil da cidade, celebrando o final do percurso académico.</p>
            </div>
        </div>

        <div class="coluna-direita">
            <div class="cartao-compra">
                <h3>Comprar Bilhetes</h3>
                <hr class="divisor">

                <div class="linha-selecao">
                    <div class="info-preco">
                        <div class="nome-bilhete">Passe Geral (8 dias)</div>
                        <div class="valor-bilhete">60€</div>
                    </div>
                    <div class="seletor-quantidade">
                        <button id="btn-menos">&#8722;</button>
                        <span id="quantidade">1</span>
                        <button id="btn-mais">&#43;</button>
                    </div>
                </div>

                <div class="caixa-resumo">
                    <div class="linha-resumo">
                        <span>Subtotal</span>
                        <span id="subtotal">60.00€</span>
                    </div>
                    <div class="linha-resumo">
                        <span>Taxas (10%)</span>
                        <span id="taxas">6.00€</span>
                    </div>
                    <div class="linha-resumo total-final">
                        <span>Total</span>
                        <span id="total">66.00€</span>
                    </div>
                </div>

                <?php if (isset($_SESSION['id_utilizador'])): ?>
                    <form method="POST" action="scripts/carrinho_adicionar.php">
                        <input type="hidden" name="id_tipo_bilhete" value="1"> 
                        
                        <input type="hidden" name="quantidade" id="input-quantidade-escondido" value="1">
                        
                        <button type="submit" class="btn-pagamento">Adicionar ao Carrinho</button>
                    </form>
                <?php else: ?>
                    <a href="login.php" class="btn-pagamento" style="display:block; text-align:center; text-decoration:none; box-sizing: border-box;">Fazer Login para Comprar</a>
                <?php endif; ?>
            </div>
        </div>

    </main>

    <footer>
        <p>2026 TicketZone. Todos os direitos reservados.</p>
    </footer>

    <?php include 'scripts/carrinho_modal.php'; ?>
    <script src="js/carrinho.js"></script>

</body>
</html>