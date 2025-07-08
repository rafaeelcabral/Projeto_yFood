<?php
    session_start();
    if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
        header('Location: login.php');
        exit;
    }
    require_once '../config/database.php';
    $id_filtro = $_GET['id'] ?? '';
    $nome_filtro = $_GET['nome'] ?? '';
    $sql = "SELECT * FROM usuarios WHERE tipo = 'cliente'";
    $params = [];
    if ($id_filtro !== '') {
        $sql .= ' AND id = ?';
        $params[] = $id_filtro;
    }
    if ($nome_filtro !== '') {
        $sql .= ' AND nome LIKE ?';
        $params[] = '%' . $nome_filtro . '%';
    }
    $sql .= ' ORDER BY id DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $clientes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Clientes - Admin yFood</title>
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
        .filtros input { padding: 6px 10px; border-radius: 4px; border: 1px solid #ccc; }
        .filtros button { background: #e63946; color: #fff; border: none; padding: 8px 16px; border-radius: 4px; font-weight: bold; cursor: pointer; }
        .clientes-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #0001; }
        .clientes-table th, .clientes-table td { padding: 8px 12px; font-size: 1rem; border-bottom: 1px solid #eee; text-align: left; }
        .clientes-table th { background: #f1faee; }
        .clientes-table td { vertical-align: middle; }
    </style>
</head>
<body>
<div class="admin-container">
    <nav class="sidebar">
        <img src="../assets/img/logo.png" alt="Logo" width="100">
        <a href="index.php">Dashboard</a>
        <a href="produtos.php">Produtos</a>
        <a href="pedidos.php">Pedidos</a>
        <a href="clientes.php" class="active">Clientes</a>
    </nav>
    <div class="content">
        <div class="header">
            <span>Clientes</span>
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
                <input type="text" name="id" placeholder="ID do cliente" value="<?php echo htmlspecialchars($id_filtro); ?>">
                <input type="text" name="nome" placeholder="Nome do cliente" value="<?php echo htmlspecialchars($nome_filtro); ?>">
                <button type="submit">Filtrar</button>
            </form>
            <table class="clientes-table">
                <thead>
                    <tr><th>ID</th><th>Nome</th><th>Email</th><th>Data de Cadastro</th></tr>
                </thead>
                <tbody>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?php echo $cliente['id']; ?></td>
                        <td><?php echo htmlspecialchars($cliente['nome']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($cliente['data_criacao'])); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($clientes)): ?>
                    <tr><td colspan="4" style="text-align:center;">Nenhum cliente encontrado.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html> 