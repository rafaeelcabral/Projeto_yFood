<?php
    session_start();
    require_once 'config/database.php';

    // Verificar se está logado
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit;
    }

    // Buscar pedidos do usuário
    try {
        $stmt = $pdo->prepare("
            SELECT p.*, 
                COUNT(ip.id) as total_itens
            FROM pedidos p 
            LEFT JOIN itens_pedido ip ON p.id = ip.pedido_id
            WHERE p.usuario_id = ?
            GROUP BY p.id
            ORDER BY p.data_pedido DESC
        ");
        $stmt->execute([$_SESSION['usuario_id']]);
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $pedidos = [];
    }

    // Função para obter o status em português
    function getStatusText($status) {
        $status_map = [
            'pendente' => 'Pendente',
            'preparando' => 'Preparando',
            'saiu pra entrega' => 'Saiu pra Entrega',
            'entregue' => 'Entregue',
            'cancelado' => 'Cancelado'
        ];
        return $status_map[$status] ?? $status;
    }

    // Função para obter a cor do status
    function getStatusColor($status) {
        $color_map = [
            'pendente' => '#ffeb3b', // amarelo
            'preparando' => '#ff9800', // laranja
            'saiu pra entrega' => '#2196f3', // azul
            'entregue' => '#4caf50', // verde
            'cancelado' => '#f44336' // vermelho
        ];
        return $color_map[$status] ?? '#666';
    }

    function statusClass($status) {
        $map = [
            'pendente' => 'pendente',
            'preparando' => 'preparando',
            'saiu pra entrega' => 'saiu-pra-entrega',
            'entregue' => 'entregue',
            'cancelado' => 'cancelado'
        ];
        return isset($map[$status]) ? $map[$status] : 'pendente';
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Pedidos - yFood</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/historico.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <!--<h1>🍔 yFood</h1>-->
                <img src="assets/img/logo2.png" alt="Logo" width="120">
            </div>
            <nav class="nav">
                <a href="index.php" class="nav-link">
                    <i class="fas fa-home"></i> Menu
                </a>
                <a href="logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="historico-container">
            <div class="historico-header">
                <h1><i class="fas fa-history"></i> Histórico de Pedidos</h1>
                <p>Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>! Aqui estão todos os seus pedidos.</p>
            </div>

            <?php if (empty($pedidos)): ?>
                <div class="sem-pedidos">
                    <i class="fas fa-receipt"></i>
                    <h3>Nenhum pedido encontrado</h3>
                    <p>Você ainda não fez nenhum pedido. Que tal experimentar nossos deliciosos lanches?</p>
                    <br>
                    <a href="index.php" class="voltar-btn">
                        <i class="fas fa-utensils"></i> 
                        <br>
                        Ver Menu
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($pedidos as $pedido): ?>
                    <div class="pedido-card">
                        <div class="pedido-header" onclick="togglePedido(<?= $pedido['id'] ?>)">
                            <div class="pedido-header-left">
                                <div>
                                    <div class="pedido-numero">Pedido #<?= $pedido['id'] ?></div>
                                    <div class="pedido-data">
                                        <i class="fas fa-calendar"></i>
                                        <?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div class="pedido-status status-<?= statusClass($pedido['status']) ?>">
                                    <?= getStatusText($pedido['status']) ?>
                                </div>
                                <i class="fas fa-chevron-down dropdown-icon" id="icon-<?= $pedido['id'] ?>"></i>
                            </div>
                        </div>
                        
                        <div class="pedido-details" id="details-<?= $pedido['id'] ?>">
                            <div class="pedido-info">
                                <div class="pedido-total">
                                    Total: R$ <?= number_format($pedido['total'], 2, ',', '.') ?>
                                </div>
                                
                                <div class="itens-lista">
                                    <h4><i class="fas fa-list"></i> Itens do Pedido:</h4>
                                    <?php
                                    // Buscar itens deste pedido
                                    try {
                                        $stmt = $pdo->prepare("
                                            SELECT ip.*, p.nome as produto_nome
                                            FROM itens_pedido ip
                                            LEFT JOIN produtos p ON ip.produto_id = p.id
                                            WHERE ip.pedido_id = ?
                                            ORDER BY p.nome
                                        ");
                                        $stmt->execute([$pedido['id']]);
                                        $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    } catch (PDOException $e) {
                                        $itens = [];
                                    }
                                    ?>
                                    
                                    <?php foreach ($itens as $item): ?>
                                        <div class="item-pedido">
                                            <div class="item-info">
                                                <div class="item-nome"><?= htmlspecialchars($item['produto_nome']) ?></div>
                                                <div class="item-detalhes">
                                                    Quantidade: <?= $item['quantidade'] ?> | 
                                                    Preço unitário: R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?>
                                                </div>
                                            </div>
                                            <div class="item-preco">
                                                R$ <?= number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.') ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 yFood. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
    <script>
        function togglePedido(pedidoId) {
            const details = document.getElementById('details-' + pedidoId);
            const icon = document.getElementById('icon-' + pedidoId);
            
            if (details.classList.contains('expanded')) {
                details.classList.remove('expanded');
                icon.classList.remove('expanded');
            } else {
                details.classList.add('expanded');
                icon.classList.add('expanded');
            }
        }
    </script>
</body>
</html> 