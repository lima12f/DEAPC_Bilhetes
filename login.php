<!DOCTYPE html>
<html lang="pt">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TicketZone - Login</title>
    <link rel="stylesheet" href="styles/login.css" />

    <script>
      function verificarErroURL() {
      let params = new URLSearchParams(window.location.search);
      let erro   = params.get('erro');

      let mensagens = {
        'campos_vazios' : 'Preencha todos os campos.',
        'credenciais'   : 'Email ou palavra-passe incorretos.',
        'erro_bd'       : 'Erro ao aceder à base de dados. Tente novamente.'
      };

      if (erro && mensagens[erro]) {
        alert(mensagens[erro]);
        window.history.replaceState({}, document.title, window.location.pathname);
      }
    }

      function validarLogin(e) {
        let emailInput    = document.getElementById('email');
        let passwordInput = document.getElementById('password');
        let email         = emailInput.value.trim();
        let password      = passwordInput.value.trim();
        let erro          = '';

        emailInput.style.border    = '';
        passwordInput.style.border = '';

        if (!email && !password) {
          emailInput.style.border    = '2px solid red';
          passwordInput.style.border = '2px solid red';
          erro = 'Preencha todos os campos.';
        } else if (!email) {
          emailInput.style.border = '2px solid red';
          erro = 'Preencha o campo de email.';
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
          emailInput.style.border = '2px solid red';
          erro = 'Email inválido.';
        } else if (!password) {
          passwordInput.style.border = '2px solid red';
          erro = 'Preencha o campo da palavra-passe.';
        } else if (password.length < 6) {
          passwordInput.style.border = '2px solid red';
          erro = 'A palavra-passe deve ter pelo menos 6 caracteres.';
        }

        if (erro) {
          e.preventDefault();
          alert(erro);
        }
      }

      function limparErro(campo) {
        document.getElementById(campo).style.border = '';
      }
    </script>

  </head>
  <body class="pagina" onload="verificarErroURL()">

    <div class="logo-topo">
      <img src="images/logoinicio.png" alt="TicketZone" />
    </div>

    <main>
      <h1>Bem-vindo de volta!</h1>

      <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] === 'registo'): ?>
        <p class="sucesso">Conta criada com sucesso! Faça login.</p>
      <?php endif; ?>

      <div class="caixa">
        <form method="POST" action="scripts/login.php" id="form-login">

          <label for="email">Email</label>
          <input type="text" id="email" name="email" placeholder="O teu email"
                 oninput="limparErro('email')" />

          <label for="password">Palavra-passe</label>
          <input type="password" id="password" name="password" placeholder="A tua palavra-passe"
                 oninput="limparErro('password')" />

          <button type="submit">Entrar</button>

        </form>

        <p>Ainda não tem conta? <a href="registo.php">Registe-se!</a></p>
      </div>
    </main>

    <script>
      document.getElementById('form-login').addEventListener('submit', validarLogin);
    </script>

  </body>
</html>