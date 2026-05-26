
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit;
}

$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');

// Validacao
if (empty($email) || empty($password)) {
    header("Location: ../login.php?erro=campos_vazios");
    exit;
}

// Ligacao a base de dados
$db = new SQLite3(__DIR__ . '/../ticketzone.db');

if (!$db) {
    header("Location: ../login.php?erro=erro_bd");
    exit;
}

// Buscar utilizador pelo email
$stmt = $db->prepare("SELECT * FROM utilizadores WHERE email = :email");
$stmt->bindValue(':email', $email, SQLITE3_TEXT);
$utilizador = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

// Verificar password
if (!$utilizador || !password_verify($password, $utilizador['password_hash'])) {
    header("Location: ../login.php?erro=credenciais");
    exit;
}

// Atualizar ultimo acesso
$agora = date('Y-m-d H:i:s');
$stmt  = $db->prepare("UPDATE utilizadores SET ultimo_acesso = :agora WHERE id = :id");
$stmt->bindValue(':agora', $agora,                SQLITE3_TEXT);
$stmt->bindValue(':id',    $utilizador['id'],     SQLITE3_INTEGER);
$stmt->execute();

$db->close();

// Guardar sessao
$_SESSION['id']       = $utilizador['id'];
$_SESSION['username'] = $utilizador['username'];
$_SESSION['email']    = $utilizador['email'];
$_SESSION['tipo']     = $utilizador['tipo'];

header("Location: ../index.php");
exit;