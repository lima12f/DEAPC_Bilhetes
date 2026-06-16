<?php

session_start();

// Segurança: Apenas admins
if (!isset($_SESSION['id_utilizador']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Verifica se o ID foi passado pelo URL via GET
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_evento = (int)$_GET['id'];

    try {
        $db = new SQLite3('../ticketzone.db');
        $db->exec("PRAGMA foreign_keys = ON;");

        // Atualiza o estado de volta para ativo
        $stmt = $db->prepare("UPDATE eventos SET estado = 'ativo' WHERE id = :id");
        $stmt->bindValue(':id', $id_evento, SQLITE3_INTEGER);
        
        if ($stmt->execute()) {
            // Redireciona com sucesso
            header("Location: ../admin.php?sucesso=reativado");
        } else {
            header("Location: ../admin.php?erro=db");
        }

        $db->close();
        exit();
    } catch (Exception $e) {
        header("Location: ../admin.php?erro=db");
        exit();
    }
} else {
    // Acesso sem ID
    header("Location: ../admin.php?erro=id_invalido");
    exit();
}
?>