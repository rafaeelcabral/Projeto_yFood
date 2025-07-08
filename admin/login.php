<?php
    session_start();
    if (isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === true) {
        header('Location: index.php');
        exit;
    }
    require_once '../config/database.php';
    $erro = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $senha = $_POST['senha'] ?? '';
        $stmt = $pdo->prepare('SELECT * FROM usuarios_admin WHERE username = ?');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        if ($admin && password_verify($senha, $admin['senha'])) {
            $_SESSION['admin_logado'] = true;
            $_SESSION['admin_nome'] = $admin['nome'];
            $_SESSION['admin_id'] = $admin['id'];
            header('Location: index.php');
            exit;
        } else {
            $erro = 'Usuário ou senha inválidos!';
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - yFood</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin-login.css">
</head>
<body>
    <div class="login-container">
        <img src="../assets/img/logo.png" alt="Logo" width="120">
        <h2>Login Administrador</h2>
        <?php if ($erro): ?><div class="erro"><?php echo $erro; ?></div><?php endif; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Usuário" required autofocus>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html> 