<?php
    session_start();
    require_once 'config/database.php';

    // Verificar se há itens no carrinho
    if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
        header('Location: index.php');
        exit;
    }

    // Calcular total
    $total = 0;
    foreach ($_SESSION['carrinho'] as $item) {
        $total += $item['preco'] * $item['quantidade'];
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho - yFood</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            <div class="carrinho-container">
                <div class="carrinho-header">
                    <h2><i class="fas fa-shopping-cart"></i> Seu Carrinho</h2>
                </div>
                
                <?php foreach ($_SESSION['carrinho'] as $id => $item): ?>
                    <div class="carrinho-item">
                        <div class="item-info">
                            <div class="item-nome"><?= htmlspecialchars($item['nome']) ?></div>
                            <div class="item-preco">R$ <?= number_format($item['preco'], 2, ',', '.') ?> cada</div>
                        </div>
                        
                        <div class="quantidade-controle">
                            <button class="btn-quantidade" onclick="atualizarQuantidade(<?= $id ?>, 'remover')">
                                <i class="fas fa-minus"></i>
                            </button>
                            <span style="font-weight: bold; min-width: 30px; text-align: center;">
                                <?= $item['quantidade'] ?>
                            </span>
                            <button class="btn-quantidade" onclick="atualizarQuantidade(<?= $id ?>, 'adicionar')">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        
                        <div style="text-align: right; margin-left: 1rem;">
                            <div style="font-weight: bold; color: #d32f2f;">
                                R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?>
                            </div>
                            <button onclick="removerDoCarrinho(<?= $id ?>)" 
                                    style="background: #dc3545; color: white; border: none; padding: 0.25rem 0.5rem; border-radius: 4px; cursor: pointer; font-size: 0.8rem; margin-top: 0.25rem;">
                                <i class="fas fa-trash"></i> Remover
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="carrinho-total-final">
                    <strong>Total: R$ <?= number_format($total, 2, ',', '.') ?></strong>
                </div>
                
                <div style="padding: 1.5rem; text-align: center;">
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <a href="checkout.php" class="btn" style="display: inline-block; text-decoration: none; margin-right: 2rem;">
                            <i class="fas fa-credit-card"></i> Finalizar Pedido
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn" style="display: inline-block; text-decoration: none; margin-right: 2rem;">
                            <i class="fas fa-sign-in-alt"></i> Fazer Login para Continuar
                        </a>
                    <?php endif; ?> <br><br>
                    
                    <a href="index.php" class="btn btn-secondary" style="display: inline-block; text-decoration: none;">
                        <i class="fas fa-arrow-left"></i> Continuar Comprando
                    </a>
                </div>
            </div>
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