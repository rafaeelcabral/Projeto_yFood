<?php
    session_start();
    require_once 'config/database.php';

    // Verificar se está logado
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit;
    }

    // Verificar se há itens no carrinho
    if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
        header('Location: index.php');
        exit;
    }

    $sucesso = '';
    $erro = '';

    // Processar finalização do pedido
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Calcular total
            $total = 0;
            foreach ($_SESSION['carrinho'] as $item) {
                $total += $item['preco'] * $item['quantidade'];
            }
            
            // Inserir pedido
            $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, total) VALUES (?, ?)");
            $stmt->execute([$_SESSION['usuario_id'], $total]);
            $pedido_id = $pdo->lastInsertId();
            
            // Inserir itens do pedido
            $stmt = $pdo->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
            foreach ($_SESSION['carrinho'] as $produto_id => $item) {
                $stmt->execute([$pedido_id, $produto_id, $item['quantidade'], $item['preco']]);
            }
            
            // Limpar carrinho
            unset($_SESSION['carrinho']);
            
            $sucesso = "Pedido realizado com sucesso! Número do pedido: #$pedido_id";
            
        } catch (PDOException $e) {
            $erro = 'Erro ao finalizar pedido. Tente novamente.';
        }
    }

    // Calcular total atual
    $total = 0;
    if (isset($_SESSION['carrinho']) && !empty($_SESSION['carrinho'])) {
        foreach ($_SESSION['carrinho'] as $item) {
            $total += $item['preco'] * $item['quantidade'];
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - yFood</title>
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
                    <i class="fas fa-home"></i> Voltar ao Menu
                </a>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <?php if ($sucesso): ?>
                <div class="form-container">
                    <div class="alert alert-success">
                        <h3><i class="fas fa-check-circle"></i> <?= htmlspecialchars($sucesso) ?></h3>
                        <p>Seu pedido foi registrado com sucesso e está sendo preparado!</p>
                    </div>
                    <div style="text-align: center; margin-top: 2rem;">
                        <a href="index.php" class="btn">
                            <i class="fas fa-home"></i> Voltar ao Menu
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; max-width: 1000px; margin: 0 auto;" class="checkout-grid">
                    <!-- Resumo do Pedido -->
                    <div class="carrinho-container">
                        <div class="carrinho-header">
                            <h2><i class="fas fa-list"></i> Resumo do Pedido</h2>
                        </div>
                        
                        <?php if (isset($_SESSION['carrinho']) && !empty($_SESSION['carrinho'])): ?>
                            <?php foreach ($_SESSION['carrinho'] as $id => $item): ?>
                                <div class="carrinho-item">
                                    <div class="item-info">
                                        <div class="item-nome"><?= htmlspecialchars($item['nome']) ?></div>
                                        <div class="item-preco">R$ <?= number_format($item['preco'], 2, ',', '.') ?> cada</div>
                                    </div>
                                    
                                    <div style="text-align: right;">
                                        <div style="font-weight: bold; color: #d32f2f;">
                                            R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?>
                                        </div>
                                        <small style="color: #666;">Qtd: <?= $item['quantidade'] ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="carrinho-item">
                                <div style="text-align: center; color: #666;">
                                    <p>Nenhum item no carrinho</p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="carrinho-total-final">
                            <strong>Total: R$ <?= number_format($total, 2, ',', '.') ?></strong>
                        </div>
                    </div>
                    
                    <!-- Formulário de Finalização -->
                    <div class="form-container">
                        <h2 class="form-title">Finalizar Pedido</h2>
                        
                        <?php if ($erro): ?>
                            <div class="alert alert-error">
                                <?= htmlspecialchars($erro) ?>
                            </div>
                        <?php endif; ?>
                        
                        <div style="margin-bottom: 2rem;">
                            <h3>Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!</h3>
                            <p>Confirme seu pedido e clique em "Finalizar Pedido" para concluir sua compra.</p>
                        </div>
                        
                        <form method="POST" action="checkout.php">
                            <div class="form-group">
                                <label>Nome:</label>
                                <input type="text" value="<?= htmlspecialchars($_SESSION['usuario_nome']) ?>" readonly 
                                       style="background-color: #f8f8f8;">
                            </div>
                            
                            <div class="form-group">
                                <label>Total do Pedido:</label>
                                <input type="text" value="R$ <?= number_format($total, 2, ',', '.') ?>" readonly 
                                       style="background-color: #f8f8f8; font-weight: bold; color: #d32f2f;">
                            </div>
                            
                            <div class="form-group">
                                <label for="observacoes">Observações (opcional):</label>
                                <textarea id="observacoes" name="observacoes" rows="3" 
                                          placeholder="Alguma observação especial para seu pedido?"></textarea>
                            </div>
                            
                            <button type="submit" class="btn">
                                <i class="fas fa-check"></i> Finalizar Pedido
                            </button>
                        </form>
                        
                        <div style="text-align: center; margin-top: 1rem;">
                            <a href="carrinho.php" class="btn btn-secondary" style="display: inline-block; text-decoration: none;">
                                <i class="fas fa-arrow-left"></i> Voltar ao Carrinho
                            </a>
                        </div>
                    </div>
                </div>
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
</body>
</html> 