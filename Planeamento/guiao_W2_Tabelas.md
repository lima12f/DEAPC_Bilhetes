## 3. Scripts PHP Necessários (Alínea 4)
 
| Nome | Descrição | User Story |
| :--- | :--- | :--- |
| `novoregisto.php (david)` | Recebe os dados do formulário de registo, verifica se o username/email já existe e, caso não exista, cria o novo utilizador na base de dados | CLI01, CLI02 |
| `login.php (david)` | Valida as credenciais (username + password) via POST, inicia sessão e redireciona o utilizador para a página adequada conforme o seu perfil (cliente ou admin) | CLI01, CLI02, CLI03, ADM01, ADM02 |
| `logout.php (david)` | Termina a sessão do utilizador autenticado e redireciona para a homepage | Todos |
| `carregar_eventos_homepage.php(diogo)` | Recebe os parâmetros de pesquisa (texto, categoria, localização, data) e devolve os eventos correspondentes da base de dados | CLI02 |
| `detalhes_evento.php` | Recebe o ID do evento e devolve a informação detalhada do mesmo, incluindo os tipos de bilhete disponíveis e as respetivas quantidades e preços | CLI01 |
| `carrinho_adicionar.php` | Recebe o ID do tipo de bilhete e a quantidade e adiciona o item ao carrinho do utilizador autenticado, verificando disponibilidade | CLI01 |
| `carrinho_ver.php (David)` | Devolve os itens presentes no carrinho do utilizador autenticado, com subtotais e total | CLI01 |
| `carrinho_remover.php` | Remove um item específico do carrinho do utilizador autenticado | CLI01 |
| `confirmar_compra.php` | Processa o carrinho do utilizador: cria o registo de compra, atualiza as quantidades disponíveis dos tipos de bilhete e limpa o carrinho | CLI01 |
| `historico_compras.php (António)` | Devolve a lista de compras efetuadas pelo utilizador autenticado, com os respetivos bilhetes e detalhes | CLI03 |
| `criar_evento.php (António)` | Recebe os dados de um novo evento (incluindo tipos de bilhete) via POST e insere-os na base de dados | ADM01 |
| `editar_evento.php (António)` | Recebe os dados atualizados de um evento existente e aplica as alterações na base de dados | ADM01 |
| `cancelar_evento.php (António)` | Altera o estado de um evento para `cancelado`, impedindo novas compras | ADM01 |
| `relatorio_vendas.php (António)` | Devolve para um dado evento o número de bilhetes vendidos por tipo, a receita total e a percentagem de ocupação | ADM02 |
 