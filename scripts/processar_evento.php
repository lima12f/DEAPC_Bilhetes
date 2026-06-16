<?php

session_start();

// Verifica se o utilizador tem sessão iniciada e é admin
if (!isset($_SESSION['id_utilizador']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new SQLite3('../ticketzone.db');
    
    // Ativa as foreign keys por segurança
    $db->exec("PRAGMA foreign_keys = ON;");
    
    // Obter dados do evento do formulário POST
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
            // Não atualiza a imagem se não tiver sido feito um novo upload
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

    // Bind dos parâmetros comuns a Criar e Editar
    $stmt->bindValue(':nome', $nome, SQLITE3_TEXT);
    $stmt->bindValue(':desc', $descricao, SQLITE3_TEXT);
    $stmt->bindValue(':d_ini', $data_inicio, SQLITE3_TEXT);
    $stmt->bindValue(':d_fim', $data_fim, SQLITE3_TEXT);
    $stmt->bindValue(':local', $local, SQLITE3_TEXT);
    $stmt->bindValue(':id_cat', $id_categoria, SQLITE3_INTEGER);
    $stmt->execute();

    // Se foi um novo evento, captura o ID recém-gerado pelo SQLite
    if (!$id_evento) {
        $id_evento = $db->lastInsertRowID();
    }

    // --- 2. PROCESSAR OS BILHETES ---
    if (isset($_POST['bilhete_nome']) && is_array($_POST['bilhete_nome'])) {
        
        $stmt_insert_bilhete = $db->prepare("INSERT INTO tipos_bilhete (id_evento, nome, preco, qtd_total, qtd_disponivel, data_valido_inicio, data_valido_fim) VALUES (:id_ev, :nome, :preco, :qtd, :qtd, :d_ini, :d_fim)");

        for ($i = 0; $i < count($_POST['bilhete_nome']); $i++) {
            $b_id = (int)$_POST['bilhete_id'][$i];
            $b_nome = trim($_POST['bilhete_nome'][$i]);
            
            // Segurança Backend: Impede valores negativos ou quantidades zero
            $b_preco = max(0, (float)$_POST['bilhete_preco'][$i]);
            $b_qtd = max(1, (int)$_POST['bilhete_qtd'][$i]);
            
            $b_d_ini = $_POST['bilhete_d_ini'][$i];
            $b_d_fim = $_POST['bilhete_d_fim'][$i];

            if ($b_nome !== '') {
                if ($b_id === 0) {
                    // É um registo novo
                    $stmt_insert_bilhete->bindValue(':id_ev', $id_evento, SQLITE3_INTEGER);
                    $stmt_insert_bilhete->bindValue(':nome', $b_nome, SQLITE3_TEXT);
                    $stmt_insert_bilhete->bindValue(':preco', $b_preco, SQLITE3_FLOAT);
                    // Como é novo, a quantidade disponível é igual à quantidade total
                    $stmt_insert_bilhete->bindValue(':qtd', $b_qtd, SQLITE3_INTEGER);
                    $stmt_insert_bilhete->bindValue(':d_ini', $b_d_ini, SQLITE3_TEXT);
                    $stmt_insert_bilhete->bindValue(':d_fim', $b_d_fim, SQLITE3_TEXT);
                    $stmt_insert_bilhete->execute();
                } else {
                    // É uma atualização de um registo existente
                    // 1. Obter os dados antigos para ajustar a quantidade disponível de forma correta
                    $stmt_old = $db->prepare("SELECT qtd_total, qtd_disponivel FROM tipos_bilhete WHERE id = :id");
                    $stmt_old->bindValue(':id', $b_id, SQLITE3_INTEGER);
                    $res_old = $stmt_old->execute();
                    $old_data = $res_old->fetchArray(SQLITE3_ASSOC);
                    
                    if ($old_data) {
                        // Calcula a diferença de bilhetes
                        $diferenca = $b_qtd - $old_data['qtd_total'];
                        $nova_qtd_disponivel = $old_data['qtd_disponivel'] + $diferenca;
                        
                        // Garante que não ficam bilhetes negativos caso o admin reduza muito a quantidade
                        if ($nova_qtd_disponivel < 0) {
                            $nova_qtd_disponivel = 0;
                        }

                        // Prepara o UPDATE com a nova quantidade disponível calculada
                        $stmt_update_bilhete = $db->prepare("UPDATE tipos_bilhete SET nome = :nome, preco = :preco, qtd_total = :qtd, qtd_disponivel = :qtd_disp, data_valido_inicio = :d_ini, data_valido_fim = :d_fim WHERE id = :id_bilhete AND id_evento = :id_ev");
                        
                        $stmt_update_bilhete->bindValue(':id_bilhete', $b_id, SQLITE3_INTEGER);
                        $stmt_update_bilhete->bindValue(':id_ev', $id_evento, SQLITE3_INTEGER);
                        $stmt_update_bilhete->bindValue(':nome', $b_nome, SQLITE3_TEXT);
                        $stmt_update_bilhete->bindValue(':preco', $b_preco, SQLITE3_FLOAT);
                        $stmt_update_bilhete->bindValue(':qtd', $b_qtd, SQLITE3_INTEGER);
                        $stmt_update_bilhete->bindValue(':qtd_disp', $nova_qtd_disponivel, SQLITE3_INTEGER);
                        $stmt_update_bilhete->bindValue(':d_ini', $b_d_ini, SQLITE3_TEXT);
                        $stmt_update_bilhete->bindValue(':d_fim', $b_d_fim, SQLITE3_TEXT);
                        $stmt_update_bilhete->execute();
                    }
                }
            }
        }
    }

    // Fecha a ligação
    $db->close();

    // Retorna à dashboard de administração com o indicador de sucesso
    header("Location: ../admin.php?sucesso=1");
    exit();
}
?>