<?php

class ContatoRepository implements ContatoRepositoryInterface {

    private mysqli $conn;

    public function __construct(Conexaobanco $db) { // Construtor recebe a conexão com o banco de dados
        $this->conn = $db->getConnection();
    }

    public function listar(): array {
        $result = $this->conn->query("SELECT * FROM contatos ORDER BY nome ASC");
        $contatos = [];  // Cria um array vazio que vai receber os objetos Contato
        while ($row = $result->fetch_assoc()) {
            $contatos[] = $this->criarContato($row); 
        } // Cria classe contatos e guarda os valores (arrays) dentro
        return $contatos;
    }

    public function buscarPorId(int $id): ?Contato {
        $stmt = $this->conn->prepare("SELECT * FROM contatos WHERE id = ?"); // Prepara o comando para chamar por id 
        $stmt->bind_param("i", $id); // Coloca valor no lugar de ?
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc(); // Ler o resultado. 
        $stmt->close();
        return $row ? $this->criarContato($row) : null;
    }

    public function inserir(Contato $c): bool {
        $stmt = $this->conn->prepare("INSERT INTO contatos (nome, telefone, email) VALUES (?, ?, ?)"); //Prepara comando
        $nome = $c->getNome(); $tel = $c->getTelefone(); $email = $c->getEmail(); //Pega os dados do objeto para ler os valores
        $stmt->bind_param("sss", $nome, $tel, $email); // Colocar valor no lugar de ?
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function atualizar(Contato $c): bool {
        $stmt = $this->conn->prepare("UPDATE contatos SET nome = ?, telefone = ?, email = ? WHERE id = ?"); //Prepara comando
        $nome = $c->getNome(); $tel = $c->getTelefone(); $email = $c->getEmail(); $id = $c->getId(); //Pega os dados do objeto para ler os valores
        $stmt->bind_param("sssi", $nome, $tel, $email, $id); // Colocar valor no lugar de ?
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function excluir(int $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM contatos WHERE id = ?"); //Prepara comando
        $stmt->bind_param("i", $id); // Colocar valor no lugar de ?
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // método privado: detalhe interno, ninguém de fora precisa chamar
    private function criarContato(array $row): Contato {
        $c = new Contato();
        $c->setId((int) $row['id']);
        $c->setNome($row['nome']);
        $c->setTelefone($row['telefone']);
        $c->setEmail($row['email'] ?? '');
        $c->setDataCadastro($row['data_cadastro']);
        return $c;
    }
}
