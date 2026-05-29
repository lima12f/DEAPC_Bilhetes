<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'scripts/carregar_eventos_homepage.php';
?>
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
            <form method="GET" action="index.php" style="display: flex; width: 100%;">
                <?php if ($id_categoria_filtro > 0): ?>
                    <input type="hidden" name="categoria" value="<?php echo $id_categoria_filtro; ?>">
                <?php endif; ?>
                
                <input type="text" name="q" placeholder="Pesquisar por evento..." value="<?php echo htmlspecialchars($termo_pesquisa); ?>" style="flex: 1; border-radius: 8px 0 0 8px;">
                <button type="submit" style="border-radius: 0 8px 8px 0;">Pesquisar</button>
            </form>
        </div>

        <div class="filtros-categoria">
            <a href="index.php<?php echo $termo_pesquisa !== '' ? '?q=' . urlencode($termo_pesquisa) : ''; ?>" 
               class="badge-filtro <?php echo $id_categoria_filtro === 0 ? 'ativo' : ''; ?>">
               Todos
            </a>
            
            <?php foreach ($lista_categorias as $cat): ?>
                <?php 
                    // Constrói o link mantendo a pesquisa de texto (se existir)
                    $link_filtro = 'index.php?categoria=' . $cat['id'];
                    if ($termo_pesquisa !== '') {
                        $link_filtro .= '&q=' . urlencode($termo_pesquisa);
                    }
                ?>
                <a href="<?php echo $link_filtro; ?>" 
                   class="badge-filtro <?php echo $id_categoria_filtro === $cat['id'] ? 'ativo' : ''; ?>">
                   <?php echo htmlspecialchars($cat['nome']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="conteudo-eventos">
        <h1>
            <?php 
            if ($termo_pesquisa !== '' && $id_categoria_filtro > 0) {
                // Descobrir o nome da categoria selecionada para o título
                $nome_cat_atual = array_column($lista_categorias, 'nome', 'id')[$id_categoria_filtro] ?? 'Categoria';
                echo 'Resultados de "' . htmlspecialchars($termo_pesquisa) . '" em ' . htmlspecialchars($nome_cat_atual);
            } elseif ($termo_pesquisa !== '') {
                echo 'Resultados da pesquisa para: "' . htmlspecialchars($termo_pesquisa) . '"';
            } elseif ($id_categoria_filtro > 0) {
                $nome_cat_atual = array_column($lista_categorias, 'nome', 'id')[$id_categoria_filtro] ?? 'Categoria';
                echo 'Eventos da categoria: ' . htmlspecialchars($nome_cat_atual);
            } else {
                echo 'Eventos em Destaque';
            }
            ?>
        </h1>
        
        <div id="bloco-cards">
            <?php if (empty($lista_eventos)): ?>
                <p style="text-align: center; width: 100%; color: #666; font-size: 18px; padding: 40px 0;">
                    Não encontrámos nenhum evento com estes critérios. Tenta outra pesquisa!
                </p>
            <?php else: ?>
                <?php foreach ($lista_eventos as $evento): ?>
                    <a href="compra.php?id=<?php echo $evento['id']; ?>" class="card">
                        <div class="card-imagem">
                            <img src="<?php echo htmlspecialchars($evento['imagem']); ?>" alt="<?php echo htmlspecialchars($evento['nome']); ?>">
                            <span class="card-badge"><?php echo htmlspecialchars($evento['categoria_nome']); ?></span>
                        </div>
                        <div class="card-info">
                            <h3 class="card-titulo"><?php echo htmlspecialchars($evento['nome']); ?></h3>
                            <div class="card-detalhe">
                                <img src="assets/calendario.svg" alt="data" class="icone-detalhe">
                                <span>
                                    <?php 
                                    if (!empty($evento['data_fim']) && $evento['data_fim'] !== $evento['data_inicio']) {
                                        echo htmlspecialchars($evento['data_inicio'] . ' a ' . $evento['data_fim']);
                                    } else {
                                        echo htmlspecialchars($evento['data_inicio']);
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="card-detalhe">
                                <img src="assets/mapa.svg" alt="local" class="icone-detalhe">
                                <span><?php echo htmlspecialchars($evento['local']); ?></span>
                            </div>
                            
                            <div class="card-footer">
                                <span class="card-preco">A partir de <strong><?php echo (int)$evento['preco_minimo']; ?>€</strong></span>
                                <span class="card-btn">Comprar</span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>2026 TicketZone. Todos os direitos reservados.</p>
    </footer>

    <?php include 'scripts/carrinho_modal.php'; ?>
    <script src="js/carrinho.js"></script>

</body>
</html>