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
        <a href="index.php" class="btn-voltar"> < Voltar</a>
        <div class="logo-pagamento">
            <div class="janela-logo">
                <img src="images/logo.png" alt="TicketZone Logo" class="pagamento-img">
            </div>
        </div>
    </nav>

    <main class="container-pagamento">
        <div class="layout-pagamento">
            
            <div class="resumo-encomenda">
                <h2>Resumo da Compra</h2>
                <p class="subtitulo-pagamento">Confirme os bilhetes antes de pagar.</p>
                <p class="aviso-pagamento">* se necessário remover itens, retorne para o carrinho e remova-os.</p>
                <hr class="divisor">
                
                <div class="lista-itens-resumo">
                    <div class="item-resumo">
                        <div class="item-info">
                            <span class="item-nome">Passe Geral (8 dias) - Queima das Fitas 2026</span>
                            <span class="item-qtd">Qtd: 2</span>
                        </div>
                        <span class="item-preco">120.00€</span>
                    </div>
                    
                    <div class="item-resumo">
                        <div class="item-info">
                            <span class="item-nome">Bilhete Diário (3 Maio) - Queima das Fitas 2026</span>
                            <span class="item-qtd">Qtd: 1</span>
                        </div>
                        <span class="item-preco">30.00€</span>
                    </div>
                </div>

                <div class="caixa-resumo" style="margin-top: 20px; margin-bottom: 0;">
                    <div class="linha-resumo">
                        <span>Subtotal</span>
                        <span>150.00€</span>
                    </div>
                    <div class="linha-resumo">
                        <span>Taxas (10%)</span>
                        <span>15.00€</span>
                    </div>
                    <div class="linha-resumo total-final">
                        <span>Total a Pagar</span>
                        <span style="color: #1a1a5e; font-size: 18px;">165.00€</span>
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
                        <input type="text" id="num_cartao" name="num_cartao" placeholder="0000 0000 0000 0000" maxlength="19">
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

                    <button type="submit" class="btn-confirmar-pagamento">Pagar 165.00€</button>
                </form>
            </div>

        </div>
    </main>

    <script src="js/pagamentos.js"></script>
</body>
</html>