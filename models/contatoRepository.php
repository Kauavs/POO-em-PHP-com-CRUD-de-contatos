<?php

class ContatoRepository implements ContatoRepositoryInterface
{
    private mysqli $conn;

    // Recebe o objeto de conexão e guarda só a conexão mysqli
    public function __construct(Conexaobanco $db)
    {
        $this->conn = $db->getConnection();
    }

    // 1) LISTAR: devolve todos os contatos
    public function listar(): array
    {
        $resultado = $this->conn->query("SELECT * FROM contatos ORDER BY nome ASC");

        $contatos = [];
        foreach ($resultado as $linha) {          // cada $linha é um array do banco
            $contatos[] = $this->linhaParaContato($linha);
        }

        return $contatos;
    }

    // 2) BUSCAR POR ID: devolve um contato ou null
    public function buscarPorId(int $id): ?Contato
    {
        $stmt = $this->conn->prepare("SELECT * FROM contatos WHERE id = ?");
        $stmt->execute([$id]);                     // o valor entra no ?

        $linha = $stmt->get_result()->fetch_assoc();

        if ($linha === null) {                     // não achou
            return null;
        }

        return $this->linhaParaContato($linha);
    }

    // 3) INSERIR: grava um contato novo
    public function inserir(Contato $contato): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO contatos (nome, telefone, email) VALUES (?, ?, ?)"
        );

        return $stmt->execute([
            $contato->getNome(),
            $contato->getTelefone(),
            $contato->getEmail(),
        ]);
    }

    // 4) ATUALIZAR: altera um contato que já existe
    public function atualizar(Contato $contato): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE contatos SET nome = ?, telefone = ?, email = ? WHERE id = ?"
        );

        return $stmt->execute([
            $contato->getNome(),
            $contato->getTelefone(),
            $contato->getEmail(),
            $contato->getId(),
        ]);
    }

    // 5) EXCLUIR: apaga pelo id
    public function excluir(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM contatos WHERE id = ?");

        return $stmt->execute([$id]);
    }

    // Transforma uma linha do banco (array) em um objeto Contato
    private function linhaParaContato(array $linha): Contato
    {
        $contato = new Contato();
        $contato->setId((int) $linha['id']);
        $contato->setNome($linha['nome']);
        $contato->setTelefone($linha['telefone']);
        $contato->setEmail($linha['email'] ?? '');   // email pode ser NULL no banco
        $contato->setDataCadastro($linha['data_cadastro']);

        return $contato;
    }
}