<?php
session_start();

// Verificar se existe uma sessao ativa antes de destruir
if (!isset($_SESSION['id_utilizador'])) {
    header("Location: ../login.php");
    exit;
}

// Limpar todas as variaveis de sessao
$_SESSION = [];

// Destruir o cookie de sessao, se existir
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destruir a sessao no servidor
session_destroy();

// Redirecionar para a pagina de login
header("Location: ../index.php?sucesso=logout");
exit;