<?php
session_start();
require_once 'config/database.php';

$erro = '';
$sucesso = '';

// Se já estiver logado, redirecionar
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

// Processar formulário de cadastro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    // Validações
    if (empty($username) || empty($senha) || empty($confirmar_senha) || empty($nome)) {
        $erro = 'Por favor, preencha todos os campos obrigatórios.';
    } elseif (strlen($username) < 3) {
        $erro = 'O nome de usuário deve ter pelo menos 3 caracteres.';
    } elseif (strlen($senha) < 4) {
        $erro = 'A senha deve ter pelo menos 4 caracteres.';
    } elseif ($senha !== $confirmar_senha) {
        $erro = 'As senhas não coincidem.';
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';
    } else {
        try {
            // Verificar se o username já existe
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE username = ?");
            $stmt->execute([$username]);
            
            if ($stmt->fetch()) {
                $erro = 'Este nome de usuário já está em uso.';
            } else {
                // Verificar se o email já existe (se fornecido)
                if (!empty($email)) {
                    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
                    $stmt->execute([$email]);
                    
                    if ($stmt->fetch()) {
                        $erro = 'Este e-mail já está em uso.';
                    }
                }
                
                if (empty($erro)) {
                    // Criar novo usuário
                    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO usuarios (username, senha, nome, email, tipo) VALUES (?, ?, ?, ?, 'cliente')");
                    $stmt->execute([$username, $senha_hash, $nome, $email]);
                    
                    $sucesso = 'Cadastro realizado com sucesso! Você já pode fazer login.';
                    
                    // Limpar formulário
                    $_POST = [];
                }
            }
        } catch (PDOException $e) {
            $erro = 'Erro ao realizar cadastro. Tente novamente.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - yFood</title>
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
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="form-container">
                <h2 class="form-title">Cadastro de Cliente</h2>
                
                <?php if ($sucesso): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($sucesso) ?>
                    </div>
                    <div style="text-align: center; margin-top: 2rem;">
                        <a href="login.php" class="btn">
                            <i class="fas fa-sign-in-alt"></i> Fazer Login
                        </a>
                    </div>
                <?php else: ?>
                    <?php if ($erro): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($erro) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="cadastro.php">
                        <div class="form-group">
                            <label for="nome">Nome Completo: *</label>
                            <input type="text" id="nome" name="nome" required 
                                   value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
                                   placeholder="Digite seu nome completo">
                        </div>
                        
                        <div class="form-group">
                            <label for="username">Nome de Usuário: *</label>
                            <input type="text" id="username" name="username" required 
                                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                                   placeholder="Digite um nome de usuário">
                            <small style="color: #666;">Mínimo 3 caracteres</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">E-mail:</label>
                            <input type="email" id="email" name="email" 
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                   placeholder="Digite seu e-mail (opcional)">
                        </div>
                        
                        <div class="form-group">
                            <label for="senha">Senha: *</label>
                            <div style="position: relative;">
                                <input type="password" id="senha" name="senha" required 
                                       placeholder="Digite sua senha">
                                <button type="button" onclick="alternarSenha('senha')" 
                                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small style="color: #666;">Mínimo 4 caracteres</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirmar_senha">Confirmar Senha: *</label>
                            <div style="position: relative;">
                                <input type="password" id="confirmar_senha" name="confirmar_senha" required 
                                       placeholder="Confirme sua senha">
                                <button type="button" onclick="alternarSenha('confirmar_senha')" 
                                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn">
                            <i class="fas fa-user-plus"></i> Criar Conta
                        </button>
                    </form>
                    
                    <div style="text-align: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #eee;">
                        <p>Já tem uma conta?</p>
                        <a href="login.php" class="btn btn-secondary" style="display: inline-block; text-decoration: none;">
                            <i class="fas fa-sign-in-alt"></i> Fazer Login
                        </a>
                    </div>
                <?php endif; ?>
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