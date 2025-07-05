<?php
session_start();
header('Content-Type: application/json');

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Verificar se há carrinho
if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    echo json_encode(['success' => false, 'message' => 'Carrinho vazio']);
    exit;
}

// Ler dados JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['id']) || !isset($input['acao'])) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

$id = intval($input['id']);
$acao = $input['acao'];

// Validar dados
if ($id <= 0 || !in_array($acao, ['adicionar', 'remover'])) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

// Verificar se o item existe no carrinho
if (!isset($_SESSION['carrinho'][$id])) {
    echo json_encode(['success' => false, 'message' => 'Item não encontrado no carrinho']);
    exit;
}

// Atualizar quantidade
if ($acao === 'adicionar') {
    $_SESSION['carrinho'][$id]['quantidade']++;
} else {
    $_SESSION['carrinho'][$id]['quantidade']--;
    
    // Remover item se quantidade for zero ou menor
    if ($_SESSION['carrinho'][$id]['quantidade'] <= 0) {
        unset($_SESSION['carrinho'][$id]);
    }
}

// Calcular total
$total = 0;
foreach ($_SESSION['carrinho'] as $item) {
    $total += $item['preco'] * $item['quantidade'];
}

echo json_encode([
    'success' => true,
    'message' => 'Carrinho atualizado',
    'total' => $total,
    'quantidade' => isset($_SESSION['carrinho'][$id]) ? $_SESSION['carrinho'][$id]['quantidade'] : 0
]);
?> 