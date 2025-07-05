<?php
session_start();
require_once 'config/database.php';

$erro = '';

// Se já estiver logado, redirecionar
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

// Processar formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $senha = $_POST['senha'] ?? '';
    
    if (empty($username) || empty($senha)) {
        $erro = 'Por favor, preencha todos os campos.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, senha, nome, tipo FROM usuarios WHERE username = ?");
            $stmt->execute([$username]);
            $usuario = $stmt->fetch();
            
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                // Login bem-sucedido
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_tipo'] = $usuario['tipo'];
                
                // Redirecionar para a página inicial
                header('Location: index.php');
                exit;
            } else {
                $erro = 'Usuário ou senha incorretos.';
            }
        } catch (PDOException $e) {
            $erro = 'Erro ao fazer login. Tente novamente.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - yFood</title>
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
                <a href="index.php" class="nav-link">
                    <i class="fas fa-home"></i> Voltar ao Menu
                </a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="form-container">
                <h2 class="form-title">Login</h2>
                
                <?php if ($erro): ?>
                    <div class="alert alert-error">
                        <?= htmlspecialchars($erro) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label for="username">Usuário:</label>
                        <input type="text" id="username" name="username" required 
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                               placeholder="Digite seu usuário">
                    </div>
                    
                    <div class="form-group">
                        <label for="senha">Senha:</label>
                        <div style="position: relative;">
                            <input type="password" id="senha" name="senha" required 
                                   placeholder="Digite sua senha">
                            <button type="button" onclick="alternarSenha('senha')" 
                                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn">
                        <i class="fas fa-sign-in-alt"></i> Entrar
                    </button>
                </form>
                
                <div style="text-align: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #eee;">
                    <a href="cadastro.php" class="btn-warning">
                        <i class="fas fa-user-plus"></i> Cadastrar
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