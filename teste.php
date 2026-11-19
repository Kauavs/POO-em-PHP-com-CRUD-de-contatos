<?php
require_once 'config/database.php';
require_once 'contato.php';
require_once 'contatoRepositoryInterface.php';   // a interface vem ANTES...
require_once 'contatoRepository.php';            // ...da classe que a implementa
require_once 'contatoController.php';



$db = new Conexaobanco();
$repo = new ContatoRepository($db);
$controller = new ContatoController($repo);
$controller->index();