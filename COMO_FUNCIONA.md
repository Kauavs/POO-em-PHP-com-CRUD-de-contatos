# Como o sistema funciona

Este documento explica o funcionamento atual da agenda telefônica em PHP com MySQL.

## Visão geral

O sistema cadastra, lista, edita e exclui contatos. Cada contato possui `id`, `nome`, `telefone`, `email` e `data_cadastro`.

A aplicação está organizada em camadas:

- **Configuração do banco:** `config/database.php`
- **Modelo (entidade):** `contato.php`
- **Contrato do repositório:** `contatoRepositoryInterface.php`
- **Repositório (acesso a dados):** `contatoRepository.php`
- **Controlador:** `contatoController.php`
- **Ponto de entrada:** `teste.php`
- **Telas:** `listar.php` e `form.php`
- **Operações de edição, atualização e exclusão:** `edit.php`, `update.php` e `delete.php`

## 1. Conexão com o banco

A classe `Conexaobanco`, em `config/database.php`, guarda os dados de acesso: host `localhost`, usuário `root`, senha vazia, banco `agenda` e porta `3308`.

O método `getConnection()` cria a conexão `mysqli` apenas na primeira chamada. Se a conexão já existir, ele a reutiliza. Também define o charset `utf8` e encerra a execução com uma mensagem de erro caso a conexão falhe.

## 2. Entidade `Contato`

A classe `Contato` representa um contato na memória. Suas propriedades são privadas e tipadas: `id` e `data_cadastro` aceitam `null`, enquanto `nome`, `telefone` e `email` começam como strings vazias.

Getters e setters controlam o acesso a esses dados:

- `setNome()` remove espaços nas bordas e lança `InvalidArgumentException` se o nome ficar vazio.
- `setTelefone()` faz o mesmo para o telefone.
- `setEmail()` remove espaços, aceita string vazia (e-mail é opcional) e valida o formato com `filter_var(..., FILTER_VALIDATE_EMAIL)`.
- `setId()` e `setDataCadastro()` apenas atribuem os valores recebidos.
- `validar()` verifica se nome e telefone foram preenchidos.

Assim, a própria entidade protege o objeto contra dados inválidos antes de ele chegar ao banco.

## 3. Interface do repositório

`ContatoRepositoryInterface` define as operações que qualquer repositório de contatos deve oferecer:

- `listar(): array`
- `buscarPorId(int $id): ?Contato`
- `inserir(Contato $contato): bool`
- `atualizar(Contato $contato): bool`
- `excluir(int $id): bool`

Isso separa o que o sistema precisa fazer (`interface`) de como ele faz (`ContatoRepository`). O controlador depende da interface, não da implementação concreta.

## 4. Repositório `ContatoRepository`

A classe implementa a interface e trabalha com a tabela `contatos`. No construtor, recebe um `Conexaobanco` e obtém a conexão `mysqli` por meio de `getConnection()`.

### Consultas

- `listar()` executa `SELECT * FROM contatos ORDER BY nome ASC` e devolve um array de objetos `Contato`.
- `buscarPorId()` usa `SELECT ... WHERE id = ?`, com `bind_param("i", $id)`, e retorna `null` se não encontrar nada.
- `inserir()` executa `INSERT INTO contatos (nome, telefone, email) VALUES (?, ?, ?)` com `bind_param("sss", ...)`.
- `atualizar()` executa `UPDATE contatos SET nome = ?, telefone = ?, email = ? WHERE id = ?` com `bind_param("sssi", ...)`.
- `excluir()` executa `DELETE FROM contatos WHERE id = ?` com `bind_param("i", ...)`.

Todas as consultas com parâmetros usam *prepared statements*, evitando a interpolação direta de valores na SQL. Cada statement é fechado após a execução.

### Conversão de linhas em objetos

O método privado `criarContato(array $row)` converte uma linha retornada pelo banco em um objeto `Contato`: preenche `id`, `nome`, `telefone`, `email` e `data_cadastro` pelos setters. Dessa forma, o restante da aplicação trabalha apenas com objetos, não com arrays do banco.

