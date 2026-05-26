<?php
//este ficheiro será incluído em todas as páginas para mostrar o header e verificar se o utilizador está logado ou não
// inicia a sessão apenas se ainda não tiver sido iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <a href="index.php" class="logo-link">
        <img src="images/logo.png" alt="TicketZone">
    </a>
    <nav style="display: flex; align-items: center; gap: 15px;">
        <?php if (isset($_SESSION['id_utilizador'])): ?>
            
            <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin'): ?>
                <a href="admin.php">Admin</a>
            <?php endif; ?>
            
            <a href="perfil.php" class="btn-perfil">Perfil</a>
            <a href="include/logout.php">Sair</a>
            
            <button id="btn-abrir-carrinho" class="btn-carrinho-nav" title="Ver Carrinho" style="background:none; border:none; cursor:pointer; margin-left: 10px;">
                <img src="assets/carrinho.svg" alt="Carrinho" style="height: 24px; width: 24px; filter: invert(1);">
            </button>

        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="registo.php">Criar conta</a>
        <?php endif; ?>
    </nav>
</header>