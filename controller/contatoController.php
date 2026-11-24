<?php

class ContatoController {

    private ContatoRepositoryInterface $repo;

    public function __construct(ContatoRepositoryInterface $repo) {
        $this->repo = $repo;
    }

    public function index(): void {
        $contatos = $this->repo->listar();
        require __DIR__ . '/../views/listar.php';

    }
    public function novo(): void {
        $dados = ['id' => '', 'nome' => '', 'telefone' => '', 'email' => ''];
        $erros = [];
        $this->mostrarFormulario('Adicionar Novo Contato', 'criar', 'Adicionar', $dados, $erros);
    }
    
    public function criar(): void {
        $contato = new Contato();
        $erros = [];

        $dados = [
            'id'       => '',
            'nome'     => trim($_POST['nome'] ?? ''),
            'telefone' => trim($_POST['telefone'] ?? ''),
            'email'    => trim($_POST['email'] ?? ''),
        ];

        try {
            $contato->setNome($dados['nome']);
        } catch (InvalidArgumentException $e) {
            $erros['nome'] = $e->getMessage();
        }

        try {
            $contato->setTelefone($dados['telefone']);
        } catch (InvalidArgumentException $e) {
            $erros['telefone'] = $e->getMessage();
        }

        try {
            $contato->setEmail($dados['email']);
        } catch (InvalidArgumentException $e) {
            $erros['email'] = $e->getMessage();
        }

        if (empty($erros)) {
            $this->repo->inserir($contato);
            header('Location: index.php');
            exit;
        }

        $this->mostrarFormulario('Adicionar Novo Contato', 'criar', 'Adicionar', $dados, $erros);
    }
        // Mostra o formulário preenchido com o contato escolhido
        public function editar(int $id): void {
            $contato = $this->repo->buscarPorId($id);
    
            if ($contato === null) {          // id que não existe: volta para a lista
                header('Location: index.php');
                exit;
            }
    
            $dados = [
                'id'       => $contato->getId(),
                'nome'     => $contato->getNome(),
                'telefone' => $contato->getTelefone(),
                'email'    => $contato->getEmail(),
            ];
    
            $this->mostrarFormulario('Editar Contato', 'atualizar', 'Salvar alterações', $dados, []);
        }
    
        // Recebe o formulário de edição
        public function atualizar(): void {
            $id = (int) ($_POST['id'] ?? 0);
            $contato = $this->repo->buscarPorId($id);   // parte do contato que já existe no banco
    
            if ($contato === null) {
                header('Location: index.php');
                exit;
            }
    
            $erros = [];
    
            $dados = [
                'id'       => $id,
                'nome'     => trim($_POST['nome'] ?? ''),
                'telefone' => trim($_POST['telefone'] ?? ''),
                'email'    => trim($_POST['email'] ?? ''),
            ];
    
            try {
                $contato->setNome($dados['nome']);
            } catch (InvalidArgumentException $e) {
                $erros['nome'] = $e->getMessage();
            }
    
            try {
                $contato->setTelefone($dados['telefone']);
            } catch (InvalidArgumentException $e) {
                $erros['telefone'] = $e->getMessage();
            }
    
            try {
                $contato->setEmail($dados['email']);
            } catch (InvalidArgumentException $e) {
                $erros['email'] = $e->getMessage();
            }
    
            if (empty($erros)) {
                $this->repo->atualizar($contato);
                header('Location: index.php');
                exit;
            }
    
            $this->mostrarFormulario('Editar Contato', 'atualizar', 'Salvar alterações', $dados, $erros);
        }
    
        // Prepara as variáveis do formulário e carrega a view
        private function mostrarFormulario(string $titulo, string $acao, string $botao, array $dados, array $erros): void {
            require __DIR__ . '/../views/form.php';
        }
        
        // Exclui o contato e volta para a lista (só aceita POST)
    public function excluir(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);

        if ($id > 0) {
            $this->repo->excluir($id);
        }

        header('Location: index.php');
        exit;
    }

}




?>