<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TicketZone</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>

    <?php include 'scripts/header.php'; ?>

    <div id="zona-pesquisa">
        <h1>Encontra o teu próximo evento</h1>
        <p>Milhares de eventos à tua espera. Garante o teu lugar hoje!</p>
        <div id="barra-procura">
            <input type="text" placeholder="Pesquisar">
            <button>Pesquisar</button>
        </div>
    </div>

    <div id="conteudo-eventos">
        <h1>Eventos em Destaque</h1>
        <div id="bloco-cards">

            <a href="compra.php" class="card">
                <div class="card-imagem">
                    <img src="images/Nos.jpg" alt="Nos Alive 2026">
                    <span class="card-badge">Música</span>
                </div>
                <div class="card-info">
                    <h3 class="card-titulo">NOS Alive 2026</h3>
                    <div class="card-detalhe">
                        <img src="assets/calendario.svg" alt="data" class="icone-detalhe">
                        <span>10 - 12 Jul 2026</span>
                    </div>
                    <div class="card-detalhe">
                        <img src="assets/mapa.svg" alt="local" class="icone-detalhe">
                        <span>Passeio Marítimo de Algés, Lisboa</span>
                    </div>
                    <div class="card-footer">
                        <span class="card-preco">A partir de <strong>45€</strong></span>
                        <span class="card-btn">Comprar</span>
                    </div>
                </div>
            </a>

            <a href="compra.php" class="card">
                <div class="card-imagem">
                    <img src="images/porto.jpg" alt="FC Porto vs Benfica">
                    <span class="card-badge">Desporto</span>
                </div>
                <div class="card-info">
                    <h3 class="card-titulo">FC Porto vs Benfica</h3>
                    <div class="card-detalhe">
                        <img src="assets/calendario.svg" alt="data" class="icone-detalhe">
                        <span>5 Jun 2026</span>
                    </div>
                    <div class="card-detalhe">
                        <img src="assets/mapa.svg" alt="local" class="icone-detalhe">
                        <span>Estádio do Dragão, Porto</span>
                    </div>
                    <div class="card-footer">
                        <span class="card-preco">A partir de <strong>30€</strong></span>
                        <span class="card-btn">Comprar</span>
                    </div>
                </div>
            </a>

            <a href="compra.php" class="card">
                <div class="card-imagem">
                    <img src="images/fantasma.jpg" alt="O Fantasma da Ópera">
                    <span class="card-badge">Teatro</span>
                </div>
                <div class="card-info">
                    <h3 class="card-titulo">O Fantasma da Ópera</h3>
                    <div class="card-detalhe">
                        <img src="assets/calendario.svg" alt="data" class="icone-detalhe">
                        <span>20 - 30 Jun 2026</span>
                    </div>
                    <div class="card-detalhe">
                        <img src="assets/mapa.svg" alt="local" class="icone-detalhe">
                        <span>Teatro Nacional D. Maria II, Lisboa</span>
                    </div>
                    <div class="card-footer">
                        <span class="card-preco">A partir de <strong>25€</strong></span>
                        <span class="card-btn">Comprar</span>
                    </div>
                </div>
            </a>

        </div>
    </div>

    <footer>
        <p> 2026 TicketZone. Todos os direitos reservados.</p>
    </footer>

    <?php include 'scripts/carrinho_modal.php'; ?>
    <script src="js/carrinho.js"></script>

</body>
</html>