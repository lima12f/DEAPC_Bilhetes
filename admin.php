<?php
session_start();

if (!isset($_SESSION['id_utilizador']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Atualiza automaticamente eventos cujo prazo já expirou
require_once __DIR__ . '/scripts/atualizar_estados.php';

// Só depois de atualizados, carrega os dados para a página com os filtros aplicados
require_once __DIR__ . '/scripts/carregar_eventos.php';
require_once __DIR__ . '/scripts/carregar_entradas.php';

// Totais para a tabela de resumo baseados na pesquisa atual
$total_bilhetes_geral = 0;
$total_vendas_geral   = 0;
$total_lucro_geral    = 0;

foreach ($eventos as $evento) {
    $total_bilhetes_geral += $evento['bilhetes_vendidos'];
    $total_vendas_geral   += $evento['total_vendas'];
    $total_lucro_geral    += $evento['lucro_plataforma'];
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TicketZone - Administrador</title>
    <link rel="stylesheet" href="styles/admin.css?v=<?= time() ?>" />
    <script src="js/admin.js"> </script>
</head>
<body>

    <nav>
        <div class="logo-nav">
            <img src="images/logo.png" alt="Logo TicketZone" />
        </div>
        <div class="botoes-nav">
            <button class="botao botao-criar" onclick="location.href='eventos.php'">Criar evento</button>
            <button class="botao botao-sair" onclick="location.href='scripts/logout.php'">Sair</button>
        </div>
    </nav>

    <main>

        <div class="cabecalho-pagina">
            <h2>Painel Administrador</h2>
            <p>Gestão da plataforma: resumo financeiro, eventos ativos e acessos de utilizadores.</p>
        </div>

        <div class="resumo-cards">
            <div class="card">
                <div class="card-numero"><?= number_format($total_bilhetes_geral, 0, ',', '.') ?></div>
                <div class="card-titulo">Bilhetes Vendidos</div>
            </div>
            <div class="card">
                <div class="card-numero"><?= number_format($total_vendas_geral, 2, ',', '.') ?>€</div>
                <div class="card-titulo">Vendas Totais</div>
            </div>
            <div class="card">
                <div class="card-numero"><?= number_format($total_lucro_geral, 2, ',', '.') ?>€</div>
                <div class="card-titulo">Lucro (Taxas)</div>
            </div>
        </div>

        <h3 class="titulo-seccao">Gerir Eventos</h3>
        
        <div class="filtro-bar">
            <form method="GET" action="admin.php">
                <select name="estado">
                    <option value="todos" <?= (isset($_GET['estado']) && $_GET['estado'] == 'todos') ? 'selected' : '' ?>>Todos os Estados</option>
                    <option value="ativo" <?= (isset($_GET['estado']) && $_GET['estado'] == 'ativo') ? 'selected' : '' ?>>Ativo</option>
                    <option value="expirado" <?= (isset($_GET['estado']) && $_GET['estado'] == 'expirado') ? 'selected' : '' ?>>Expirado</option>
                    <option value="cancelado" <?= (isset($_GET['estado']) && $_GET['estado'] == 'cancelado') ? 'selected' : '' ?>>Cancelado</option>
                </select>
                
                <select name="categoria">
                    <option value="0">Todas as Categorias</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= (isset($_GET['categoria']) && $_GET['categoria'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit" class="botao botao-criar" style="padding: 8px 18px;">Filtrar</button>
                <a href="admin.php" class="botao-limpar">Remover Filtros</a>
            </form>
        </div>

        <div class="tabela-container">
            <table class="tabela-moderna">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Estado</th>
                        <th>Total Bilhetes</th>
                        <th>Restantes</th>
                        <th>Vendas</th>
                        <th>Vendas (€)</th>
                        <th>Lucro (€)</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>

                    <?php if (empty($eventos)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: #888; padding: 30px;">
                                Nenhum evento encontrado para estes filtros.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($eventos as $evento): ?>
                            <tr>
                                <td class="destaque-nome"><?= htmlspecialchars($evento['nome']) ?></td>
                                
                                <?php 
                                    $est = strtolower($evento['estado']);
                                    if ($est === 'ativo') {
                                        $classe_badge = 'badge-ativo';
                                    } elseif ($est === 'expirado') {
                                        $classe_badge = 'badge-expirado';
                                    } else {
                                        $classe_badge = 'badge-cancelado';
                                    }
                                ?>
                                <td>
                                    <span class="badge <?= $classe_badge ?>">
                                        <?= ucfirst(htmlspecialchars($evento['estado'])) ?>
                                    </span>
                                </td>
                                
                                <td><?= number_format($evento['lotacao_total'], 0, ',', '.') ?></td>
                                <td><?= number_format($evento['lotacao_total'] - $evento['bilhetes_vendidos'], 0, ',', '.') ?></td>
                                <td><?= number_format($evento['bilhetes_vendidos'], 0, ',', '.') ?></td>
                                <td><?= number_format($evento['total_vendas'], 2, ',', '.') ?>€</td>
                                <td class="destaque-lucro"><?= number_format($evento['lucro_plataforma'], 2, ',', '.') ?>€</td>

                                <td>
                                    <?php if ($est === 'ativo'): ?>
                                        <button class="botao-editar" onclick="location.href='eventos.php?id=<?= $evento['id'] ?>'">
                                            Editar
                                        </button>
                                        <button class="botao-cancelar" onclick="location.href='scripts/cancelar_evento.php?id=<?= $evento['id'] ?>'">
                                            Cancelar
                                        </button>
                                        
                                    <?php elseif ($est === 'cancelado'): ?>
                                        <button class="botao-reativar" onclick="location.href='scripts/reativar_evento.php?id=<?= $evento['id'] ?>'">
                                            Reativar
                                        </button>

                                    <?php elseif ($est === 'expirado'): ?>
                                        <span class="texto-secundario" style="font-size: 12px; font-style: italic;">Sem ações</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>

        <h3 class="titulo-seccao">Últimos Acessos</h3>

        <div class="tabela-container">
            <table class="tabela-moderna">
                <thead>
                    <tr>
                        <th>Utilizador</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th>Último Acesso</th>
                    </tr>
                </thead>
                <tbody>

                    <?php if (empty($entradas)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #888; padding: 30px;">
                                Nenhum acesso registado.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($entradas as $entrada): ?>
                            <tr>
                                <td class="destaque-nome"><?= htmlspecialchars($entrada['username']) ?></td>
                                <td><?= htmlspecialchars($entrada['email']) ?></td>
                                
                                <?php $classe_tipo = strtolower($entrada['tipo']) === 'admin' ? 'badge-admin' : 'badge-cliente'; ?>
                                <td>
                                    <span class="badge <?= $classe_tipo ?>">
                                        <?= ucfirst(htmlspecialchars($entrada['tipo'])) ?>
                                    </span>
                                </td>

                                <td class="texto-secundario">
                                    <?php
                                        $timestamp = strtotime($entrada['ultimo_acesso']);
                                        echo date('d/m/Y H:i', $timestamp);
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>

    </main>

    <footer>
        <p>© 2026 TicketZone - Todos os direitos reservados.</p>
    </footer>

</body>
</html>