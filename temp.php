<?php
// limpar_eventos.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // Ligar à base de dados na raiz
    $db = new PDO('sqlite:ticketzone.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Iniciar uma transação (garante que ou apaga tudo, ou não apaga nada se der erro)
    $db->beginTransaction();

    // 1. Apagar dependências na ordem correta
    $db->exec("DELETE FROM itens_compra");
    $db->exec("DELETE FROM carrinho");
    $db->exec("DELETE FROM tipos_bilhete");
    
    // 2. Apagar os eventos
    $db->exec("DELETE FROM eventos");

    // 3. (Opcional) Resetar o Auto-Increment para que o próximo evento volte a ser o ID 1
    $db->exec("DELETE FROM sqlite_sequence WHERE name IN ('itens_compra', 'carrinho', 'tipos_bilhete', 'eventos')");

    // Confirmar as alterações
    $db->commit();

    echo "<div style='font-family: Arial; text-align: center; margin-top: 50px;'>";
    echo "<h2 style='color: #5cb85c;'>✅ Limpeza concluída com sucesso!</h2>";
    echo "<p>Todos os eventos, bilhetes e itens de carrinho foram removidos.</p>";
    echo "<a href='index.php' style='display: inline-block; padding: 10px 20px; background: #1a1a5e; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px;'>Voltar ao Início</a>";
    echo "</div>";

} catch (PDOException $e) {
    // Se algo falhar, reverte as alterações
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo "<h3 style='color: red;'>❌ Erro ao limpar eventos:</h3> " . $e->getMessage();
}
?>