<?php
// Inicia a sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteção da página: se o utilizador não estiver logado, redireciona para o login
if (!isset($_SESSION['id_utilizador'])) {
    header("Location: login.php");
    exit;
}

// Ir buscar os dados guardados na sessão após o login correto
$nome_utilizador = $_SESSION['username'];
$email_utilizador = $_SESSION['email'];

// Gerar as iniciais para o Avatar (ex: apanha as primeiras 2 letras do username em Maiúsculas)
$iniciais_avatar = strtoupper(substr($nome_utilizador, 0, 2));
?>
<!DOCTYPE html>
<html lang="pt">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TicketZone - Perfil</title>
    <link rel="stylesheet" href="styles/perfil.css" />
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

        <div class="tickets-grid">
          <div class="ticket-card" style="display: flex; align-items: center; justify-content: center; color: #888; font-size: 13px;">Brevemente</div>
          <div class="ticket-card" style="display: flex; align-items: center; justify-content: center; color: #888; font-size: 13px;">Brevemente</div>
          <div class="ticket-card" style="display: flex; align-items: center; justify-content: center; color: #888; font-size: 13px;">Brevemente</div>
        </div>

      </section>

    </main>

    <footer>
        <p>&copy; 2026 TicketZone. Todos os direitos reservados.</p>
    </footer>

    <?php include 'scripts/carrinho_modal.php'; ?>
    <script src="js/carrinho.js"></script>

  </body>
</html>