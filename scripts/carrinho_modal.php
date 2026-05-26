<?php
// include/carrinho_modal.php

// 1. Inclui o script que acabámos de criar para ir buscar os dados à BD
include_once 'carrinho_ver.php';

// 2. Verifica se o parâmetro de URL indica que o carrinho deve iniciar aberto
$classe_visibilidade = (isset($_GET['carrinho']) && $_GET['carrinho'] === 'aberto') ? 'ativo' : '';
?>

<div id="overlay-carrinho" class="<?php echo $classe_visibilidade; ?>"></div>

<div id="modal-carrinho" class="<?php echo $classe_visibilidade; ?>">
    <div class="cabecalho-carrinho">
        <h2>O teu Carrinho</h2>
        <button id="fechar-carrinho">&times;</button>
    </div>
    
    <div class="conteudo-carrinho">
        <?php if (empty($itens_carrinho)): ?>
            <p class="carrinho-vazio">O teu carrinho está vazio.</p>
        <?php else: ?>
            
            <?php foreach ($itens_carrinho as $item): ?>
                <div class="item-carrinho">
                    <img src="<?php echo htmlspecialchars($item['evento_imagem']); ?>" 
                         alt="<?php echo htmlspecialchars($item['evento_nome']); ?>" 
                         class="img-item-carrinho">
                    
                    <div class="detalhes-item-carrinho">
                        <h4><?php echo htmlspecialchars($item['evento_nome']); ?></h4>
                        <p><?php echo htmlspecialchars($item['tipo_bilhete']); ?></p>
                        
                        <div class="rodape-item-carrinho">
                            <span class="qtd-item">Qtd: <?php echo $item['quantidade']; ?></span>
                            <span class="preco-item"><?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', ' '); ?>€</span>
                        </div>
                    </div>
                    
                    <form method="POST" action="include/carrinho_remover.php" class="form-remover">
                        <input type="hidden" name="id_item_carrinho" value="<?php echo $item['id_carrinho']; ?>">
                        <button type="submit" class="btn-remover-item" title="Remover do carrinho">&times;</button>
                    </form>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>

    <?php if (!empty($itens_carrinho)): ?>
        <div class="rodape-carrinho">
            <div class="resumo-carrinho">
                <div class="linha-resumo">
                    <span>Subtotal</span>
                    <span><?php echo number_format($subtotal, 2, ',', ' '); ?>€</span>
                </div>
                <div class="linha-resumo">
                    <span>Taxas (10%)</span>
                    <span><?php echo number_format($taxas, 2, ',', ' '); ?>€</span>
                </div>
                <div class="linha-resumo total-final">
                    <span>Total</span>
                    <span><?php echo number_format($total, 2, ',', ' '); ?>€</span>
                </div>
            </div>
            
            <form method="POST" action="include/confirmar_compra.php">
                <button type="submit" class="btn-pagamento">Finalizar Compra</button>
            </form>
        </div>
    <?php endif; ?>
</div>