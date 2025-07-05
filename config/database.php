<?php
// Configurações do banco de dados
$host = 'localhost';
$dbname = 'raf-projeto_yFood';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

// Função para criar as tabelas se não existirem
function criarTabelas($pdo) {
    // Tabela de usuários
    $sql_usuarios = "
    CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        senha VARCHAR(255) NOT NULL,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100),
        tipo ENUM('admin', 'cliente') DEFAULT 'cliente',
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    // Tabela de produtos
    $sql_produtos = "
    CREATE TABLE IF NOT EXISTS produtos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        descricao TEXT,
        preco DECIMAL(10,2) NOT NULL,
        tipo ENUM('Combo', 'Hambúrguer', 'Batata', 'Acompanhamento', 'Bebida') NOT NULL,
        imagem VARCHAR(255),
        disponivel BOOLEAN DEFAULT TRUE,
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    // Tabela de pedidos
    $sql_pedidos = "
    CREATE TABLE IF NOT EXISTS pedidos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT,
        total DECIMAL(10,2) NOT NULL,
        status ENUM('pendente', 'preparando', 'pronto', 'entregue', 'cancelado') DEFAULT 'pendente',
        data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    )";
    
    // Tabela de itens do pedido
    $sql_itens_pedido = "
    CREATE TABLE IF NOT EXISTS itens_pedido (
        id INT AUTO_INCREMENT PRIMARY KEY,
        pedido_id INT,
        produto_id INT,
        quantidade INT NOT NULL,
        preco_unitario DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
        FOREIGN KEY (produto_id) REFERENCES produtos(id)
    )";
    
    try {
        $pdo->exec($sql_usuarios);
        $pdo->exec($sql_produtos);
        $pdo->exec($sql_pedidos);
        $pdo->exec($sql_itens_pedido);
        

        
        // Inserir produtos de exemplo se não existirem
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM produtos");
        $stmt->execute();
        
        if ($stmt->fetchColumn() == 0) {
            $produtos_exemplo = [
                ['Combo Big Mac', 'Hambúrguer, batata e refrigerante', 25.90, 'Combo'],
                ['Combo Quarterão', 'Hambúrguer, batata e refrigerante', 22.90, 'Combo'],
                ['Big Mac', 'Dois hambúrgueres, alface, queijo, molho especial', 18.90, 'Hambúrguer'],
                ['Quarterão', 'Hambúrguer, queijo, alface, tomate, cebola', 15.90, 'Hambúrguer'],
                ['Batata Frita Grande', 'Batata frita crocante', 8.90, 'Batata'],
                ['Batata Frita Média', 'Batata frita crocante', 6.90, 'Batata'],
                ['Nuggets de Frango', '6 unidades de nuggets', 12.90, 'Acompanhamento'],
                ['Coca-Cola', 'Refrigerante 350ml', 5.90, 'Bebida'],
                ['Sprite', 'Refrigerante 350ml', 5.90, 'Bebida'],
                ['Fanta Laranja', 'Refrigerante 350ml', 5.90, 'Bebida']
            ];
            
            $stmt = $pdo->prepare("INSERT INTO produtos (nome, descricao, preco, tipo) VALUES (?, ?, ?, ?)");
            foreach ($produtos_exemplo as $produto) {
                $stmt->execute($produto);
            }
        }
        
    } catch (PDOException $e) {
        die("Erro ao criar tabelas: " . $e->getMessage());
    }
}

// Criar tabelas automaticamente
criarTabelas($pdo);
?> 