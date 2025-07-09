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
            <!-- Modal de cadastro de produto -->
            <div id="modal-cadastro" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);align-items:center;justify-content:center;z-index:1000;">
              <div style="background:#fff;padding:40px 36px 32px 36px;border-radius:16px;min-width:400px;max-width:95vw;position:relative;box-shadow:0 8px 32px rgba(0,0,0,0.18);">
                <button id="fechar-modal-cadastro" style="position:absolute;top:12px;right:18px;font-size:1.6rem;background:none;border:none;cursor:pointer;">&times;</button>
                <h2 style="margin-top:0;font-size:2rem;">Cadastrar Produto</h2>
                <form id="form-cadastro-produto">
                  <div style="margin-bottom:20px;">
                    <label style="font-size:1.1rem;">Nome:<br><input type="text" name="nome" required style="width:100%;padding:8px 10px;font-size:1.1rem;margin-top:4px;"></label>
                  </div>
                  <div style="margin-bottom:20px;">
                    <label style="font-size:1.1rem;">Descrição:<br><textarea name="descricao" rows="3" style="width:100%;padding:8px 10px;font-size:1.1rem;margin-top:4px;resize:vertical;"></textarea></label>
                  </div>
                  <div style="margin-bottom:20px;">
                    <label style="font-size:1.1rem;">Tipo:<br>
                      <select name="tipo" required style="width:100%;padding:8px 10px;font-size:1.1rem;margin-top:4px;">
                        <option value="">Selecione</option>
                        <?php foreach ($tipos as $tipo): ?>
                          <option value="<?php echo $tipo; ?>"><?php echo $tipo; ?></option>
                        <?php endforeach; ?>
                      </select>
                    </label>
                  </div>
                  <div style="margin-bottom:20px;">
                    <label style="font-size:1.1rem;">Preço:<br><input type="number" name="preco" step="0.01" min="0" required style="width:100%;padding:8px 10px;font-size:1.1rem;margin-top:4px;"></label>
                  </div>
                  <div style="margin-bottom:24px;">
                    <label style="font-size:1.1rem;">Disponível:
                      <select name="disponivel" required style="width:100%;padding:8px 10px;font-size:1.1rem;margin-top:4px;">
                        <option value="1">Sim</option>
                        <option value="0">Não</option>
                      </select>
                    </label>
                  </div>
                  <button type="submit" style="background:#457b9d;color:#fff;padding:12px 32px;border:none;border-radius:8px;font-weight:bold;font-size:1.1rem; cursor: pointer">Cadastrar</button>
                </form>
              </div>
            </div>
            <table class="produtos-table">
                <thead>
                    <tr><th>ID</th><th>Nome</th><th>Tipo</th><th>Preço</th><th>Disponível</th><th>Ações</th></tr>
                </thead>
                <tbody>
                <?php foreach ($produtos as $produto): ?>
                    <tr id="produto-<?php echo $produto['id']; ?>">
                        <td><?php echo $produto['id']; ?></td>
                        <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                        <td><?php echo $produto['tipo']; ?></td>
                        <td>R$ <?php echo number_format($produto['preco'],2,',','.'); ?></td>
                        <td><?php echo $produto['disponivel'] ? 'Sim' : 'Não'; ?></td>
                        <td><button class="remover-btn styled-remover-btn" data-id="<?php echo $produto['id']; ?>" title="Remover" style="cursor: pointer">
  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#e63946" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
</button></td>
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
<script src="../assets/js/script.js"></script>
</html> 