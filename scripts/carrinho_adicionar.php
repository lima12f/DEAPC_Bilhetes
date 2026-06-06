<?php
// scripts/carrinho_adicionar.php
session_start();

// 1. Mostrar os erros no ecrã para descobrirmos imediatamente se a BD está bloqueada!
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Verificar se o utilizador tem login feito
if (!isset($_SESSION['id_utilizador'])) {
    header("Location: ../login.php");
    exit();
}

// 3. Receber os dados do formulário (via POST)
$id_tipo_bilhete = isset($_POST['id_tipo_bilhete']) ? (int)$_POST['id_tipo_bilhete'] : 0;
$quantidade = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : 1;
$id_utilizador = $_SESSION['id_utilizador'];

// Só avançamos se recebermos um bilhete válido
if ($id_tipo_bilhete > 0 && $quantidade > 0) {
    try {
        // CORREÇÃO 1: Usar __DIR__ para não haver falhas de caminho no Windows
        $db = new SQLite3(__DIR__ . '/../ticketzone.db');
        
        // CORREÇÃO 2: Espera até 5 segundos caso a base de dados esteja ocupada (timeout)
        $db->busyTimeout(5000); 
        $db->exec("PRAGMA foreign_keys = ON;");

        // 4. Verificar se este bilhete já existe no carrinho deste utilizador
        $stmt_check = $db->prepare("SELECT id, quantidade FROM carrinho WHERE id_utilizador = :id_utilizador AND id_tipo_bilhete = :id_tipo_bilhete");
        $stmt_check->bindValue(':id_utilizador', $id_utilizador, SQLITE3_INTEGER);
        $stmt_check->bindValue(':id_tipo_bilhete', $id_tipo_bilhete, SQLITE3_INTEGER);
        
        $resultado = $stmt_check->execute();
        $item_existente = $resultado->fetchArray(SQLITE3_ASSOC);

        if ($item_existente) {
            // Se já lá estiver, somamos a nova quantidade
            $nova_quantidade = $item_existente['quantidade'] + $quantidade;
            $stmt_update = $db->prepare("UPDATE carrinho SET quantidade = :quantidade WHERE id = :id_carrinho");
            $stmt_update->bindValue(':quantidade', $nova_quantidade, SQLITE3_INTEGER);
            $stmt_update->bindValue(':id_carrinho', $item_existente['id'], SQLITE3_INTEGER);
            $stmt_update->execute();
        } else {
            // Se for um bilhete novo
            $stmt_insert = $db->prepare("INSERT INTO carrinho (id_utilizador, id_tipo_bilhete, quantidade) VALUES (:id_utilizador, :id_tipo_bilhete, :quantidade)");
            $stmt_insert->bindValue(':id_utilizador', $id_utilizador, SQLITE3_INTEGER);
            $stmt_insert->bindValue(':id_tipo_bilhete', $id_tipo_bilhete, SQLITE3_INTEGER);
            $stmt_insert->bindValue(':quantidade', $quantidade, SQLITE3_INTEGER);
            $stmt_insert->execute();
        }

        $db->close();

    } catch (Exception $e) {
        // CORREÇÃO 3: Se houver erro, a página "morre" e avisa-te. Assim sabes logo o que falhou!
        die("<h2 style='color:red;'>Erro Fatal na Base de Dados!</h2><p>" . $e->getMessage() . "</p><p>Dica: Fecha o 'DB Browser for SQLite' se o tiveres aberto!</p>");
    }
}

// 5. Redirecionar de volta (Substitui o HTTP_REFERER pela abordagem segura do POST)
$id_retorno = isset($_POST['id_evento_retorno']) ? (int)$_POST['id_evento_retorno'] : 0;

if ($id_retorno > 0) {
    header("Location: ../compra.php?id=" . $id_retorno . "&carrinho=aberto");
    exit();
} else {
    // Fallback genérico caso falhe
    header("Location: ../index.php?carrinho=aberto");
    exit();
}
?>