<?php
// scripts/carrinho_adicionar.php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Limpar reservas expiradas de todos os utilizadores antes de continuar
include_once __DIR__ . '/limpar_reservas.php';

if (!isset($_SESSION['id_utilizador'])) {
    header("Location: ../login.php");
    exit();
}

$id_tipo_bilhete = isset($_POST['id_tipo_bilhete']) ? (int)$_POST['id_tipo_bilhete'] : 0;
$quantidade = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : 1;
$id_utilizador = $_SESSION['id_utilizador'];

if ($id_tipo_bilhete > 0 && $quantidade > 0) {
    try {
        $db = new SQLite3(__DIR__ . '/../ticketzone.db');
        $db->busyTimeout(5000); 
        $db->exec("PRAGMA foreign_keys = ON;");

        // INICIAR TRANSAÇÃO - Protege a Base de Dados
        $db->exec('BEGIN');

        // 1. Verificar se há stock suficiente
        $stmt_stock = $db->prepare("SELECT qtd_disponivel FROM tipos_bilhete WHERE id = :id_tipo");
        $stmt_stock->bindValue(':id_tipo', $id_tipo_bilhete, SQLITE3_INTEGER);
        $result_stock = $stmt_stock->execute()->fetchArray(SQLITE3_ASSOC);

        if (!$result_stock || $result_stock['qtd_disponivel'] < $quantidade) {
            throw new Exception("Stock insuficiente. Os bilhetes podem ter esgotado ou sido reservados por outro utilizador.");
        }

        // 2. Descontar o stock do bilhete
        $stmt_desconto = $db->prepare("UPDATE tipos_bilhete SET qtd_disponivel = qtd_disponivel - :qtd WHERE id = :id_tipo");
        $stmt_desconto->bindValue(':qtd', $quantidade, SQLITE3_INTEGER);
        $stmt_desconto->bindValue(':id_tipo', $id_tipo_bilhete, SQLITE3_INTEGER);
        $stmt_desconto->execute();

        // 3. Gerir o Carrinho
        $stmt_check = $db->prepare("SELECT id, quantidade FROM carrinho WHERE id_utilizador = :id_utilizador AND id_tipo_bilhete = :id_tipo_bilhete");
        $stmt_check->bindValue(':id_utilizador', $id_utilizador, SQLITE3_INTEGER);
        $stmt_check->bindValue(':id_tipo_bilhete', $id_tipo_bilhete, SQLITE3_INTEGER);
        $item_existente = $stmt_check->execute()->fetchArray(SQLITE3_ASSOC);

        if ($item_existente) {
            $nova_quantidade = $item_existente['quantidade'] + $quantidade;
            // Atualizamos a quantidade E a data_adicao para reiniciar os 15 minutos!
            $stmt_update = $db->prepare("UPDATE carrinho SET quantidade = :quantidade, data_adicao = CURRENT_TIMESTAMP WHERE id = :id_carrinho");
            $stmt_update->bindValue(':quantidade', $nova_quantidade, SQLITE3_INTEGER);
            $stmt_update->bindValue(':id_carrinho', $item_existente['id'], SQLITE3_INTEGER);
            $stmt_update->execute();
        } else {
            $stmt_insert = $db->prepare("INSERT INTO carrinho (id_utilizador, id_tipo_bilhete, quantidade, data_adicao) VALUES (:id_utilizador, :id_tipo_bilhete, :quantidade, CURRENT_TIMESTAMP)");
            $stmt_insert->bindValue(':id_utilizador', $id_utilizador, SQLITE3_INTEGER);
            $stmt_insert->bindValue(':id_tipo_bilhete', $id_tipo_bilhete, SQLITE3_INTEGER);
            $stmt_insert->bindValue(':quantidade', $quantidade, SQLITE3_INTEGER);
            $stmt_insert->execute();
        }

        $db->exec('COMMIT');
        $db->close();

    } catch (Exception $e) {
        if (isset($db)) $db->exec('ROLLBACK');
        die("<h2 style='color:red;'>Erro ao adicionar bilhete!</h2><p>" . $e->getMessage() . "</p><a href='../index.php'>Voltar</a>");
    }
}

$id_retorno = isset($_POST['id_evento_retorno']) ? (int)$_POST['id_evento_retorno'] : 0;
if ($id_retorno > 0) {
    header("Location: ../compra.php?id=" . $id_retorno . "&carrinho=aberto");
} else {
    header("Location: ../index.php?carrinho=aberto");
}
exit();
?>