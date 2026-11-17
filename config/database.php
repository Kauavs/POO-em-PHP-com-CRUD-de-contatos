<?php
// Configurações do banco de dados
Class Conexaobanco{

    private string $host = 'localhost';
    private string $username = "root";
    private string $password = "root";
    private string $database = "agenda";
    private int $port = 3306;

    private ?mysqli $conn = null;

    public function getConnection(): mysqli { // criar a coxeão com o banco de dados

        if ($this->conn === null) { // verifica se a conexão já foi estabelecida
            $conn = mysqli_connect( // estabelece a conexão com o banco de dados
                $this->host, // declara as variáveis de conexão
                $this->username,
                $this->password,
                $this->database,
                $this->port
            );
            if (!$conn) {
                 die("Falha na conexão: " . mysqli_connect_error());    
            }
            mysqli_set_charset($conn, "utf8");
            $this->conn = $conn; // armazena a conexão na propriedade $conn
        }
        return $this->conn; // retorna a conexão
    }
}
?>