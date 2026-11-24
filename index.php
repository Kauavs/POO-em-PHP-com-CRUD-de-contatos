<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Contato.php';
require_once __DIR__ . '/models/ContatoRepositoryInterface.php';
require_once __DIR__ . '/models/ContatoRepository.php';
require_once __DIR__ . '/controller/ContatoController.php';

try {
    $db = new Conexaobanco();
    $repo = new ContatoRepository($db);
    $controller = new ContatoController($repo);

    $acao = $_GET['acao'] ?? 'index';

    switch ($acao) {
        case 'novo':
            $controller->novo();
            break;
        case 'criar':
            $controller->criar();
            break;
        case 'editar':
            $controller->editar((int) ($_GET['id'] ?? 0));
            break;
        case 'atualizar':
            $controller->atualizar();
            break;
        case 'excluir':
            $controller->excluir();
            break;
        default:
            $controller->index();
    }
} catch (mysqli_sql_exception $e) {
    echo 'Erro ao acessar o banco de dados: ' . htmlspecialchars($e->getMessage());
}

