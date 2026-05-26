<div id="overlay-carrinho" class="overlay-escondido"></div>
<div id="modal-carrinho" class="modal-escondido">
    <div class="cabecalho-carrinho">
        <h2>O teu Carrinho</h2>
        <button id="fechar-carrinho">&times;</button>
    </div>
    
    <div class="conteudo-carrinho">
        <div class="item-carrinho">
            <img src="images/Nos.jpg" alt="NOS Alive" class="img-item-carrinho">
            <div class="detalhes-item-carrinho">
                <h4>NOS Alive 2026</h4>
                <p>Passe Geral (3 Dias)</p>
                <div class="rodape-item-carrinho">
                    <span class="qtd-item">Qtd: 2</span>
                    <span class="preco-item">90.00€</span>
                </div>
            </div>
            <form method="POST" action="include/carrinho_remover.php" class="form-remover">
                <input type="hidden" name="id_item_carrinho" value="1">
                <button type="submit" class="btn-remover-item" title="Remover do carrinho">&times;</button>
            </form>
        </div>

        <div class="item-carrinho">
            <img src="images/queima.jpg" alt="Queima das Fitas" class="img-item-carrinho">
            <div class="detalhes-item-carrinho">
                <h4>Queima das Fitas</h4>
                <p>Bilhete Diário (Sábado)</p>
                <div class="rodape-item-carrinho">
                    <span class="qtd-item">Qtd: 1</span>
                    <span class="preco-item">15.00€</span>
                </div>
            </div>
            <form method="POST" action="include/carrinho_remover.php" class="form-remover">
                <input type="hidden" name="id_item_carrinho" value="2">
                <button type="submit" class="btn-remover-item" title="Remover do carrinho">&times;</button>
            </form>
        </div>

        </div>

    <div class="rodape-carrinho">
        <div class="resumo-carrinho">
            <div class="linha-resumo">
                <span>Subtotal</span>
                <span>105.00€</span>
            </div>
            <div class="linha-resumo">
                <span>Taxas (10%)</span>
                <span>10.50€</span>
            </div>
            <div class="linha-resumo total-final">
                <span>Total</span>
                <span>115.50€</span>
            </div>
        </div>
        <form method="POST" action="include/confirmar_compra.php">
            <button type="submit" class="btn-pagamento">Finalizar Compra</button>
        </form>
    </div>
</div>