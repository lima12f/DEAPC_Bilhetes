<?php
// Inicia a sessão para podermos verificar se o utilizador está autenticado
session_start();

// Se não é admin, retorna para o login
if (!isset($_SESSION['id_utilizador']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/scripts/carregar_eventos.php';
require_once __DIR__ . '/scripts/carregar_entradas.php';

// Totais para a tabela de resumo
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
    <!-- Adicionado ?v=time() para forçar o navegador a limpar a cache do CSS -->
    <link rel="stylesheet" href="styles/admin.css?v=<?= time() ?>" />
</head>
<body>

    <!-- NAVBAR -->
    <nav>
        <div class="logo-nav">
            <img src="images/logo.png" alt="Logo TicketZone" />
        </div>
        <div class="botoes-nav">
            <button class="botao botao-criar" onclick="location.href='eventos.php'">Criar evento</button>
            <button class="botao botao-sair" onclick="location.href='scripts/logout.php'">Sair</button>
        </div>
    </nav>

    <!-- Principal -->
    <main>

        <div class="cabecalho-pagina">
            <h2>Painel Administrador</h2>
            <p>Gestão da plataforma: resumo financeiro, eventos ativos e acessos de utilizadores.</p>
        </div>

        <!-- NOVO FORMATO DE RESUMO (CARTÕES) -->
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
                                Nenhum evento encontrado.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($eventos as $evento): ?>
                            <tr>
                                <td class="destaque-nome"><?= htmlspecialchars($evento['nome']) ?></td>
                                
                                <!-- Aplicação do Badge de Cor -->
                                <?php $classe_badge = strtolower($evento['estado']) === 'ativo' ? 'badge-ativo' : 'badge-cancelado'; ?>
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
                                    <button class="botao-editar" onclick="location.href='eventos.php?id=<?= $evento['id'] ?>'">
                                        Editar
                                    </button>
                                    <button class="botao-cancelar" onclick="location.href='scripts/cancelar_evento.php?id=<?= $evento['id'] ?>'">
                                        Remover
                                    </button>
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
                                
                                <!-- Badge para o tipo de utilizador -->
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

    <!-- FOOTER -->
    <footer>
        <p>© 2026 TicketZone - Todos os direitos reservados.</p>
    </footer>

</body>
</html>