## 5. Controlador `ContatoController`

O controlador recebe um `ContatoRepositoryInterface` no construtor e guarda essa dependência. Ele coordena a entrada do usuário, chama o repositório e escolhe qual tela mostrar.

- `index()`: chama `$repo->listar()` e inclui `listar.php`, disponibilizando a variável `$contatos` para a tela.
- `novo()`: prepara `$dados` vazios e `$erros` vazios e inclui `form.php`.
- `criar()`: lê `$_POST`, limpa os valores com `trim()` e tenta preencher um novo `Contato`. Cada setter é chamado dentro de um `try/catch` de `InvalidArgumentException`; a mensagem de erro correspondente é guardada em `$erros['nome']`, `$erros['telefone']` ou `$erros['email']`. Se não houver erros, chama `$repo->inserir($contato)` e redireciona para `index.php`. Se houver erros, mostra novamente `form.php` com os dados digitados e as mensagens.

## 6. Ponto de entrada

`teste.php` é o arquivo que monta a aplicação, na seguinte ordem:

1. `config/database.php`
2. `contato.php`
3. `contatoRepositoryInterface.php` (antes da classe que a implementa)
4. `contatoRepository.php`
5. `contatoController.php`

Depois cria `$db = new Conexaobanco()`, `$repo = new ContatoRepository($db)` e `$controller = new ContatoController($repo)`, por fim chamando `$controller->index()` para exibir a listagem.

## 7. Telas

### `listar.php`

Recebe `$contatos` do controlador. Se houver contatos, monta uma tabela com ID, nome, telefone, e-mail, data de cadastro e ações. Os dados de texto passam por `htmlspecialchars()` antes de serem exibidos; a data é formatada como `d/m/Y H:i`. A linha de ações contém links para `edit.php?id=...` e `delete.php?id=...`, com confirmação em JavaScript para a exclusão. Se a lista estiver vazia, exibe “Nenhum contato encontrado.”

### `form.php`

Recebe `$dados` e `$erros`. Gera o formulário de cadastro com os campos `nome`, `telefone` e `email`. Os valores digitados e as mensagens de erro são exibidos com `htmlspecialchars()`. O formulário envia `POST` para `index.php?acao=criar` e oferece link para cancelar.

## 8. Edição, atualização e exclusão

Essas operações usam scripts próprios e a conexão `mysqli` diretamente.

### `edit.php`

Lê o `id` enviado pela URL (`$_GET`), prepara `SELECT * FROM contatos WHERE id = ?` e busca a linha correspondente. Se encontrar, guarda `nome`, `telefone` e `email` e mostra o formulário de edição, mantendo o `id` em um campo oculto. Se o ID estiver ausente ou não encontrar registro, redireciona para `error.php`.

### `update.php`

Só processa requisições `POST`. Valida a presença do `id` e dos campos obrigatórios (`nome` e `telefone`), prepara o `UPDATE` com `bind_param("sssi", ...)` e redireciona para `index.php` quando a atualização é concluída com sucesso. Em caso de erro nos campos, volta para `edit.php` com o parâmetro `error=empty_fields`.

### `delete.php`

Lê o `id` da URL, prepara `DELETE FROM contatos WHERE id = ?`, executa a exclusão com `bind_param("i", ...)` e redireciona para `index.php`. Sem ID, redireciona para `error.php`.

## Resumo do fluxo

**Listar:** `teste.php` → `ContatoController::index()` → `ContatoRepository::listar()` → SQL → objetos `Contato` → `listar.php`.

**Cadastrar:** `form.php` → `POST index.php?acao=criar` → `ContatoController::criar()` → validação na entidade → `ContatoRepository::inserir()` → SQL `INSERT` → redirecionamento ou reexibição do formulário com erros.

**Editar/excluir:** links em `listar.php` → `edit.php` ou `delete.php` → `update.php` no envio do formulário → redirecionamento para a listagem.
