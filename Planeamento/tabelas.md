## 1. Processamento e Armazenamento no Servidor (Alínea 3a e 3b)

| User Story | Ação / Formulário | Processamento no Servidor | Dados Armazenados/Lidos |
| :--- | :--- | :--- | :--- |
| **CLI01/CLI02** | Registo e Autenticação | Validação de formulários e encriptação de password | Escrita/Leitura na tabela `utilizadores` |
| **CLI02** | Pesquisa de Eventos | Processamento do termo de pesquisa (GET) | Leitura nas tabelas `eventos`, `categorias`, `tipos_bilhete` |
| **CLI01** | Adicionar ao Carrinho | Validação de stock e associação ao ID do utilizador (POST) | Escrita/Atualização na tabela `carrinho` |
| **CLI01** | Finalizar Compra | Cálculo de totais e esvaziamento do carrinho (POST) | Escrita nas tabelas `compras` e `itens_compra`; Atualização em `tipos_bilhete` |
| **CLI03** | Consulta de Histórico | Pesquisa das compras efetuadas pelo utilizador logado | Leitura nas tabelas `compras`, `itens_compra`, `eventos` |
| **ADM01** | Criar/Editar Evento | Validação de dados do evento e upload de imagem (POST) | Escrita na tabela `eventos` e `tipos_bilhete` |

## 2. Estrutura da Base de Dados (Alínea 3b)

As tabelas seguintes definem a estrutura da base de dados da aplicação onde serão guardados os dados necessários ao seu funcionamento.

### Tabela: utilizadores
| Campo | Tipo | Descrição |
| :--- | :--- | :--- |
| `id` | INTEGER (PK) | Identificador único do utilizador |
| `username` | TEXT | Nome de utilizador |
| `email` | TEXT | Endereço de email único |
| `password_hash` | TEXT | Password encriptada |
| `tipo` | TEXT | Perfil do utilizador ('cliente' ou 'admin') |
| `data_registo` | DATETIME | Data e hora de criação da conta |
| `ultimo_acesso` | DATETIME | Registo do último acesso efetuado |

### Tabela: categorias
| Campo | Tipo | Descrição |
| :--- | :--- | :--- |
| `id` | INTEGER (PK) | Identificador único da categoria |
| `nome` | TEXT | Nome da categoria (ex: Música, Desporto) |

### Tabela: eventos
| Campo | Tipo | Descrição |
| :--- | :--- | :--- |
| `id` | INTEGER (PK) | Identificador único do evento |
| `nome` | TEXT | Título do evento |
| `descricao` | TEXT | Descrição longa do evento |
| `data_inicio` | TEXT | Data de início |
| `data_fim` | TEXT | Data de fecho |
| `local` | TEXT | Localização do evento |
| `imagem` | TEXT | Caminho/URL para a imagem do evento |
| `estado` | TEXT | Estado (ex: ativo, cancelado) |
| `id_categoria` | INTEGER (FK) | Chave estrangeira para `categorias` |
| `id_admin` | INTEGER (FK) | Administrador que criou o evento |

### Tabela: tipos_bilhete
| Campo | Tipo | Descrição |
| :--- | :--- | :--- |