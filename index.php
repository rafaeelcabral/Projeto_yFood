<?php
session_start();
require_once 'config/database.php';

// Buscar produtos do banco de dados
try {
    $stmt = $pdo->query("SELECT * FROM produtos ORDER BY tipo, nome");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $produtos = [];
}

// Organizar produtos por categoria
$categorias = [
    'Combo' => [],
    'Hambúrguer' => [],
    'Batata' => [],
    'Acompanhamento' => [],
    'Bebida' => []
];

foreach ($produtos as $produto) {
    if (isset($categorias[$produto['tipo']])) {
        $categorias[$produto['tipo']][] = $produto;
    }
}

// Calcular total do carrinho
$total_carrinho = 0;
if (isset($_SESSION['carrinho']) && !empty($_SESSION['carrinho'])) {
    foreach ($_SESSION['carrinho'] as $item) {
        $total_carrinho += $item['preco'] * $item['quantidade'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>yFood - Lanchonete</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <h1>🍔 yFood</h1>
            </div>
            <nav class="nav">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a>
                <?php elseif ($total_carrinho == 0): ?>
                    <a href="login.php" class="nav-link">
                        <i class="fas fa-user"></i> Login
                    </a>
                <?php endif; ?>
                
                <?php if ($total_carrinho > 0): ?>
                    <a href="carrinho.php" class="nav-link carrinho">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="carrinho-total">R$ <?= number_format($total_carrinho, 2, ',', '.') ?></span>
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h2>Bem-vindo ao yFood!</h2>
            <p>Os melhores lanches da cidade com qualidade e sabor incomparáveis</p>
        </div>
    </section>

    <!-- Menu Sections -->
    <main class="main-content">
        <div class="container">
            <?php foreach ($categorias as $categoria => $produtos_categoria): ?>
                <?php if (!empty($produtos_categoria)): ?>
                    <section class="menu-section">
                        <h2 class="section-title"><?= $categoria ?>s</h2>
                        <div class="produtos-grid">
                            <?php foreach ($produtos_categoria as $produto): ?>
                                <div class="produto-card">
                                    <div class="produto-imagem">
                                        <?php if ($produto['imagem']): ?>
                                            <img src="<?= htmlspecialchars($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
                                        <?php else: ?>
                                            <div class="placeholder-imagem">
                                                <i class="fas fa-utensils"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="produto-info">
                                        <h3><?= htmlspecialchars($produto['nome']) ?></h3>
                                        <p class="preco">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                                        <button class="btn-adicionar" onclick="adicionarAoCarrinho(<?= $produto['id'] ?>, '<?= htmlspecialchars($produto['nome']) ?>', <?= $produto['preco'] ?>)">
                                            <i class="fas fa-plus"></i> Adicionar
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            <?php endforeach; ?>
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