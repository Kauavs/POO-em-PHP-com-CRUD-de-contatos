<?php

class contatoController {

    private ContatoRepositoryInterface $repo;

    public function __construct(ContatoRepositoryInterface $repo) {
        $this->repo = $repo;
    }

    public function index(): void {
        $contatos = $this->repo->listar();
        require 'listar.php';
    }
}




?>