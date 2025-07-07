<?php
    session_start();
    if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
        header('Location: login.php');
        exit;
    }
    require_once '../config/database.php';
    $id_filtro = $_GET['id'] ?? '';
    $status_filtro = $_GET['status'] ?? '';
    // Situações possíveis
    $situacoes = ['pendente', 'preparando', 'pronto', 'entregue', 'cancelado'];
    $sql = 'SELECT p.*, u.nome as cliente_nome FROM pedidos p LEFT JOIN usuarios u ON p.usuario_id = u.id WHERE 1=1';
    $params = [];
    if ($id_filtro !== '') {
        $sql .= ' AND p.id = ?';
        $params[] = $id_filtro;
    }
    if ($status_filtro !== '') {
        $sql .= ' AND p.status = ?';
        $params[] = $status_filtro;
    }
    $sql .= ' ORDER BY p.id DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $pedidos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pedidos - Admin yFood</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background: #f7f7f7; }
        .admin-container { display: flex; min-height: 100vh; }
        .sidebar {
            width: 200px; background: #22223b; color: #fff; padding-top: 24px; display: flex; flex-direction: column;
        }
        .sidebar img { display: block; margin: 0 auto 24px; }
        .sidebar a {
            color: #fff; text-decoration: none; padding: 14px 24px; display: block; font-weight: bold; border-left: 4px solid transparent;
        }
        .sidebar a.active, .sidebar a:hover { background: #e63946; border-left: 4px solid #f1faee; }
        .content { flex: 1; padding: 0; }
        .header {
            background: #e63946; color: #fff; padding: 16px 32px; display: flex; justify-content: space-between; align-items: center;
        }
        .filtros { display: flex; gap: 16px; margin-bottom: 24px; }
        .filtros input, .filtros select { padding: 6px 10px; border-radius: 4px; border: 1px solid #ccc; }
        .filtros button { background: #e63946; color: #fff; border: none; padding: 8px 16px; border-radius: 4px; font-weight: bold; cursor: pointer; }
        .pedidos-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #0001; }
        .pedidos-table th, .pedidos-table td { padding: 10px 12px; border-bottom: 1px solid #eee; text-align: left; }
        .pedidos-table th { background: #f1faee; }
        .pedidos-table td { vertical-align: middle; }
    </style>
</head>
<body>
<div class="admin-container">
    <nav class="sidebar">
        <img src="../assets/img/logo.png" alt="Logo" width="100">
        <a href="index.php">Dashboard</a>
        <a href="produtos.php">Produtos</a>
        <a href="pedidos.php" class="active">Pedidos</a>
        <a href="clientes.php">Clientes</a>
    </nav>
    <div class="content">
        <div class="header">
            <span>Pedidos</span>
            <span><?php echo htmlspecialchars($_SESSION['admin_nome']); ?> | <a href="logout.php" style="color:#fff;text-decoration:underline;">Sair</a></span>
        </div>
        <div style="padding:32px;">
            <form class="filtros" method="get">
                <input type="text" name="id" placeholder="ID do pedido" value="<?php echo htmlspecialchars($id_filtro); ?>">
                <select name="status">
                    <option value="">Situação do pedido</option>
                    <?php foreach ($situacoes as $sit): ?>
                        <option value="<?php echo $sit; ?>" <?php if ($status_filtro === $sit) echo 'selected'; ?>><?php echo ucfirst($sit); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Filtrar</button>
            </form>
            <table class="pedidos-table">
                <thead>
                    <tr><th>ID</th><th>Cliente</th><th>Total</th><th>Status</th><th>Data</th></tr>
                </thead>
                <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td><?php echo $pedido['id']; ?></td>
                        <td><?php echo htmlspecialchars($pedido['cliente_nome'] ?? '-'); ?></td>
                        <td>R$ <?php echo number_format($pedido['total'],2,',','.'); ?></td>
                        <td><?php echo ucfirst($pedido['status']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($pedidos)): ?>
                    <tr><td colspan="5" style="text-align:center;">Nenhum pedido encontrado.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html> 