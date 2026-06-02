<?php

session_start();

if (!isset($_SESSION['id_utilizador']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new SQLite3('../ticketzone.db');
    
    // Obter dados do evento
    $id_evento = isset($_POST['id_evento']) ? (int)$_POST['id_evento'] : null;
    $nome = trim($_POST['nome']);
    $id_categoria = (int)$_POST['id_categoria'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
    $local = trim($_POST['local']);
    $descricao = trim($_POST['descricao']);
    $id_admin = $_SESSION['id_utilizador'];
    $estado = isset($_POST['estado']) ? $_POST['estado'] : 'ativo'; 

    // Upload de Imagem
    $caminho_imagem = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $novo_nome = uniqid('evento_') . '.' . $ext;
        $destino_relativo = 'images/' . $novo_nome; 
        $destino_absoluto = '../' . $destino_relativo;
        
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino_absoluto)) {
            $caminho_imagem = $destino_relativo;
        }
    }

    // --- 1. PROCESSAR O EVENTO ---
    if ($id_evento) {
        // MODO: EDITAR
        if ($caminho_imagem) {
            $stmt = $db->prepare("UPDATE eventos SET nome = :nome, descricao = :desc, data_inicio = :d_ini, data_fim = :d_fim, local = :local, imagem = :img, estado = :estado, id_categoria = :id_cat WHERE id = :id");
            $stmt->bindValue(':img', $caminho_imagem, SQLITE3_TEXT);
        } else {
            $stmt = $db->prepare("UPDATE eventos SET nome = :nome, descricao = :desc, data_inicio = :d_ini, data_fim = :d_fim, local = :local, estado = :estado, id_categoria = :id_cat WHERE id = :id");
        }
        $stmt->bindValue(':id', $id_evento, SQLITE3_INTEGER);
        $stmt->bindValue(':estado', $estado, SQLITE3_TEXT);
        
    } else {
        // MODO: CRIAR
        $stmt = $db->prepare("INSERT INTO eventos (nome, descricao, data_inicio, data_fim, local, imagem, id_categoria, id_admin) VALUES (:nome, :desc, :d_ini, :d_fim, :local, :img, :id_cat, :id_admin)");
        $stmt->bindValue(':img', $caminho_imagem, SQLITE3_TEXT);
        $stmt->bindValue(':id_admin', $id_admin, SQLITE3_INTEGER);
    }

    $stmt->bindValue(':nome', $nome, SQLITE3_TEXT);
    $stmt->bindValue(':desc', $descricao, SQLITE3_TEXT);
    $stmt->bindValue(':d_ini', $data_inicio, SQLITE3_TEXT);
    $stmt->bindValue(':d_fim', $data_fim, SQLITE3_TEXT);
    $stmt->bindValue(':local', $local, SQLITE3_TEXT);
    $stmt->bindValue(':id_cat', $id_categoria, SQLITE3_INTEGER);
    $stmt->execute();

    // Se foi um novo evento, captura o ID gerado pelo SQLite
    if (!$id_evento) {
        $id_evento = $db->lastInsertRowID();
    }

    // --- 2. PROCESSAR OS BILHETES ---
    if (isset($_POST['bilhete_nome']) && is_array($_POST['bilhete_nome'])) {
        
        $stmt_insert_bilhete = $db->prepare("INSERT INTO tipos_bilhete (id_evento, nome, preco, qtd_total, qtd_disponivel, data_valido_inicio, data_valido_fim) VALUES (:id_ev, :nome, :preco, :qtd, :qtd, :d_ini, :d_fim)");
        $stmt_update_bilhete = $db->prepare("UPDATE tipos_bilhete SET nome = :nome, preco = :preco, qtd_total = :qtd, data_valido_inicio = :d_ini, data_valido_fim = :d_fim WHERE id = :id_bilhete AND id_evento = :id_ev");

        for ($i = 0; $i < count($_POST['bilhete_nome']); $i++) {
            $b_id = (int)$_POST['bilhete_id'][$i];
            $b_nome = trim($_POST['bilhete_nome'][$i]);
            $b_preco = (float)$_POST['bilhete_preco'][$i];
            $b_qtd = (int)$_POST['bilhete_qtd'][$i];
            $b_d_ini = $_POST['bilhete_d_ini'][$i];
            $b_d_fim = $_POST['bilhete_d_fim'][$i];

            if ($b_nome !== '' && $b_qtd > 0) {
                if ($b_id === 0) {
                    // É um registo novo
                    $stmt_insert_bilhete->bindValue(':id_ev', $id_evento, SQLITE3_INTEGER);
                    $stmt_insert_bilhete->bindValue(':nome', $b_nome, SQLITE3_TEXT);
                    $stmt_insert_bilhete->bindValue(':preco', $b_preco, SQLITE3_FLOAT);
                    $stmt_insert_bilhete->bindValue(':qtd', $b_qtd, SQLITE3_INTEGER);
                    $stmt_insert_bilhete->bindValue(':d_ini', $b_d_ini, SQLITE3_TEXT);
                    $stmt_insert_bilhete->bindValue(':d_fim', $b_d_fim, SQLITE3_TEXT);
                    $stmt_insert_bilhete->execute();
                } else {
                    // É uma atualização de um registo existente
                    $stmt_update_bilhete->bindValue(':id_bilhete', $b_id, SQLITE3_INTEGER);
                    $stmt_update_bilhete->bindValue(':id_ev', $id_evento, SQLITE3_INTEGER);
                    $stmt_update_bilhete->bindValue(':nome', $b_nome, SQLITE3_TEXT);
                    $stmt_update_bilhete->bindValue(':preco', $b_preco, SQLITE3_FLOAT);
                    $stmt_update_bilhete->bindValue(':qtd', $b_qtd, SQLITE3_INTEGER);
                    $stmt_update_bilhete->bindValue(':d_ini', $b_d_ini, SQLITE3_TEXT);
                    $stmt_update_bilhete->bindValue(':d_fim', $b_d_fim, SQLITE3_TEXT);
                    $stmt_update_bilhete->execute();
                }
            }
        }
    }

    // Retorna ao painel de administração com indicador de sucesso
    header("Location: ../admin.php?sucesso=1");
    exit();
}
?>