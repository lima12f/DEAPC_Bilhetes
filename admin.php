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
$total_ganhos_geral   = 0;

foreach ($eventos as $evento) {
    $total_bilhetes_geral += $evento['total_bilhetes'];
    $total_ganhos_geral   += $evento['total_ganhos'];
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TicketZone - Administrador</title>
    <link rel="stylesheet" href="styles/admin.css" />
</head>
<body>

    <!-- NAVBAR -->
    <nav>
        <div class="logo-nav">
            <img src="images/logo.png" alt="Logo TicketZone" />
        </div>
        <div class="botoes-nav">
            <button class="botao botao-criar">Criar evento</button>
            <button class="botao botao-perfil" onclick="location.href='perfil.php'">Perfil</button>
            <button class="botao botao-sair" onclick="location.href='scripts/logout.php'">Sair</button>
        </div>
    </nav>

    <!-- Principal -->
    <main>

        <h2>Administrador</h2>

        <div class="resumo">
            <table>
                <thead>
                    <tr>
                        <th>Total de Bilhetes Vendidos</th>
                        <th>Ganhos (€)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <!-- number_format formata o número: separador de milhar = '.' e decimais = ',' -->
                        <td><?= number_format($total_bilhetes_geral, 0, ',', '.') ?></td>
                        <td><?= number_format($total_ganhos_geral,   2, ',', '.') ?>€</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <table class="eventos">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Estado</th>
                    <th>Vendas</th>
                    <th>Ganhos (€)</th>
                    <th>Editar / Cancelar</th>
                </tr>
            </thead>
            <tbody>

                <?php if (empty($eventos)): ?>
                    <!-- Se não há eventos na base de dados, mostra uma mensagem -->
                    <tr>
                        <td colspan="5" style="text-align: center; color: #888;">
                            Nenhum evento encontrado.
                        </td>
                    </tr>

                <?php else: ?>
                    <?php foreach ($eventos as $evento): ?>
                        <tr>
                            <td><?= htmlspecialchars($evento['nome']) ?></td>
                            <td><?= htmlspecialchars($evento['estado']) ?></td>
                            <td><?= number_format($evento['total_bilhetes'], 0, ',', '.') ?></td>
                            <td><?= number_format($evento['total_ganhos'],   2, ',', '.') ?>€</td>

                            <td>
                                <button class="botao-editar"
                                        onclick="location.href='editar_evento.php?id=<?= $evento['id'] ?>'">
                                    Editar
                                </button>
                                <button class="botao-cancelar"
                                        onclick="location.href='cancelar_evento.php?id=<?= $evento['id'] ?>'">
                                    x
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>
        </table>

        <h3 style="margin-top: 50px;">Últimos Acessos à Plataforma</h3>

        <table class="eventos">
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
                        <td colspan="4" style="text-align: center; color: #888;">
                            Nenhum acesso registado.
                        </td>
                    </tr>

                <?php else: ?>
                    <?php foreach ($entradas as $entrada): ?>
                        <tr>
                            <td><?= htmlspecialchars($entrada['username']) ?></td>
                            <td><?= htmlspecialchars($entrada['email']) ?></td>

                            <td><?= ucfirst(htmlspecialchars($entrada['tipo'])) ?></td>

                            <td>
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

    </main>

    <!-- FOOTER -->
    <footer>
        <p>2026 TicketZone. Todos os direitos reservados.</p>
    </footer>

</body>
</html>