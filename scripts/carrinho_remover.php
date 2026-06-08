<?php
// include/carrinho_remover.php
session_start();

if (!isset($_SESSION['id_utilizador'])) {
    header("Location: ../login.php");
    exit();
}

// 2. Receber os dados do formulário (via POST)
$id_item_carrinho = isset($_POST['id_item_carrinho']) ? (int)$_POST['id_item_carrinho'] : 0;
$id_utilizador = $_SESSION['id_utilizador'];

// se item válido
if ($id_item_carrinho > 0) {
    try {
        // 3. Ligar à base de dados SQLite
        $db = new SQLite3('../ticketzone.db');
        $db->exec("PRAGMA foreign_keys = ON;");

        // 4. Apagar o item do carrinho
        $stmt_delete = $db->prepare("DELETE FROM carrinho WHERE id = :id_item_carrinho AND id_utilizador = :id_utilizador");
        $stmt_delete->bindValue(':id_item_carrinho', $id_item_carrinho, SQLITE3_INTEGER);
        $stmt_delete->bindValue(':id_utilizador', $id_utilizador, SQLITE3_INTEGER);
        
        $stmt_delete->execute();

        $db->close();

    } catch (Exception $e) {
        echo "Erro: " . $e->getMessage();
    }
}

// 5. Redirecionar de volta à página anterior onde se encontrava o carrinho, para que o modal possa ser reaberto automaticamente
$pagina_anterior = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php';

// Limpar parâmetros antigos da URL e adicionar o ?carrinho=aberto para o modal reabrir automaticamente
$url_limpa = strtok($pagina_anterior, '?'); 
header("Location: " . $url_limpa . "?carrinho=aberto");
exit();
?>