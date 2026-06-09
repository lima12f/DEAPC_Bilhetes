<!DOCTYPE html>
<html lang="pt">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TicketZone - Registo</title>
    <link rel="stylesheet" href="styles/login.css" />

    <script>
      function verificarErroURL() {
        let params = new URLSearchParams(window.location.search);
        let erro   = params.get('erro');

        let mensagens = {
          'campos_vazios'  : 'Preencha todos os campos.',
          'email_invalido' : 'Email inválido.',
          'email_existe'   : 'Este email já está registado.',
          'senha_curta'    : 'A palavra-passe deve ter pelo menos 6 caracteres.',
          'erro_bd'        : 'Erro ao criar conta. Tente novamente.'
        };

        if (erro && mensagens[erro]) {
          alert(mensagens[erro]);
          window.history.replaceState({}, document.title, window.location.pathname);
        }
      }

      function validarRegisto(e) {
      let usernameInput = document.getElementById('username');
      let emailInput    = document.getElementById('email');
      let passwordInput = document.getElementById('password');
      let username      = usernameInput.value.trim();
      let email         = emailInput.value.trim();
      let password      = passwordInput.value.trim();
      let erros         = [];

      usernameInput.style.border = '';
      emailInput.style.border    = '';
      passwordInput.style.border = '';

      if (!username) {
        usernameInput.style.border = '2px solid red';
        erros.push('Preencha o campo de nome.');
      }

      if (!email) {
        emailInput.style.border = '2px solid red';
        erros.push('Preencha o campo de email.');
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        emailInput.style.border = '2px solid red';
        erros.push('Email inválido.');
      }

      if (!password) {
        passwordInput.style.border = '2px solid red';
        erros.push('Preencha o campo da palavra-passe.');
      } else if (password.length < 6) {
        passwordInput.style.border = '2px solid red';
        erros.push('A palavra-passe deve ter pelo menos 6 caracteres.');
      }

      if (erros.length > 0) {
        e.preventDefault();
        alert(erros.join('\n'));
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
      <h1>Registe-se!</h1>

      <div class="caixa">
        <form method="POST" action="scripts/novo_registo.php" id="form-registo">

          <label for="username">Nome</label>
          <input type="text" id="username" name="username" placeholder="O teu nome"
                 oninput="limparErro('username')" />

          <label for="email">Email</label>
          <input type="text" id="email" name="email" placeholder="O teu email"
                 oninput="limparErro('email')" />

          <label for="password">Palavra-passe</label>
          <input type="password" id="password" name="password" placeholder="A tua palavra-passe"
                 oninput="limparErro('password')" />

          <button type="submit">Registar</button>

        </form>

        <p>Já tem conta? <a href="login.php">Inicie sessão!</a></p>
      </div>
    </main>

    <script>
      document.getElementById('form-registo').addEventListener('submit', validarRegisto);
    </script>

  </body>
</html>