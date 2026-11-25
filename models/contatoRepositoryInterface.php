<?php

interface ContatoRepositoryInterface
{
    public function listar(): array;               // array de Contato
    public function buscarPorId(int $id): ?Contato;
    public function inserir(Contato $contato): bool;
    public function atualizar(Contato $contato): bool;
    public function excluir(int $id): bool;
}
