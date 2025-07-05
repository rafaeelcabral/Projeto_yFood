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
            'pronto' => 'Pronto',
            'entregue' => 'Entregue',
            'cancelado' => 'Cancelado'
        ];
        return $status_map[$status] ?? $status;
    }

    // Função para obter a cor do status
    function getStatusColor($status) {
        $color_map = [
            'pendente' => '#ff9800',
            'preparando' => '#2196f3',
            'pronto' => '#4caf50',
            'entregue' => '#4caf50',
            'cancelado' => '#f44336'
        ];
        return $color_map[$status] ?? '#666';
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Pedidos - yFood</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
        }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1 0 auto;
        }
        .footer {
            flex-shrink: 0;
        }
        .historico-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 0;
        }
        
        .historico-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .historico-header h1 {
            color: #d32f2f;
            margin-bottom: 0.5rem;
        }
        
        .historico-header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .pedido-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        
        .pedido-header {
            background: linear-gradient(135deg, #d32f2f, #b71c1c);
            color: white;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .pedido-header:hover {
            background: linear-gradient(135deg, #b71c1c, #8e0000);
        }
        
        .pedido-header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .pedido-numero {
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .pedido-data {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .pedido-status {
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
        }
        
        .dropdown-icon {
            font-size: 1.2rem;
            transition: transform 0.3s;
        }
        
        .dropdown-icon.expanded {
            transform: rotate(180deg);
        }
        
        .pedido-details {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            background: #f9f9f9;
        }
        
        .pedido-details.expanded {
            max-height: 1000px;
            transition: max-height 0.5s ease-in;
        }
        
        .pedido-info {
            padding: 1.5rem;
        }
        
        .pedido-total {
            font-size: 1.3rem;
            font-weight: bold;
            color: #d32f2f;
            margin-bottom: 1rem;
        }
        
        .itens-lista {
            margin-top: 1rem;
        }
        
        .item-pedido {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .item-pedido:last-child {
            border-bottom: none;
        }
        
        .item-info {
            flex: 1;
        }
        
        .item-nome {
            font-weight: bold;
            color: #333;
        }
        
        .item-detalhes {
            color: #666;
            font-size: 0.9rem;
        }
        
        .item-preco {
            font-weight: bold;
            color: #d32f2f;
        }
        
        .sem-pedidos {
            text-align: center;
            padding: 4rem 2rem;
            color: #666;
        }
        
        .sem-pedidos i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 1rem;
        }
        
        .voltar-btn {
            display: inline-block;
            background: #d32f2f;
            color: white;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 1rem;
            transition: background 0.3s;
        }
        
        .voltar-btn:hover {
            background: #b71c1c;
        }
        
        @media (max-width: 768px) {
            .pedido-header {
                flex-direction: column;
                text-align: center;
            }
            
            .pedido-header-left {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .item-pedido {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <!--<h1>🍔 yFood</h1>-->
                <img src="assets/img/logo.png" alt="yFood" width="150" height="80">
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
                                <div class="pedido-status" style="background-color: <?= getStatusColor($pedido['status']) ?>;">
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