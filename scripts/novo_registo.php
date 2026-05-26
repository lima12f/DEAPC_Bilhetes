<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../registo.php");
    exit;
}

$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');

// Validacao
if (empty($username) || empty($email) || empty($password)) {
    header("Location: ../registo.php?erro=campos_vazios");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../registo.php?erro=email_invalido");
    exit;
}

if (strlen($password) < 6) {
    header("Location: ../registo.php?erro=senha_curta");
    exit;
}

// Ligacao a base de dados
$db = new SQLite3(__DIR__ . '/../ticketzone.db');

if (!$db) {
    header("Location: ../registo.php?erro=erro_bd");
    exit;
}

// Verificar se email ja existe
$stmt = $db->prepare("SELECT id FROM utilizadores WHERE email = :email");
$stmt->bindValue(':email', $email, SQLITE3_TEXT);
$resultado = $stmt->execute()->fetchArray();

if ($resultado) {
    header("Location: ../registo.php?erro=email_existe");
    exit;
}

// Inserir utilizador
$password_hash  = password_hash($password, PASSWORD_DEFAULT);
$data_registo   = date('Y-m-d H:i:s');

$stmt = $db->prepare("
    INSERT INTO utilizadores (username, email, password_hash, tipo, data_registo, ultimo_acesso)
    VALUES (:username, :email, :password_hash, 'cliente', :data_registo, :data_registo)
");
$stmt->bindValue(':username',      $username,      SQLITE3_TEXT);
$stmt->bindValue(':email',         $email,         SQLITE3_TEXT);
$stmt->bindValue(':password_hash', $password_hash, SQLITE3_TEXT);
$stmt->bindValue(':data_registo',  $data_registo,  SQLITE3_TEXT);

if (!$stmt->execute()) {
    header("Location: ../registo.php?erro=erro_bd");
    exit;
}

$db->close();

header("Location: ../login.php?sucesso=registo");
exit;