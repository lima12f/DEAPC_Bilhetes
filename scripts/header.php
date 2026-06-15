<?php
// este ficheiro será incluído em todas as páginas para mostrar o header e verificar se o utilizador está logado ou não
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Obtém o nome do ficheiro atual para controlar os botões visíveis
$pagina_atual = basename($_SERVER['PHP_SELF']);

// VALIDAÇÃO DE ACESSOS

if (isset($_SESSION['tipo'])) {
    
    //Se é ADMIN e está a tentar aceder a outra página qualquer
    if ($_SESSION['tipo'] === 'admin' && $pagina_atual !== 'admin.php') {
        header("Location: admin.php");
        exit();
    }
}
?>
<header>
    <a href="<?php echo (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin') ? 'admin.php' : 'index.php'; ?>" class="logo-link">
        <img src="images/logo.png" alt="TicketZone">
    </a>
    
    <nav style="display: flex; align-items: center; gap: 20px;">
        <?php if (isset($_SESSION['id_utilizador'])): ?>
            
            <?php if ($_SESSION['tipo'] === 'admin'): ?>
                
                <span style="color: #f39c12; font-weight: bold; font-size: 14px;">Modo Administrador</span>
                <a href="scripts/logout.php" class="btn-sair">Sair</a>
            
            <?php else: ?>
                
                <a href="index.php">Início</a>
                
                <?php if ($pagina_atual !== 'perfil.php'): ?>
                    <a href="perfil.php" class="btn-perfil">Perfil</a>
                <?php endif; ?>
                
                <button id="btn-abrir-carrinho" class="btn-carrinho-nav" title="Ver Carrinho" style="background:none; border:none; cursor:pointer; display: flex; align-items: center;">
                    <img src="assets/carrinho.svg" alt="Carrinho" style="height: 24px; width: 24px; filter: invert(1);">
                </button>

                <a href="scripts/logout.php" class="btn-sair">Sair</a>
                
            <?php endif; ?>

        <?php else: ?>
            
            <a href="login.php">Login</a>
            <a href="registo.php">Criar conta</a>
            
        <?php endif; ?>
    </nav>
</header>