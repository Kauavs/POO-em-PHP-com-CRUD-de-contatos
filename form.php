<?php
/**
 * @var array $dados valores do formulário, enviados pelo controller
 * @var array $erros mensagens de erro por campo, enviadas pelo controller
 */
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Contato</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="container container-estreito">

        <header class="topo">
            <h1 class="titulo">Adicionar Novo Contato</h1>
        </header>

        <div class="cartao">
            <form action="index.php?acao=criar" method="post">
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
                    <input class="btn" type="submit" value="Adicionar">
                    <a class="cancelar" href="index.php">Cancelar</a>
                </div>
            </form>
        </div>

    </div>
</body>
</html>