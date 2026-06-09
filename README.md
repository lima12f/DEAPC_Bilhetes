# TicketZone - Plataforma de venda de bilhetes
## 1-Definição do grupo e do Projeto (Guião W0):
**a) Identificação dos elementos do grupo:**

* António Correia – nº 1250767
* Diogo Menezes – nº 1250894
* David Barbosa – nº 1250843

**b) Identificação do tema do trabalho**
* O tema escolhido foi o "P18- Venda de bilhetes"

**c) Link de acesso ao repositório**
* https://github.com/lima12f/DEAPC_Bilhetes

## 2-Definição de objetivos e identificação de requisitos:

**1) Objetivos da aplicação**

* Facilitar o Acesso: Permitir que utilizadores encontrem e comprem bilhetes a qualquer hora e em qualquer lugar.

* Gestão de Inventário: Garantir que o servidor de base de dados controle a disponibilidade (evitando overbooking).

* Autonomia Administrativa: Oferecer ferramentas para que gestores criem e editem eventos sem necessidade de intervenção técnica direta na base de dados.

**b) Atores e perfis de utilizador**

* Cliente: Utilizador final que navega no catálogo, efetua compras e gere o seu perfil pessoal.

* Administrador: Utilizador com privilégios elevados responsável pela gestão de conteúdos (eventos), supervisão de vendas e manutenção da integridade dos dados.

**c) User Stories**

* CLI01: João:
O João pretende comprar bilhetes para um evento em específico. Para isso, este cria conta na nossa plataforma para realizar a compra dos mesmos.

* CLI02: Maria:
A Maria pretende procurar eventos, por isso, cria conta na nossa plataforma para procurar eventos perto dela através dos filtros de localização e data.
* CLI3: Rubén: 
O Rubén pretende visualizar os eventos que comprou e, por isso, dá login na plataforma e acede a sua área de utilizador, onde poder ver os bilhetes e as faturas.

* Adm1: Manuel:  
O Manuel pretende criar novos eventos e editar o existentes. Este necessita também de cancelar na plataforma eventos para evitar compras de eventos já cancelados.

* Adm2: Joana:
A Joana em quanto administradora pretende visualizar a quantidade de bilhetes vendidos, receita e lotação.

### Tabela

#### Clientes

| Código | Nome | Descrição | Prioridade |
| :--- | :--- | :--- | :--- |
| CLI01 | Compra de Bilhetes | Como Cliente, após login, pretendo selecionar um evento e comprar bilhetes . | Alta |
| CLI02 | Pesquisa de Eventos | Como Cliente, pretendo filtrar eventos por categoria ou localização, sendo necessário criar conta para prosseguir com a aquisição do mesmo | Alta |
| CLI03 | Histórico de Compras | Como Cliente, pretendo aceder a uma área pessoal para consultar os bilhetes que já adquirio. | Média |

#### Administradores

| Código | Nome | Descrição | Prioridade |
| :--- | :--- | :--- | :--- |
| ADM01 | Gestão de Eventos | Como Administrador, pretendo criar, editar ou cancelar eventos para manter o catálogo da plataforma atualizado. | Alta |
| ADM02 | Relatórios de Vendas | Como Administrador, pretendo visualizar a quantidade de bilhetes vendidos por evento para gerir a lotação e receitas. | Média |

## 3 - Especificação
**1) Arquitetura de páginas**

**a) Landing Page da Plataforma** A página de entrada da aplicação é a Homepage (Página Inicial). É nesta página que todos os utilizadores (autenticados ou não) aterram, permitindo a visualização imediata da oferta de eventos. Para realizar as restantes funcionalidades (comprar ou consultar as informações da conta) será necessário iniciar sessão.

**b) Estrutura da Plataforma**

Prevemos utilizar pelo menos 6 páginas:

* Homepage (Página Inicial):
Função: Montra de eventos com barra de pesquisa integrada.
Conteúdo: Grelha de eventos e filtros de pesquisa.

* Autenticação (Login & Registo):
Função: Página única que contém os formulários de entrada no sistema e de criação de nova conta.

* Detalhes do Evento & Compra:
Função: Exibe informações detalhadas de um evento específico e o formulário de seleção de bilhetes/pagamento.

* Área Pessoal (cliente):
Função: Uma página que lista os bilhetes comprados e histórico.  

* Dashboard (Admin)
Permite criar/editar e cancelar eventos. Permite também ao administrador ter uma visão mais abrangente da plataforma.