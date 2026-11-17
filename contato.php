<?php
// Configurações do banco de dados
class Contato{

    private ?int $id = null;
    private string $nome = '';
    private string $telefone = '';
    private string $email = '';
    private ?string $dataCadastro = null;
    
    // getters 

    public function getId(): ?int {
        return $this->id;
    }

    public function getNome(): string {
        return $this->nome;
    }   

    public function getTelefone(): string {
        return $this->telefone;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getDataCadastro(): ?string {
        return $this->dataCadastro;
    }

    // setters

    public function setId(?int $id): void {
        $this->id = $id;
    }   

    public function setNome(string $nome): void {
        
        if(trim($nome) === '') {
            throw new InvalidArgumentException("O nome não pode ser vazio."); // lança uma exceção, que interrompe o método e 
                                                                              //  avisa que o argumento é inválido. Quem chamou
                                                                              // decide o que fazer com o erro.
        }

        $this->nome = trim($nome);
    }

    public function setTelefone(string $telefone): void {

        if(trim($telefone) === '') {
            throw new InvalidArgumentException("O Telefone não pode ser vazio.");
        }

        $this->telefone = trim($telefone);
    }

    public function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('E-mail inválido');
        }

        $this->email = $email;
    }

    public function setDataCadastro(?string $dataCadastro): void
    {
        $this->dataCadastro = $dataCadastro;
    }

    public function validar(): void
    {
        if ($this->nome === '' || $this->telefone === '' || $this->email === '') {
            throw new InvalidArgumentException('Nome, telefone e e-mail são obrigatórios');
        }
    }

}

?>