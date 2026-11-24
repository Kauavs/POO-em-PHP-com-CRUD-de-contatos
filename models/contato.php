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
        $telefone = trim($telefone);

        if ($telefone === '') {
            throw new InvalidArgumentException("O telefone não pode ser vazio.");
        }

        // só aceita números, espaço, parênteses e hífen
        if (!preg_match('/^[0-9\s()\-]+$/', $telefone)) {
            throw new InvalidArgumentException("O telefone deve conter apenas números.");
        }

        // conta só os dígitos: DDD + número = 10 (fixo) ou 11 (celular)
        $digitos = preg_replace('/\D/', '', $telefone);

        if (strlen($digitos) < 10 || strlen($digitos) > 11) {
            throw new InvalidArgumentException("Informe o telefone com DDD (10 ou 11 dígitos).");
        }

        $this->telefone = $telefone;
    }

    public function setEmail(string $email): void
    {
        $email = trim($email);
    
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
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
        if ($this->nome === '' || $this->telefone === '') {
            throw new InvalidArgumentException('Nome e telefone são obrigatórios');
        }
    }

}

?>