<?php
// tem.php (na raiz do projeto)

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // Liga à base de dados na raiz
    $db = new SQLite3(__DIR__ . '/ticketzone.db');
    
    // Desativa as foreign keys temporariamente
    $db->exec("PRAGMA foreign_keys = OFF;");
    $db->exec("BEGIN TRANSACTION;");

    // 1. Criamos a tabela fantasma que o SQLite está à procura
    $db->exec("CREATE TABLE IF NOT EXISTS eventos_old (id INTEGER PRIMARY KEY);");

    // 2. Escondemos a nossa tabela verdadeira (eventos) temporariamente
    $db->exec("ALTER TABLE eventos RENAME TO eventos_bom;");

    // 3. Renomeamos a fantasma para o nome correto. 
    // O SQLite vai ver isto e corrigir automaticamente a tabela dos bilhetes para voltar a apontar para 'eventos'!
    $db->exec("ALTER TABLE eventos_old RENAME TO eventos;");

    // 4. Apagamos a tabela fantasma (que agora já se chama 'eventos')
    $db->exec("DROP TABLE eventos;");

    // 5. Voltamos a colocar a nossa tabela verdadeira no sítio com o nome correto
    $db->exec("ALTER TABLE eventos_bom RENAME TO eventos;");

    $db->exec("COMMIT;");
    $db->exec("PRAGMA foreign_keys = ON;");

    echo "<h2 style='color: #137333; font-family: sans-serif;'>✅ Base de Dados reparada com sucesso!</h2>";
    echo "<p style='font-family: sans-serif;'>O SQLite já redirecionou as ligações para a tabela correta. Já podes voltar a criar eventos no teu <a href='admin.php'>Painel de Administrador</a>.</p>";

    $db->close();

} catch (Exception $e) {
    if (isset($db)) {
        $db->exec("ROLLBACK;");
        $db->close();
    }
    echo "<h2 style='color: red; font-family: sans-serif;'>❌ Erro ao reparar: " . $e->getMessage() . "</h2>";
}
?>