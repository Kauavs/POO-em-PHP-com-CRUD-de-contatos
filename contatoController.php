<?php

class ContatoController {

    private ContatoRepositoryInterface $repo;

    public function __construct(ContatoRepositoryInterface $repo) {
        $this->repo = $repo;
    }

    public function index(): void {
        $contatos = $this->repo->listar();
        require 'listar.php';

    }
    public function novo(): void {
        $dados = ['nome' => '', 'telefone' => '', 'email' => ''];
        $erros = [];
        require __DIR__ . '/form.php';
    }
    
    public function criar(): void {
        $contato = new Contato();
        $erros = [];

        $dados = [
            'nome'     => trim($_POST['nome'] ?? ''),
            'telefone' => trim($_POST['telefone'] ?? ''),
            'email'    => trim($_POST['email'] ?? ''),
        ];

        try {
            $contato -> setNome($dados['nome']);
        }   catch (InvalidArgumentException $e) {
            $erros['nome'] = $e->getMessage();
        }

        try {
            $contato -> setTelefone($dados['telefone']);
        }   catch (InvalidArgumentException $e) {
            $erros['telefone'] = $e->getMessage();
        }

        try {
            $contato -> setEmail($dados['email']);
        }   catch (InvalidArgumentException $e) {
            $erros['email'] = $e->getMessage();
        }

        if (empty($erros)) {
            $this->repo->inserir($contato);
            header('Location: index.php');
            exit;
        }

        require __DIR__ . '/form.php';
    

    }
}




?>