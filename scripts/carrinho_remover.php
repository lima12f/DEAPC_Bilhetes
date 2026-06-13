<?php
// scripts/carrinho_remover.php
session_start();

if (!isset($_SESSION['id_utilizador'])) {
    header("Location: ../login.php");
    exit();
}

$id_item_carrinho = isset($_POST['id_item_carrinho']) ? (int)$_POST['id_item_carrinho'] : 0;
$id_utilizador = $_SESSION['id_utilizador'];

if ($id_item_carrinho > 0) {
    try {
        $db = new SQLite3(__DIR__ . '/../ticketzone.db');
        $db->busyTimeout(5000);
        $db->exec("PRAGMA foreign_keys = ON;");
        $db->exec('BEGIN');

        // 1. Descobrir os dados do item antes de o apagar
        $stmt_get = $db->prepare("SELECT id_tipo_bilhete, quantidade FROM carrinho WHERE id = :id AND id_utilizador = :user");
        $stmt_get->bindValue(':id', $id_item_carrinho, SQLITE3_INTEGER);
        $stmt_get->bindValue(':user', $id_utilizador, SQLITE3_INTEGER);
        $item = $stmt_get->execute()->fetchArray(SQLITE3_ASSOC);

        if ($item) {
            // 2. Repor o stock
            $stmt_repo = $db->prepare("UPDATE tipos_bilhete SET qtd_disponivel = qtd_disponivel + :qtd WHERE id = :id_tipo");
            $stmt_repo->bindValue(':qtd', $item['quantidade'], SQLITE3_INTEGER);
            $stmt_repo->bindValue(':id_tipo', $item['id_tipo_bilhete'], SQLITE3_INTEGER);
            $stmt_repo->execute();

            // 3. Apagar o item do carrinho
            $stmt_delete = $db->prepare("DELETE FROM carrinho WHERE id = :id AND id_utilizador = :user");
            $stmt_delete->bindValue(':id', $id_item_carrinho, SQLITE3_INTEGER);
            $stmt_delete->bindValue(':user', $id_utilizador, SQLITE3_INTEGER);
            $stmt_delete->execute();
        }

        $db->exec('COMMIT');
        $db->close();

    } catch (Exception $e) {
        if (isset($db)) $db->exec('ROLLBACK');
        echo "Erro: " . $e->getMessage();
        exit();
    }
}

$pagina_anterior = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php';
$url_limpa = strtok($pagina_anterior, '?'); 
header("Location: " . $url_limpa . "?carrinho=aberto");
exit();
?>