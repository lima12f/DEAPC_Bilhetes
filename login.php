<!DOCTYPE html>
<html lang="pt">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TicketZone - Login</title>
    <link rel="stylesheet" href="styles/login.css" />
  </head>
  <body class="pagina">

    <div class="logo-topo">
      <img src="images/logoinicio.png" alt="TicketZone" />
    </div>

    <main>
      <h1>Bem-vindo de volta!</h1>

      <?php if (isset($_GET['erro'])): ?>
        <p class="erro">
          <?php
            $erros = [
              'campos_vazios'    => 'Preencha todos os campos.',
              'credenciais'      => 'Email ou palavra-passe incorretos.',
              'erro_bd'          => 'Erro ao aceder à base de dados. Tente novamente.'
            ];
            echo $erros[$_GET['erro']] ?? 'Erro desconhecido.';
          ?>
        </p>
      <?php endif; ?>

      <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] === 'registo'): ?>
        <p class="sucesso">Conta criada com sucesso! Faça login.</p>
      <?php endif; ?>

      <div class="caixa">
        <form method="POST" action="scripts/login.php">

          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="O teu email" required />

          <label for="password">Palavra-passe</label>
          <input type="password" id="password" name="password" placeholder="A tua palavra-passe" required />

          <button type="submit">Entrar</button>

        </form>

        <p>Ainda não tem conta? <a href="registo.php">Registe-se!</a></p>
      </div>
    </main>

  </body>
</html>