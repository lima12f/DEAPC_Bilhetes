<?php

$db = new SQLite3(__DIR__ . '/../ticketzone.db');

$query = "
    SELECT
        e.id,
        e.nome,
        e.estado,
        
        -- Lotação e Bilhetes Restantes
        (SELECT COALESCE(SUM(qtd_total), 0) FROM tipos_bilhete WHERE id_evento = e.id) AS lotacao_total,
        (SELECT COALESCE(SUM(qtd_disponivel), 0) FROM tipos_bilhete WHERE id_evento = e.id) AS bilhetes_restantes,
        
        -- Bilhetes Vendidos (Quantidade)
        (SELECT COALESCE(SUM(ic.quantidade), 0)
         FROM itens_compra ic
         JOIN tipos_bilhete tb ON tb.id = ic.id_tipo_bilhete
         JOIN compras c ON c.id = ic.id_compra
         WHERE tb.id_evento = e.id AND c.estado IN ('concluida', 'pago')
        ) AS bilhetes_vendidos,
        
        -- Vendas Totais (€)
        (SELECT COALESCE(SUM(ic.quantidade * ic.preco_unitario), 0)
         FROM itens_compra ic
         JOIN tipos_bilhete tb ON tb.id = ic.id_tipo_bilhete
         JOIN compras c ON c.id = ic.id_compra
         WHERE tb.id_evento = e.id AND c.estado IN ('concluida', 'pago')
        ) AS total_vendas,
        
        -- Lucro da Plataforma (buscado diretamente à base de dados na tabela pagamentos)
        (SELECT COALESCE(SUM(p.taxa_plataforma), 0)
         FROM pagamentos p
         JOIN compras c ON c.id = p.id_compra
         WHERE c.estado IN ('concluida', 'pago')
         AND c.id IN (
            SELECT ic.id_compra
            FROM itens_compra ic
            JOIN tipos_bilhete tb ON tb.id = ic.id_tipo_bilhete
            WHERE tb.id_evento = e.id
         )
        ) AS lucro_plataforma
        
    FROM eventos e
    ORDER BY e.id DESC
";

$resultado = $db->query($query);
$eventos = [];

while ($linha = $resultado->fetchArray(SQLITE3_ASSOC)) {
    $eventos[] = $linha;
}

$db->close();

?>