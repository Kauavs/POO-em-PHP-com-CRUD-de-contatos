<?php
/** @var array $dados   */
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Contato</title>
</head>
<body>
    <h1>Adicionar Novo Contato</h1>
    
    <form action="index.php?acao=criar" method="post">
        <div>
            <label>Nome</label>
            <input type="text" name="nome" value="<?php echo htmlspecialchars($dados['nome']); ?>">
            <span><?php echo htmlspecialchars($erros['nome'] ?? ''); ?></span>
        </div>
        
        <div>
            <label>Telefone</label>
            <input type="text" name="telefone" value="<?php echo htmlspecialchars($dados['telefone']); ?>">
            <span><?php echo htmlspecialchars($erros['telefone'] ?? ''); ?></span>
        </div>
        
        <div>
            <label>Email</label>
            <input type="text" name="email" value="<?php echo htmlspecialchars($dados['email']); ?>">
            <span><?php echo htmlspecialchars($erros['email'] ?? ''); ?></span>
        </div>
        
        <div>
            <input type="submit" value="Adicionar">
            <a href="index.php">Cancelar</a>
        </div>
    </form>
</body>
</html>