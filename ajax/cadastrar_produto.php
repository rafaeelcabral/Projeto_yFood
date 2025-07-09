<?php
session_start();
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    echo json_encode(['sucesso' => false, 'erro' => 'Não autorizado']);
    exit;
}
require_once '../config/database.php';
$nome = trim($_POST['nome'] ?? '');
$tipo = $_POST['tipo'] ?? '';
$preco = $_POST['preco'] ?? '';
$disponivel = $_POST['disponivel'] ?? '';
$descricao = trim($_POST['descricao'] ?? '');
if ($nome === '' || $tipo === '' || $preco === '' || $disponivel === '' || !is_numeric($preco) || !in_array($tipo, ['Combo','Hambúrguer','Batata','Acompanhamento','Bebida'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Dados inválidos']);
    exit;
}
$stmt = $pdo->prepare('INSERT INTO produtos (nome, descricao, tipo, preco, disponivel) VALUES (?, ?, ?, ?, ?)');
if ($stmt->execute([$nome, $descricao, $tipo, $preco, $disponivel])) {
    $id = $pdo->lastInsertId();
    echo json_encode(['sucesso' => true, 'produto' => [
        'id' => $id,
        'nome' => htmlspecialchars($nome),
        'descricao' => htmlspecialchars($descricao),
        'tipo' => $tipo,
        'preco' => $preco,
        'disponivel' => $disponivel
    ]]);
} else {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao cadastrar']);
} 