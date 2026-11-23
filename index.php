<?php

require_once 'config/database.php';
require_once 'contato.php';
require_once 'contatoRepositoryInterface.php';
require_once 'contatoRepository.php';
require_once 'contatoController.php';

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
        default:
            $controller->index();
    }
} catch (mysqli_sql_exception $e) {
    echo 'Erro ao acessar o banco de dados: ' . htmlspecialchars($e->getMessage());
}

