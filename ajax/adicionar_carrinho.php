<?php
    session_start();
    header('Content-Type: application/json');

    // Verificar se é uma requisição POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método não permitido']);
        exit;
    }

    // Ler dados JSON
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['id']) || !isset($input['nome']) || !isset($input['preco'])) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        exit;
    }

    $id = intval($input['id']);
    $nome = trim($input['nome']);
    $preco = floatval($input['preco']);

    // Validar dados
    if ($id <= 0 || empty($nome) || $preco <= 0) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        exit;
    }

    // Inicializar carrinho se não existir
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }

    // Adicionar ou atualizar item no carrinho
    if (isset($_SESSION['carrinho'][$id])) {
        $_SESSION['carrinho'][$id]['quantidade']++;
    } else {
        $_SESSION['carrinho'][$id] = [
            'nome' => $nome,
            'preco' => $preco,
            'quantidade' => 1
        ];
    }

    // Calcular total
    $total = 0;
    foreach ($_SESSION['carrinho'] as $item) {
        $total += $item['preco'] * $item['quantidade'];
    }

    echo json_encode([
        'success' => true,
        'message' => 'Produto adicionado ao carrinho',
        'total' => $total,
        'quantidade' => $_SESSION['carrinho'][$id]['quantidade']
    ]);
?> 