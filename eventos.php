<?php

session_start();

// Verifica se o utilizador tem sessão iniciada e é admin
if (!isset($_SESSION['id_utilizador']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Liga-se à BD
$db = new SQLite3('ticketzone.db');

// Carrega categorias para o select
$categorias_query = $db->query("SELECT id, nome FROM categorias");
$categorias = [];
while ($row = $categorias_query->fetchArray(SQLITE3_ASSOC)) {
    $categorias[] = $row;
}

// Verifica se é Edição ou Criação
$modo = 'Criar';
$evento = null;
$bilhetes = [];

if (isset($_GET['id'])) {
    $modo = 'Editar';
    
    // Carrega os dados do Evento
    $stmt = $db->prepare("SELECT * FROM eventos WHERE id = :id");
    $stmt->bindValue(':id', $_GET['id'], SQLITE3_INTEGER);
    $result = $stmt->execute();
    $evento = $result->fetchArray(SQLITE3_ASSOC);
    
    // Redireciona se o evento não existir
    if (!$evento) {
        header("Location: admin.php");
        exit();
    }

    // IMPEDE a edição de eventos que já ocorreram ou que foram cancelados
    if ($evento['estado'] === 'expirado' || $evento['estado'] === 'cancelado') {
        header("Location: admin.php?erro=estado_invalido");
        exit();
    }
    
    // Carrega os Tipos de Bilhete associados ao Evento
    $stmt_bilhetes = $db->prepare("SELECT * FROM tipos_bilhete WHERE id_evento = :id");
    $stmt_bilhetes->bindValue(':id', $_GET['id'], SQLITE3_INTEGER);
    $res_bilhetes = $stmt_bilhetes->execute();
    while ($b = $res_bilhetes->fetchArray(SQLITE3_ASSOC)) {
        $bilhetes[] = $b;
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TicketZone - <?= $modo ?> Evento</title>
    <link rel="stylesheet" href="styles/admin.css?v=<?= time() ?>">
    <link rel="stylesheet" href="styles/eventos.css">
    <script src="js/eventos.js"></script>
    <script src="js/admin.js"></script>
</head>
<body>
    <nav>
        <div class="logo-nav">
            <img src="images/logo.png" alt="Logo TicketZone">
        </div>
        <div class="botoes-nav">
            <button class="botao botao-voltar" onclick="location.href='admin.php'">Voltar ao Dashboard</button>
        </div>
    </nav>

    <main>
        <h2><?= $modo ?> Evento</h2>
        
        <form action="scripts/processar_evento.php" method="POST" id="formEvento" enctype="multipart/form-data">
            
            <?php if ($evento): ?>
                <input type="hidden" name="id_evento" value="<?= $evento['id'] ?>">
            <?php endif; ?>

            <div class="grupo-form">
                <label for="nome">Nome do Evento *</label>
                <input type="text" id="nome" name="nome" value="<?= $evento ? htmlspecialchars($evento['nome']) : '' ?>">
                <span class="erro-msg" id="erro_nome"></span>
            </div>

            <div class="grupo-form">
                <label for="id_categoria">Categoria *</label>
                <select id="id_categoria" name="id_categoria">
                    <option value="">Selecione uma categoria...</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($evento && $evento['id_categoria'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="erro-msg" id="erro_categoria"></span>
            </div>

            <div class="grupo-form duplo">
                <div>
                    <label for="data_inicio">Data e Hora de Início *</label>
                    <input type="datetime-local" id="data_inicio" name="data_inicio" value="<?= $evento ? htmlspecialchars($evento['data_inicio']) : '' ?>">
                    <span class="erro-msg" id="erro_data_inicio"></span>
                </div>
                <div>
                    <label for="data_fim">Data e Hora de Fim *</label>
                    <input type="datetime-local" id="data_fim" name="data_fim" value="<?= $evento ? htmlspecialchars($evento['data_fim']) : '' ?>">
                    <span class="erro-msg" id="erro_data_fim"></span>
                </div>
            </div>

            <div class="grupo-form">
                <label for="local">Localização *</label>
                <input type="text" id="local" name="local" value="<?= $evento ? htmlspecialchars($evento['local']) : '' ?>">
                <span class="erro-msg" id="erro_local"></span>
            </div>

            <div class="grupo-form">
                <label for="descricao">Descrição *</label>
                <textarea id="descricao" name="descricao" rows="4"><?= $evento ? htmlspecialchars($evento['descricao']) : '' ?></textarea>
                <span class="erro-msg" id="erro_descricao"></span>
            </div>

            <div class="grupo-form">
                <label for="imagem">Capa do Evento <?= $evento ? '(Deixar vazio para manter a atual)' : '*' ?></label>
                <input type="file" id="imagem" name="imagem" accept="image/*">
                <span class="erro-msg" id="erro_imagem"></span>
                <?php if ($evento && $evento['imagem']): ?>
                    <p class="img-atual">Imagem atual: <img src="<?= htmlspecialchars($evento['imagem']) ?>" width="100"></p>
                <?php endif; ?>
            </div>

            <hr class="divisor-linha">
            <h3 style="margin-bottom: 20px;">Tipos de Bilhete</h3>

            <div class="cabecalho-bilhetes">
                <div>Nome do Bilhete</div>
                <div>Preço (€)</div>
                <div>Qtd Total</div>
                <div>Válido Desde</div>
                <div>Válido Até</div>
                <div></div>
            </div>

            <div id="container-bilhetes">
                <?php if (empty($bilhetes)): ?>
                    <div class="linha-bilhete">
                        <input type="hidden" name="bilhete_id[]" value="0">
                        <input type="text" name="bilhete_nome[]" placeholder="Ex: VIP" class="input-bilhete">
                        <input type="number" step="0.01" min="0" name="bilhete_preco[]" placeholder="Preço" class="input-bilhete">
                        <input type="number" min="1" name="bilhete_qtd[]" placeholder="Qtd" class="input-bilhete">
                        <input type="date" name="bilhete_d_ini[]" title="Válido a partir de" class="input-bilhete">
                        <input type="date" name="bilhete_d_fim[]" title="Válido até" class="input-bilhete">
                        <button type="button" class="btn-remover-bilhete" onclick="this.parentElement.remove()">X</button>
                    </div>
                <?php else: ?>
                    <?php foreach ($bilhetes as $b): ?>
                        <div class="linha-bilhete">
                            <input type="hidden" name="bilhete_id[]" value="<?= $b['id'] ?>">
                            <input type="text" name="bilhete_nome[]" value="<?= htmlspecialchars($b['nome']) ?>" placeholder="Ex: VIP" class="input-bilhete">
                            <input type="number" step="0.01" min="0" name="bilhete_preco[]" value="<?= $b['preco'] ?>" placeholder="Preço" class="input-bilhete">
                            <input type="number" min="1" name="bilhete_qtd[]" value="<?= $b['qtd_total'] ?>" placeholder="Qtd" class="input-bilhete">
                            <input type="date" name="bilhete_d_ini[]" value="<?= $b['data_valido_inicio'] ?>" class="input-bilhete">
                            <input type="date" name="bilhete_d_fim[]" value="<?= $b['data_valido_fim'] ?>" class="input-bilhete">
                            <button type="button" class="btn-remover-bilhete" onclick="this.parentElement.remove()">X</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <button type="button" class="botao" id="btn-add-bilhete" style="background-color: #6c757d; border: none; color: white; margin-top: 10px;">Adicionar Bilhete</button>
            <span class="erro-msg" id="erro_bilhetes" style="display:block; margin-top:10px;"></span>

            <div class="acoes-form">
                <button type="submit" class="botao botao-criar">Guardar Evento</button>
            </div>
        </form>
    </main>

</body>
</html>