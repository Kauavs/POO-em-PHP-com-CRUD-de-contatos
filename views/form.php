<?php

/**
 * @var array  $dados  valores do formulário (id, nome, telefone, email)
 * @var array  $erros  mensagens de erro por campo
 * @var string $titulo título da página
 * @var string $acao   ação do formulário (criar ou atualizar)
 * @var string $botao  texto do botão
 */
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo); ?></title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>
    <div class="container container-estreito">

        <header class="topo">
            <h1 class="titulo"><?php echo htmlspecialchars($titulo); ?></h1>
        </header>

        <div class="cartao">
            <form action="index.php?acao=<?php echo htmlspecialchars($acao); ?>" method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars((string) $dados['id']); ?>">

                <div class="campo">
                    <label>Nome</label>
                    <input type="text" name="nome" value="<?php echo htmlspecialchars($dados['nome']); ?>">
                    <span class="erro"><?php echo htmlspecialchars($erros['nome'] ?? ''); ?></span>
                </div>

                <div class="campo">
                    <label>Telefone</label>
                    <input type="text" name="telefone" value="<?php echo htmlspecialchars($dados['telefone']); ?>">
                    <span class="erro"><?php echo htmlspecialchars($erros['telefone'] ?? ''); ?></span>
                </div>

                <div class="campo">
                    <label>Email</label>
                    <input type="text" name="email" value="<?php echo htmlspecialchars($dados['email']); ?>">
                    <span class="erro"><?php echo htmlspecialchars($erros['email'] ?? ''); ?></span>
                </div>

                <div class="botoes">
                    <input class="btn" type="submit" value="<?php echo htmlspecialchars($botao); ?>">
                    <a class="cancelar" href="index.php">Cancelar</a>
                </div>
            </form>
        </div>

    </div>
</body>

</html>