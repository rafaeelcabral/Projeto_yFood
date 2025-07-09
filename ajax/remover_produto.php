<?php
session_start();
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    echo json_encode(['sucesso' => false, 'erro' => 'Não autorizado']);
    exit;
}
require_once '../config/database.php';
$id = $_POST['id'] ?? '';
if ($id === '' || !is_numeric($id)) {
    echo json_encode(['sucesso' => false, 'erro' => 'ID inválido']);
    exit;
}
$stmt = $pdo->prepare('DELETE FROM produtos WHERE id = ?');
if ($stmt->execute([$id])) {
    echo json_encode(['sucesso' => true]);
} else {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao remover']);
} 