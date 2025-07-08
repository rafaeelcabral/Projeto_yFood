<?php
    session_start();
    if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
        header('Location: login.php');
        exit;
    }
    require_once '../config/database.php';
    // Filtros
    $id_filtro = $_GET['id'] ?? '';
    $tipo_filtro = $_GET['tipo'] ?? '';
    // Buscar tipos de produto
    $tipos = ['Combo', 'Hambúrguer', 'Batata', 'Acompanhamento', 'Bebida'];
    // Montar query
    $sql = 'SELECT * FROM produtos WHERE 1=1';
    $params = [];
    if ($id_filtro !== '') {
        $sql .= ' AND id = ?';
        $params[] = $id_filtro;
    }
    if ($tipo_filtro !== '') {
        $sql .= ' AND tipo = ?';
        $params[] = $tipo_filtro;
    }
    $sql .= ' ORDER BY id DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $produtos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Produtos - Admin yFood</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin-base.css">
    <link rel="stylesheet" href="../assets/css/admin-inline.css">
</head>
<body>
<div class="admin-container">
    <nav class="sidebar">
        <img src="../assets/img/logo.png" alt="Logo" width="100">
        <a href="index.php">Dashboard</a>
        <a href="produtos.php" class="active">Produtos</a>
        <a href="pedidos.php">Pedidos</a>
        <a href="clientes.php">Clientes</a>
    </nav>
    <div class="content">
        <div class="header">
            <span>Produtos</span>
            <span>
    <form action="logout.php" method="post" style="display:inline;">
        <button type="submit" class="btn-logout" style="display:flex;align-items:center;gap:6px;background:#fff;color:#e63946;border:none;padding:7px 18px;border-radius:18px;font-weight:bold;font-size:1rem;cursor:pointer;transition:background 0.2s;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24"><path fill="#e63946" d="M16.293 7.293a1 1 0 0 1 1.414 1.414L15.414 11H21a1 1 0 1 1 0 2h-5.586l2.293 2.293a1 1 0 0 1-1.414 1.414l-4-4a1 1 0 0 1 0-1.414l4-4z"/><path fill="#e63946" d="M13 3a1 1 0 0 1 1 1v2a1 1 0 1 1-2 0V5H7a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h5v-1a1 1 0 1 1 2 0v2a1 1 0 0 1-1 1H7a3 3 0 0 1-3-3V6a3 3 0 0 1 3-3h6z"/></svg>
            Sair
        </button>
    </form>
</span>
        </div>
        <div style="padding:32px;">
            <form class="filtros" method="get">
                <input type="text" name="id" placeholder="ID do produto" value="<?php echo htmlspecialchars($id_filtro); ?>">
                <select name="tipo">
                    <option value="">Tipo do produto</option>
                    <?php foreach ($tipos as $tipo): ?>
                        <option value="<?php echo $tipo; ?>" <?php if ($tipo_filtro === $tipo) echo 'selected'; ?>><?php echo $tipo; ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Filtrar</button>
                <button type="button" class="novo-btn">Cadastrar Novo</button>
            </form>
            <table class="produtos-table">
                <thead>
                    <tr><th>ID</th><th>Nome</th><th>Tipo</th><th>Preço</th><th>Disponível</th></tr>
                </thead>
                <tbody>
                <?php foreach ($produtos as $produto): ?>
                    <tr>
                        <td><?php echo $produto['id']; ?></td>
                        <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                        <td><?php echo $produto['tipo']; ?></td>
                        <td>R$ <?php echo number_format($produto['preco'],2,',','.'); ?></td>
                        <td><?php echo $produto['disponivel'] ? 'Sim' : 'Não'; ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($produtos)): ?>
                    <tr><td colspan="5" style="text-align:center;">Nenhum produto encontrado.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html> 