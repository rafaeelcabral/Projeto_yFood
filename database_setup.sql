-- =====================================================
-- yFood - Sistema de Lanchonete
-- Script de Criação do Banco de Dados
-- =====================================================

-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS raf-projeto_yFood
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Usar o banco de dados
USE raf-projeto_yFood;

-- =====================================================
-- TABELA: usuarios
-- =====================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    tipo ENUM('admin', 'cliente') DEFAULT 'cliente',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: produtos
-- =====================================================
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    tipo ENUM('Combo', 'Hambúrguer', 'Batata', 'Acompanhamento', 'Bebida') NOT NULL,
    imagem VARCHAR(255),
    disponivel BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: pedidos
-- =====================================================
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pendente', 'preparando', 'saiu pra entrega', 'entregue', 'cancelado') DEFAULT 'pendente',
    data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: itens_pedido
-- =====================================================
CREATE TABLE IF NOT EXISTS itens_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT,
    produto_id INT,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DADOS INICIAIS
-- =====================================================



-- Inserir produtos de exemplo
INSERT INTO produtos (nome, descricao, preco, tipo) VALUES 
('Combo Big Mac', 'Hambúrguer, batata e refrigerante', 25.90, 'Combo'),
('Combo Quarterão', 'Hambúrguer, batata e refrigerante', 22.90, 'Combo'),
('Big Mac', 'Dois hambúrgueres, alface, queijo, molho especial', 18.90, 'Hambúrguer'),
('Quarterão', 'Hambúrguer, queijo, alface, tomate, cebola', 15.90, 'Hambúrguer'),
('Batata Frita Grande', 'Batata frita crocante', 8.90, 'Batata'),
('Batata Frita Média', 'Batata frita crocante', 6.90, 'Batata'),
('Nuggets de Frango', '6 unidades de nuggets', 12.90, 'Acompanhamento'),
('Coca-Cola', 'Refrigerante 350ml', 5.90, 'Bebida'),
('Sprite', 'Refrigerante 350ml', 5.90, 'Bebida'),
('Fanta Laranja', 'Refrigerante 350ml', 5.90, 'Bebida')
ON DUPLICATE KEY UPDATE id=id;

-- Usuário admin inicial para testes
INSERT INTO usuarios_admin (username, senha, nome, email, tipo, data_criacao)
VALUES ('admin', '$2y$10$eImiTXuWVxfM37uY4JANjQ==', 'Administrador', 'admin@yfood.com', 'superadmin', NOW());
-- A senha acima é um hash de exemplo. Recomenda-se gerar um hash real com password_hash('sua_senha', PASSWORD_DEFAULT) no PHP.

-- =====================================================
-- ÍNDICES PARA MELHOR PERFORMANCE
-- =====================================================

-- Índices para tabela usuarios
CREATE INDEX idx_usuarios_username ON usuarios(username);
CREATE INDEX idx_usuarios_tipo ON usuarios(tipo);

-- Índices para tabela produtos
CREATE INDEX idx_produtos_tipo ON produtos(tipo);
CREATE INDEX idx_produtos_disponivel ON produtos(disponivel);
CREATE INDEX idx_produtos_nome ON produtos(nome);

-- Índices para tabela pedidos
CREATE INDEX idx_pedidos_usuario ON pedidos(usuario_id);
CREATE INDEX idx_pedidos_status ON pedidos(status);
CREATE INDEX idx_pedidos_data ON pedidos(data_pedido);

-- Índices para tabela itens_pedido
CREATE INDEX idx_itens_pedido_pedido ON itens_pedido(pedido_id);
CREATE INDEX idx_itens_pedido_produto ON itens_pedido(produto_id);

-- =====================================================
-- CONSULTAS ÚTEIS PARA TESTE
-- =====================================================

-- Verificar usuários cadastrados
-- SELECT * FROM usuarios;

-- Verificar produtos cadastrados
-- SELECT * FROM produtos ORDER BY tipo, nome;

-- Verificar pedidos
-- SELECT p.*, u.nome as cliente FROM pedidos p LEFT JOIN usuarios u ON p.usuario_id = u.id ORDER BY p.data_pedido DESC;

-- Verificar itens de um pedido específico
-- SELECT ip.*, pr.nome as produto FROM itens_pedido ip LEFT JOIN produtos pr ON ip.produto_id = pr.id WHERE ip.pedido_id = 1;

-- =====================================================
-- FIM DO SCRIPT
-- ===================================================== 