<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_ok'])) {
    require_once '../config/database.php';
    $id_ok = intval($_POST['id_ok']);
    $stmt = $pdo->prepare("UPDATE pedidos SET status = 'preparando' WHERE id = ? AND status = 'pendente'");
    $stmt->execute([$id_ok]);
    header('Location: index.php');
    exit;
}
session_start();
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    header('Location: login.php');
    exit;
}
require_once '../config/database.php';
$admin_nome = $_SESSION['admin_nome'] ?? 'Administrador';
// Produtos mais vendidos
$sql_mais_vendidos = "SELECT p.nome, SUM(i.quantidade) as total_vendido FROM itens_pedido i JOIN produtos p ON i.produto_id = p.id JOIN pedidos pe ON i.pedido_id = pe.id WHERE pe.status = 'entregue' GROUP BY i.produto_id ORDER BY total_vendido DESC, p.nome ASC LIMIT 5";
$stmt = $pdo->query($sql_mais_vendidos);
$mais_vendidos = $stmt->fetchAll();
// Últimos 6 pedidos
$sql_ultimos_pedidos = "SELECT pe.id, u.nome as cliente, pe.total, pe.status, pe.data_pedido FROM pedidos pe LEFT JOIN usuarios u ON pe.usuario_id = u.id ORDER BY pe.id DESC LIMIT 6";
$stmt = $pdo->query($sql_ultimos_pedidos);
$ultimos_pedidos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Admin - yFood</title>
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
        .dashboard {
            padding: 32px; display: flex; flex-direction: column; gap: 32px;
        }
        .dashboard-top { display: flex; gap: 32px; }
        .mais-vendidos { flex-basis: 35%; max-width: 35%; height: 500px; overflow-y: auto; display: flex; flex-direction: column; }
        .grafico { flex-basis: 65%; max-width: 65%; display: flex; align-items: center; justify-content: center; min-height: 500px; height: 500px; }
        .ultimos-pedidos { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #0001; padding: 24px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f1faee; }
        .mais-vendidos-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .mais-vendidos-list li {
            background: #f1faee;
            border-radius: 6px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            font-size: 1.08em;
            font-weight: 500;
            box-shadow: 0 1px 4px #0001;
            gap: 10px;
        }
        .mais-vendidos-list li .medalha {
            font-size: 1.2em;
            margin-right: 8px;
        }
    </style>
</head>
<body>
<div class="admin-container">
    <nav class="sidebar">
        <img src="../assets/img/logo.png" alt="Logo" width="100">
        <a href="index.php" class="active">Dashboard</a>
        <a href="produtos.php">Produtos</a>
        <a href="pedidos.php">Pedidos</a>
        <a href="clientes.php">Clientes</a>
    </nav>
    <div class="content">
        <div class="header">
            <span>Painel do Administrador</span>
            <span><a href="logout.php" style="color:#fff;text-decoration:underline;"><img src="../assets/img/sair.png" alt="Sair" width="30"></a></span>
        </div>
        <div class="dashboard">
            <div class="dashboard-top">
                <div class="mais-vendidos">
                    <h3>Produtos Mais Vendidos</h3>
                    <ul class="mais-vendidos-list">
                        <?php
                        $medalhas = ['🥇','🥈','🥉','',''];
                        foreach ($mais_vendidos as $i => $mv): ?>
                            <li><span class="medalha"><?php echo $medalhas[$i] ?? '⭐'; ?></span> <?php echo htmlspecialchars($mv['nome']); ?> <span style="margin-left:auto; color:#222; font-weight:400;"><?php echo $mv['total_vendido']; ?> vendas</span></li>
                        <?php endforeach; ?>
                        <?php if (empty($mais_vendidos)): ?>
                            <li style="text-align:center; color:#888;">Nenhuma venda registrada ainda.</li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="grafico">
                    <canvas id="graficoVendas" width="350" height="500"></canvas>
                </div>
            </div>
            <div class="ultimos-pedidos">
                <h3>Últimos 6 Pedidos</h3>
                <table>
                    <thead>
                        <tr><th>ID</th><th>Cliente</th><th>Total</th><th>Status</th><th>Data</th><th>Ações</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        function getStatusColor($status) {
                            switch ($status) {
                                case 'pendente': return '#ffeb3b'; // amarelo
                                case 'preparando': return '#ff9800'; // laranja
                                case 'saiu pra entrega': return '#2196f3'; // azul
                                case 'entregue': return '#4caf50'; // verde
                                case 'cancelado': return '#f44336'; // vermelho
                                default: return '#ccc';
                            }
                        }
                        foreach ($ultimos_pedidos as $pedido): ?>
                        <tr>
                            <td><?php echo $pedido['id']; ?></td>
                            <td><?php echo htmlspecialchars($pedido['cliente'] ?? '-'); ?></td>
                            <td>R$ <?php echo number_format($pedido['total'],2,',','.'); ?></td>
                            <td><span class="status-<?php echo str_replace(' ', '-', $pedido['status']); ?>" style="padding:4px 12px;border-radius:12px;font-weight:bold;display:inline-block;min-width:90px;text-align:center;">
                                <?php echo ucfirst($pedido['status']); ?>
                            </span></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></td>
                            <td>
                            <?php if ($pedido['status'] === 'pendente'): ?>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="id_ok" value="<?php echo $pedido['id']; ?>">
                                    <button type="submit" class="btn btn-warning">OK</button>
                                </form>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($ultimos_pedidos)): ?>
                        <tr><td colspan="5" style="text-align:center; color:#888;">Nenhum pedido encontrado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de pizza para produtos mais vendidos (dados reais)
const ctx = document.getElementById('graficoVendas').getContext('2d');
const labels = <?php echo json_encode(array_column($mais_vendidos, 'nome')); ?>;
const data = <?php echo json_encode(array_map('intval', array_column($mais_vendidos, 'total_vendido'))); ?>;
new Chart(ctx, {
    type: 'pie',
    data: {
        labels: labels,
        datasets: [{
            data: data,
            backgroundColor: ['#e63946', '#f1faee', '#a8dadc', '#457b9d', '#22223b']
        }]
    },
    options: {
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>
<?php
?>
</body>
</html> 