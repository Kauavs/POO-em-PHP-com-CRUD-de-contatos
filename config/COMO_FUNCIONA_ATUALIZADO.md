# Como o sistema funciona

Este documento explica o funcionamento da versão atual da agenda telefônica, desenvolvida em PHP com MySQL e organizada com Programação Orientada a Objetos (POO).

## Visão geral

O sistema permite cadastrar, listar, editar e excluir contatos. Cada contato possui `id`, `nome`, `telefone`, `email` e `data_cadastro`.

A aplicação está organizada em partes com responsabilidades diferentes:

- **Configuração do banco:** `config/database.php`
- **Modelo/entidade:** `models/contato.php`
- **Contrato do repositório:** `models/contatoRepositoryInterface.php`
- **Repositório:** `models/contatoRepository.php`
- **Controlador:** `controller/contatoController.php`
- **Ponto de entrada:** `index.php`
- **Telas:** `views/listar.php` e `views/form.php`
- **Estilos:** `css/estilo.css`

Diferentemente da versão estruturada, as operações não ficam distribuídas em arquivos independentes como `create.php`, `update.php` e `delete.php`. Agora, o `index.php` recebe a ação solicitada e encaminha o processamento para o Controller.

## 1. Conexão com o banco de dados

O arquivo `config/database.php` contém a classe `Conexaobanco`, responsável por criar e fornecer a conexão com o MySQL.

As configurações utilizadas são:

- Host: `localhost`
- Usuário: `root`
- Senha: vazia
- Banco: `agenda`
- Porta: `3308`

O método `getConnection()` verifica se a conexão já foi criada. Caso ainda não exista, abre a conexão com `mysqli`, define o charset como `utf8` e guarda a conexão no atributo `$conn`. Nas próximas chamadas, reutiliza a conexão existente.

## 2. Classe `Contato`

O arquivo `models/contato.php` contém a classe `Contato`, que representa um contato como um objeto.

A classe possui os atributos privados:

- `id`
- `nome`
- `telefone`
- `email`
- `dataCadastro`

Os atributos são privados para que sejam acessados e modificados por meio dos métodos públicos, chamados getters e setters.

### Validações

- `setNome()` remove espaços no início e no final e não permite um nome vazio.
- `setTelefone()` remove espaços nas bordas, permite números, espaços, parênteses e hífen e exige de 10 a 11 dígitos, considerando o DDD.
- `setEmail()` remove espaços e valida o formato do e-mail quando o campo não está vazio. Portanto, o e-mail é opcional.
- `setId()` e `setDataCadastro()` atribuem os valores correspondentes.
- `validar()` verifica se nome e telefone estão preenchidos.

Quando uma informação não atende às regras, os setters lançam uma `InvalidArgumentException`. O Controller captura essa exceção e apresenta a mensagem no formulário.

## 3. Interface `ContatoRepositoryInterface`

O arquivo `models/contatoRepositoryInterface.php` define um contrato com as operações que um repositório de contatos deve oferecer:

- `listar(): array`
- `buscarPorId(int $id): ?Contato`
- `inserir(Contato $contato): bool`
- `atualizar(Contato $contato): bool`
- `excluir(int $id): bool`

A interface define quais métodos devem existir, mas não determina como eles serão executados. A classe `ContatoRepository` implementa esse contrato.

O Controller recebe a interface como dependência, em vez de depender diretamente dos detalhes da implementação do repositório. Isso ajuda a reduzir o acoplamento entre as partes do sistema.

## 4. Classe `ContatoRepository`

O arquivo `models/contatoRepository.php` contém a classe `ContatoRepository`, responsável por acessar a tabela `contatos` no banco de dados.

No construtor, ela recebe um objeto `Conexaobanco` e obtém a conexão por meio de `getConnection()`.

### Métodos do repositório

| Método | Responsabilidade |
|---|---|
| `listar()` | Busca todos os contatos, ordenados pelo nome, e retorna objetos `Contato`. |
| `buscarPorId()` | Busca um contato pelo ID. Retorna `null` quando não encontra. |
| `inserir()` | Insere um novo contato no banco. |
| `atualizar()` | Atualiza nome, telefone e e-mail de um contato existente. |
| `excluir()` | Exclui um contato pelo ID. |

As consultas que recebem valores utilizam *prepared statements*, com `prepare()` e `bind_param()`, em vez de colocar os dados diretamente no comando SQL.

### Conversão dos dados em objetos

O método privado `criarContato(array $row)` recebe uma linha retornada pelo banco e cria um objeto `Contato`, preenchendo seus atributos por meio dos setters.

Assim, as outras partes da aplicação trabalham com objetos `Contato`, e não precisam manipular diretamente os arrays retornados pelo MySQL.

## 5. Classe `ContatoController`

O arquivo `controller/contatoController.php` contém a classe `ContatoController`, responsável por coordenar as operações da aplicação.

No construtor, recebe um objeto que implementa `ContatoRepositoryInterface` e guarda essa dependência no atributo `$repo`.

### Métodos do Controller

- `index()`: solicita ao repositório a lista de contatos e carrega a tela `views/listar.php`.
- `novo()`: prepara os campos vazios e carrega o formulário para um novo contato.
- `criar()`: recebe os dados enviados por POST, cria um objeto `Contato`, executa as validações e, se não houver erros, solicita ao repositório que insira o contato.
- `editar(int $id)`: busca o contato pelo ID e prepara os dados para preencher o formulário de edição.
- `atualizar()`: recebe os dados do formulário, busca o contato existente, atualiza seus atributos, valida as informações e solicita ao repositório que salve as alterações.
- `excluir()`: aceita somente requisições POST, recebe o ID e solicita ao repositório a exclusão do contato.
- `mostrarFormulario()`: método privado que prepara e carrega a tela do formulário.

