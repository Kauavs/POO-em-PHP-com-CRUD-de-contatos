<?php

/**    @var Contato[] $contatos lista enviada pelo ContatoController */

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Telefônica</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>
    <div class="container">

        <header class="topo">
            <h1>Agenda Telefônica</h1>
            <a class="btn" href="index.php?acao=novo">+ Novo contato</a>
        </header>

        <div class="cartao">
            <h2>Lista de Contatos</h2>

            <?php if (count($contatos) > 0): ?>
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>Email</th>
                            <th>Data de Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contatos as $c): ?>
                            <tr>
                                <td><?php echo $c->getId(); ?></td>
                                <td><?php echo htmlspecialchars($c->getNome()); ?></td>
                                <td><?php echo htmlspecialchars($c->getTelefone()); ?></td>
                                <td><?php echo htmlspecialchars($c->getEmail()); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($c->getDataCadastro())); ?></td>
                                <td class="acoes">
                                    <a class="acao" href="index.php?acao=editar&id=<?php echo $c->getId(); ?>">Editar</a>
                                    <form class="form-excluir" action="index.php?acao=excluir" method="post"
                                        onsubmit="return confirm('Tem certeza que deseja excluir este contato?');">
                                        <input type="hidden" name="id" value="<?php echo $c->getId(); ?>">
                                        <button class="acao excluir" type="submit">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="vazio">Nenhum contato encontrado.</p>
            <?php endif; ?>
        </div>

    </div>
</body>

</html>