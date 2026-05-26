<!DOCTYPE html>
<html lang="pt">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TicketZone - Registo</title>
    <link rel="stylesheet" href="styles/login.css" />
  </head>
  <body class="pagina">

    <div class="logo-topo">
        <img src="images/logoinicio.png" alt="TicketZone" />
    </div>

    <main>
      <h1>Registe-se!</h1>

      <?php if (isset($_GET['erro'])): ?>
        <p class="erro">
          <?php
            $erros = [
              'campos_vazios'   => 'Preencha todos os campos.',
              'email_invalido'  => 'Email inválido.',
              'email_existe'    => 'Este email já está registado.',
              'senha_curta'     => 'A palavra-passe deve ter pelo menos 6 caracteres.',
              'erro_bd'         => 'Erro ao criar conta. Tente novamente.'
            ];
            echo $erros[$_GET['erro']] ?? 'Erro desconhecido.';
          ?>
        </p>
      <?php endif; ?>

      <div class="caixa">
        <form method="POST" action="scripts/novo_registo.php">

          <label for="username">Nome</label>
          <input type="text" id="username" name="username" placeholder="O teu nome" required />

          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="O teu email" required />

          <label for="password">Palavra-passe</label>
          <input type="password" id="password" name="password" placeholder="A tua palavra-passe" required />

          <button type="submit">Registar</button>

        </form>

        <p>Já tem conta? <a href="login.php">Inicie sessão!</a></p>
      </div>
    </main>

  </body>
</html>