Quando as validações falham durante o cadastro ou a atualização, o Controller guarda as mensagens de erro e mostra novamente o formulário com os dados digitados.

## 6. Ponto de entrada: `index.php`

O arquivo `index.php` é o ponto de entrada da aplicação.

Primeiro, ele carrega os arquivos necessários com `require_once`, respeitando a ordem das dependências:

1. `config/database.php`
2. `models/Contato.php`
3. `models/ContatoRepositoryInterface.php`
4. `models/ContatoRepository.php`
5. `controller/ContatoController.php`

Depois, cria os objetos de conexão, repositório e controlador.

A ação é identificada pelo parâmetro `acao` da URL. Um `switch` encaminha a solicitação para o método correspondente do Controller:

| URL/ação | Método chamado |
|---|---|
| `index.php` | `index()` |
| `index.php?acao=novo` | `novo()` |
| `index.php?acao=criar` | `criar()` |
| `index.php?acao=editar&id=1` | `editar()` |
| `index.php?acao=atualizar` | `atualizar()` |
| `index.php?acao=excluir` | `excluir()` |

O arquivo também possui um `try/catch` para capturar exceções `mysqli_sql_exception` durante o acesso ao banco.

## 7. Telas (Views)

As telas ficam na pasta `views`. Elas são responsáveis por apresentar as informações ao usuário.

### `views/listar.php`

Recebe do Controller a variável `$contatos`, que contém objetos `Contato`.

Quando existem contatos, apresenta uma tabela com ID, nome, telefone, e-mail, data de cadastro e ações. Os valores de texto são tratados com `htmlspecialchars()` antes de serem exibidos.

A tela oferece:

- Um link para cadastrar um novo contato, usando `index.php?acao=novo`.
- Um link para editar um contato, usando `index.php?acao=editar&id=...`.
- Um formulário POST para excluir um contato, usando `index.php?acao=excluir`.

A exclusão pede confirmação no navegador antes de enviar o formulário. Se não houver contatos, a tela informa que nenhum contato foi encontrado.

### `views/form.php`

Recebe do Controller as variáveis `$dados`, `$erros`, `$titulo`, `$acao` e `$botao`.

A mesma tela é utilizada tanto para cadastrar quanto para editar contatos. O Controller define o título, a ação do formulário e o texto do botão.

O formulário envia os dados pelo método POST para `index.php?acao=...`. Os valores digitados e as mensagens de erro são exibidos com `htmlspecialchars()`.

## 8. Fluxos das operações

### Listar contatos

1. O usuário acessa `index.php`.
2. O `index.php` chama `ContatoController::index()`.
3. O Controller solicita os contatos ao método `listar()` do repositório.
4. O Repository consulta o banco e transforma cada linha em um objeto `Contato`.
5. O Controller carrega `views/listar.php`, que apresenta os contatos.

**Fluxo resumido:**

`index.php` → Controller → Repository → MySQL → objetos `Contato` → View.

### Cadastrar contato

1. O usuário acessa `index.php?acao=novo`.
2. O Controller carrega o formulário vazio.
3. O usuário preenche os campos e envia o formulário.
4. Os dados são enviados por POST para `index.php?acao=criar`.
5. O Controller cria um objeto `Contato` e chama seus setters.
6. Os setters validam nome, telefone e e-mail.
7. Se não houver erros, o Controller chama `inserir()` no Repository.
8. O Repository executa o INSERT no banco e o usuário é redirecionado para a listagem.
9. Se houver erros, o formulário é exibido novamente com os dados e as mensagens.

### Editar e atualizar contato

1. O usuário clica em Editar na listagem.
2. O navegador acessa `index.php?acao=editar&id=...`.
3. O Controller solicita ao Repository o contato correspondente ao ID.
4. O contato é apresentado no formulário preenchido.
5. Ao enviar as alterações, o formulário faz uma requisição POST para `index.php?acao=atualizar`.
6. O Controller busca o contato existente, aplica os novos valores e executa as validações.
7. Se estiver tudo correto, chama `atualizar()` no Repository.
8. O Repository executa o UPDATE e o usuário retorna à listagem. Se houver erros, o formulário é exibido novamente.

### Excluir contato

1. O usuário clica em Excluir e confirma a ação.
2. A tela envia o ID por um formulário POST para `index.php?acao=excluir`.
3. O Controller verifica se a requisição utiliza POST.
4. O Controller chama `excluir()` no Repository.
5. O Repository executa o DELETE no banco.
6. O usuário é redirecionado para a listagem.

## 9. Por que a estrutura com POO foi utilizada?

Na versão estruturada, cada arquivo de operação concentrava o recebimento dos dados, as validações e as consultas ao banco.

Na versão orientada a objetos, essas responsabilidades foram distribuídas:

- `Contato` representa o contato e protege seus dados por meio de atributos privados e setters.
- `ContatoRepository` concentra as consultas e operações no banco.
- `ContatoRepositoryInterface` define o contrato das operações de dados.
- `ContatoController` coordena o fluxo das ações.
- As Views cuidam da apresentação das informações.
- `index.php` centraliza a entrada e o direcionamento das ações.

Essa organização facilita a manutenção, evita a repetição de responsabilidades e deixa o projeto mais preparado para receber alterações e novas funcionalidades.

A mudança não alterou o objetivo da agenda: o sistema continua realizando o CRUD de contatos. O que mudou foi a maneira como o código foi dividido e organizado.
