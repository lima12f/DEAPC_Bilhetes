<?php
// include/carrinho_adicionar.php
session_start();

// 1. Verificar se o utilizador tem login feito
if (!isset($_SESSION['id_utilizador'])) {
    header("Location: ../login.php");
    exit();
}

// 2. Receber os dados do formulário (via POST)
// Usamos (int) para garantir que os valores são sempre números inteiros (segurança)
$id_tipo_bilhete = isset($_POST['id_tipo_bilhete']) ? (int)$_POST['id_tipo_bilhete'] : 0;
$quantidade = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : 1;
$id_utilizador = $_SESSION['id_utilizador'];

// Só avançamos se recebermos um bilhete válido
if ($id_tipo_bilhete > 0 && $quantidade > 0) {
    try {
        // 3. Ligar à base de dados SQLite
        $db = new SQLite3('../ticketzone.db');
        $db->exec("PRAGMA foreign_keys = ON;");

        // 4. Verificar se este bilhete já existe no carrinho deste utilizador
        $stmt_check = $db->prepare("SELECT id, quantidade FROM carrinho WHERE id_utilizador = :id_utilizador AND id_tipo_bilhete = :id_tipo_bilhete");
        $stmt_check->bindValue(':id_utilizador', $id_utilizador, SQLITE3_INTEGER);
        $stmt_check->bindValue(':id_tipo_bilhete', $id_tipo_bilhete, SQLITE3_INTEGER);
        
        $resultado = $stmt_check->execute();
        $item_existente = $resultado->fetchArray(SQLITE3_ASSOC);

        if ($item_existente) {
            // Se já lá estiver, somamos a nova quantidade à que já existia
            $nova_quantidade = $item_existente['quantidade'] + $quantidade;
            $stmt_update = $db->prepare("UPDATE carrinho SET quantidade = :quantidade WHERE id = :id_carrinho");
            $stmt_update->bindValue(':quantidade', $nova_quantidade, SQLITE3_INTEGER);
            $stmt_update->bindValue(':id_carrinho', $item_existente['id'], SQLITE3_INTEGER);
            $stmt_update->execute();
        } else {
            // Se for um bilhete novo, criamos uma nova linha no carrinho
            $stmt_insert = $db->prepare("INSERT INTO carrinho (id_utilizador, id_tipo_bilhete, quantidade) VALUES (:id_utilizador, :id_tipo_bilhete, :quantidade)");
            $stmt_insert->bindValue(':id_utilizador', $id_utilizador, SQLITE3_INTEGER);
            $stmt_insert->bindValue(':id_tipo_bilhete', $id_tipo_bilhete, SQLITE3_INTEGER);
            $stmt_insert->bindValue(':quantidade', $quantidade, SQLITE3_INTEGER);
            $stmt_insert->execute();
        }

        $db->close();

    } catch (Exception $e) {
        // Num cenário real poderias guardar o erro num ficheiro de log
        // echo "Erro: " . $e->getMessage();
    }
}

// 5. Redirecionar de volta à página anterior
// O HTTP_REFERER sabe de que página o utilizador veio (ex: compra.php)
$pagina_anterior = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php';

// Limpar parâmetros antigos e adicionar o ?carrinho=aberto
$url_limpa = strtok($pagina_anterior, '?'); 
header("Location: " . $url_limpa . "?carrinho=aberto");
exit();
?>