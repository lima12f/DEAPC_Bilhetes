<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_utilizador'])) {
    header("Location: login.php");
    exit;
}

$nome_utilizador  = $_SESSION['username'];
$email_utilizador = $_SESSION['email'];
$iniciais_avatar  = strtoupper(substr($nome_utilizador, 0, 2));
?>
<!DOCTYPE html>
<html lang="pt">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TicketZone - Perfil</title>
    <link rel="stylesheet" href="styles/perfil.css" />
    <script src="js/carrinho.js"></script>
  </head>
  <body>

    <?php include 'scripts/header.php'; ?>

    <main class="profile-container">

      <aside class="sidebar">
        <div class="avatar"><?php echo htmlspecialchars($iniciais_avatar); ?></div>
        <h2><?php echo htmlspecialchars($nome_utilizador); ?></h2>
        <p><?php echo htmlspecialchars($email_utilizador); ?></p>
        <ul class="sidebar-menu">
          <li><a href="#">Os Meus Bilhetes</a></li>
          <li><a href="include/logout.php">Terminar Sessão</a></li>
        </ul>
      </aside>

      <section class="content">
        <h2>Os Meus Bilhetes</h2>

        <?php include 'scripts/carregar_entradas.php'; ?>

        <div class="tickets-grid">
          <?php if (empty($bilhetes)): ?>
            <p style="color: #888; font-size: 13px;">Ainda não compraste nenhum bilhete.</p>
          <?php else: ?>
            <?php foreach ($bilhetes as $bilhete): ?>
              <?php
                $data_evento_fmt = date('d \d\e F \d\e Y, H:i', strtotime($bilhete['data_evento']));
                $data_compra_fmt = date('d \d\e F \d\e Y, H:i', strtotime($bilhete['data_compra']));
              ?>
              <div class="ticket-card">
                <?php if (!empty($bilhete['imagem'])): ?>
                  <img class="ticket-card-imagem"
                       src="<?php echo htmlspecialchars($bilhete['imagem']); ?>"
                       alt="<?php echo htmlspecialchars($bilhete['nome_evento']); ?>" />
                <?php else: ?>
                  <div class="ticket-card-imagem-placeholder">Sem imagem</div>
                <?php endif; ?>
                <div class="ticket-card-info">
                  <span class="ticket-card-badge"><?php echo htmlspecialchars($bilhete['tipo_bilhete']); ?></span>
                  <h3><?php echo htmlspecialchars($bilhete['nome_evento']); ?></h3>
                  <p><span>Data:</span> <?php echo $data_evento_fmt; ?></p>
                  <p><span>Local:</span> <?php echo htmlspecialchars($bilhete['local_evento']); ?></p>
                  <p><span>Quantidade:</span> <?php echo htmlspecialchars($bilhete['quantidade']); ?></p>
                  <p><span>Preco unit.:</span> <?php echo number_format($bilhete['preco_unitario'], 2); ?>€</p>
                  <p><span>Comprado em:</span> <?php echo $data_compra_fmt; ?></p>
                  <p><span>ID Pagamento:</span> #<?php echo htmlspecialchars($bilhete['id_pagamento'] ?? 'N/A'); ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

      </section>

    </main>

    <footer>
        <p>2026 TicketZone. Todos os direitos reservados.</p>
    </footer>

    <?php include 'scripts/carrinho_modal.php'; ?>

  </body>
</html>