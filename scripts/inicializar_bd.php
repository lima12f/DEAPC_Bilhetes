<?php
// includes/inicializar_bd.php

// Ativar mensagens de erro para debug, como exige o guião
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // 1. Liga-se ao ficheiro (se não existir, o SQLite cria-o)
    $db = new SQLite3('../ticketzone.db');
    
    // Habilitar suporte a chaves estrangeiras no SQLite (desativado por padrão)
    $db->exec("PRAGMA foreign_keys = ON;");

    // 2. Criar Tabela de Utilizadores (com o campo de último acesso)
    $db->exec("CREATE TABLE IF NOT EXISTS utilizadores (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        email TEXT NOT NULL UNIQUE,
        password_hash TEXT NOT NULL,
        tipo TEXT NOT NULL CHECK(tipo IN ('cliente', 'admin')),
        data_registo TEXT DEFAULT CURRENT_TIMESTAMP,
        ultimo_acesso TEXT
    );");

    // 3. Criar Tabela de Categorias
    $db->exec("CREATE TABLE IF NOT EXISTS categorias (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL UNIQUE
    );");

    // 4. Criar Tabela de Eventos
    $db->exec("CREATE TABLE IF NOT EXISTS eventos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        descricao TEXT,
        data_inicio TEXT NOT NULL,
        data_fim TEXT NOT NULL,
        local TEXT NOT NULL,
        imagem TEXT,
        estado TEXT DEFAULT 'ativo' CHECK(estado IN ('ativo', 'cancelado')),
        id_categoria INTEGER,
        id_admin INTEGER,
        FOREIGN KEY (id_categoria) REFERENCES categorias(id),
        FOREIGN KEY (id_admin) REFERENCES utilizadores(id)
    );");

    // 5. Criar Tabela de Tipos de Bilhete
    $db->exec("CREATE TABLE IF NOT EXISTS tipos_bilhete (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        id_evento INTEGER NOT NULL,
        nome TEXT NOT NULL,
        preco REAL NOT NULL,
        qtd_total INTEGER NOT NULL,
        qtd_disponivel INTEGER NOT NULL,
        data_valido_inicio DATE NOT NULL,
        data_valido_fim DATE NOT NULL,
        FOREIGN KEY (id_evento) REFERENCES eventos(id)
    );");

    // 6. Criar Tabela do Carrinho
    $db->exec("CREATE TABLE IF NOT EXISTS carrinho (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        id_utilizador INTEGER,
        id_tipo_bilhete INTEGER,
        quantidade INTEGER NOT NULL,
        data_adicao TEXT DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_utilizador) REFERENCES utilizadores(id) ON DELETE CASCADE,
        FOREIGN KEY (id_tipo_bilhete) REFERENCES tipos_bilhete(id) ON DELETE CASCADE
    );");

    // 7. Criar Tabela de Compras
    $db->exec("CREATE TABLE IF NOT EXISTS compras (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        id_utilizador INTEGER,
        data_compra TEXT DEFAULT CURRENT_TIMESTAMP,
        total REAL NOT NULL,
        estado TEXT DEFAULT 'pago',
        FOREIGN KEY (id_utilizador) REFERENCES utilizadores(id)
    );");

    // 8. Criar Tabela de Itens de Compra
    $db->exec("CREATE TABLE IF NOT EXISTS itens_compra (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        id_compra INTEGER,
        id_tipo_bilhete INTEGER,
        quantidade INTEGER NOT NULL,
        preco_unitario REAL NOT NULL,
        FOREIGN KEY (id_compra) REFERENCES compras(id) ON DELETE CASCADE,
        FOREIGN KEY (id_tipo_bilhete) REFERENCES tipos_bilhete(id)
    );");

    // 9. Criar Tabela de Pagamentos
    $db->exec("CREATE TABLE IF NOT EXISTS pagamentos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        id_compra INTEGER,
        valor_total REAL NOT NULL,
        taxa_plataforma REAL NOT NULL,
        valor_evento REAL NOT NULL,
        nif TEXT,
        num_cartao TEXT,
        data_pagamento TEXT DEFAULT CURRENT_TIMESTAMP,
        estado TEXT DEFAULT 'concluido',
        FOREIGN KEY (id_compra) REFERENCES compras(id)
    );");

    echo "<h3>Base de dados e tabelas criadas com sucesso!</h3>";
    
    // Fechar a ligação para libertar o ficheiro
    unset($db);

} catch (Exception $e) {
    echo "Erro ao inicializar a base de dados: " . $e->getMessage();
}
?>