<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_ok'])) {
    require_once '../config/database.php';
    $id_ok = intval($_POST['id_ok']);
    $stmt = $pdo->prepare("UPDATE pedidos SET status = 'preparando' WHERE id = ? AND status = 'pendente'");
    $stmt->execute([$id_ok]);
    header('Location: index.php');
    exit;
}
// Processamento do select de alteração de status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_status']) && isset($_POST['novo_status'])) {
    require_once '../config/database.php';
    $id_status = intval($_POST['id_status']);
    $novo_status = $_POST['novo_status'];
    if ($novo_status === '') {
        header('Location: index.php');
        exit;
    }
    $permitidos = [
        'preparando' => ['saiu pra entrega', 'cancelado'],
        'saiu pra entrega' => ['entregue', 'cancelado']
    ];
    $stmt = $pdo->prepare("SELECT status FROM pedidos WHERE id = ?");
    $stmt->execute([$id_status]);
    $atual = $stmt->fetchColumn();
    if (isset($permitidos[$atual]) && in_array($novo_status, $permitidos[$atual])) {
        $stmt = $pdo->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
        $stmt->execute([$novo_status, $id_status]);
    }
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
    <link rel="stylesheet" href="../assets/css/admin-base.css">
    <link rel="stylesheet" href="../assets/css/admin-inline.css">
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
            <span>
                <form action="logout.php" method="post" style="display:inline;">
                    <button type="submit" class="btn-logout" style="display:flex;align-items:center;gap:6px;background:#fff;color:#e63946;border:none;padding:7px 18px;border-radius:18px;font-weight:bold;font-size:1rem;cursor:pointer;transition:background 0.2s;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24"><path fill="#e63946" d="M16.293 7.293a1 1 0 0 1 1.414 1.414L15.414 11H21a1 1 0 1 1 0 2h-5.586l2.293 2.293a1 1 0 0 1-1.414 1.414l-4-4a1 1 0 0 1 0-1.414l4-4z"/><path fill="#e63946" d="M13 3a1 1 0 0 1 1 1v2a1 1 0 1 1-2 0V5H7a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h5v-1a1 1 0 1 1 2 0v2a1 1 0 0 1-1 1H7a3 3 0 0 1-3-3V6a3 3 0 0 1 3-3h6z"/></svg>
                        Sair
                    </button>
                </form>
            </span>
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
                            <?php if ($pedido['status'] === 'preparando'): ?>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="id_status" value="<?php echo $pedido['id']; ?>">
                                    <select name="novo_status" style="padding:6px 16px; border-radius:8px; border:1px solid #ccc; min-width:170px; font-size:1rem; background:#f8f8f8; margin-right:8px;">
                                        <option value="">---- Selecione ----</option>
                                        <option value="saiu pra entrega">Saiu pra Entrega</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary" style="width:auto; min-width:90px; margin-left:0; padding:7px 18px; font-size:1rem; border-radius:18px; line-height:1;">Alterar</button>
                                </form>
                            <?php elseif ($pedido['status'] === 'saiu pra entrega'): ?>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="id_status" value="<?php echo $pedido['id']; ?>">
                                    <select name="novo_status" style="padding:6px 16px; border-radius:8px; border:1px solid #ccc; min-width:170px; font-size:1rem; background:#f8f8f8; margin-right:8px;">
                                        <option value="">---- Selecione ----</option>
                                        <option value="entregue">Entregue</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary" style="width:auto; min-width:90px; margin-left:0; padding:7px 18px; font-size:1rem; border-radius:18px; line-height:1;">Alterar</button>
                                </form>
                            <?php elseif ($pedido['status'] === 'pendente'): ?>
                                <span title="Pendente" style="font-size:1.3em;">⏳</span>
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