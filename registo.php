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

      <div class="caixa">

        <label for="name">Nome</label>
        <input type="text" id="name" name="name" placeholder="O teu nome" />

        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="O teu email" />

        <label for="password">Palavra-passe</label>
        <input type="password" id="password" name="password" placeholder="A tua palavra-passe" />

        <button type="button" onclick="window.location.href='index.html'">Registar</button>

        <p>Já tem conta? <a href="login.html">Inicie sessão!</a></p>

      </div>
    </main>

  <body class="pagina">
</html>