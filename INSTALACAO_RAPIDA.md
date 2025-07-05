# 🚀 Instalação Rápida - yFood

## ⚡ Passos para Instalar (5 minutos)

### 1. Preparar XAMPP
- ✅ Baixe e instale o XAMPP
- ✅ Inicie Apache e MySQL
- ✅ Acesse: `http://localhost/phpmyadmin`

### 2. Criar Banco de Dados
**Opção A - Automática (Recomendada):**
- Copie todos os arquivos para: `C:\xampp\htdocs\yfood\`
- Acesse: `http://localhost/yfood/`
- O sistema criará o banco automaticamente

**Opção B - Manual:**
- No phpMyAdmin, crie banco: `raf-projeto_yFood`
- Importe o arquivo: `database_setup.sql`

### 3. Acessar o Sistema
- 🌐 URL: `http://localhost/yfood/`
- 👤 Cadastro: Clique em "Login" → "Cadastrar"

## 🎯 Funcionalidades Principais

### Para Clientes:
- 🍔 **Menu**: Produtos organizados por categoria
- 🛒 **Carrinho**: Adicionar/remover produtos
- 🔐 **Login**: Sistema de autenticação
- 💳 **Checkout**: Finalizar pedidos

### Para Clientes:
- 👤 **Cadastro**: Criar conta pessoal
- 🔐 **Login**: Autenticação segura
- 📋 **Histórico**: Ver pedidos realizados

## 🔧 Configurações (se necessário)

Edite `config/database.php` se suas credenciais forem diferentes:
```php
$host = 'localhost';
$dbname = 'RAF_ProjetoFoodCursor';
$username = 'root';  // Seu usuário MySQL
$password = '';      // Sua senha MySQL
```

## 🎨 Características Visuais

- 🎨 **Estilo**: Inspirado no McDonald's
- 🌈 **Cores**: Vermelho (#d32f2f) e branco
- 📱 **Responsivo**: Funciona em todos os dispositivos
- ✨ **Animações**: Efeitos suaves e modernos

## 🛒 Como Usar

1. **Navegar**: Explore o menu na página inicial
2. **Adicionar**: Clique "Adicionar" nos produtos
3. **Carrinho**: Gerencie itens no carrinho
4. **Login**: Faça login ou cadastre-se
5. **Checkout**: Finalize o pedido

## 🔐 Segurança

- ✅ Senhas criptografadas
- ✅ Proteção contra SQL Injection
- ✅ Validação de dados
- ✅ Headers de segurança
- ✅ Upload seguro de imagens

## 📁 Estrutura do Projeto

```
yfood/
├── index.php              # Página inicial
├── login.php              # Login
├── carrinho.php           # Carrinho
├── checkout.php           # Finalização
├── config/database.php    # Configuração DB
├── cadastro.php           # Cadastro de clientes
├── ajax/                  # Requisições AJAX
├── assets/                # CSS e JavaScript
└── uploads/               # Imagens (criada automaticamente)
```

## 🐛 Problemas Comuns

### Erro de Conexão:
- ✅ MySQL rodando?
- ✅ Credenciais corretas?
- ✅ Banco criado?

### Upload não Funciona:
- ✅ Pasta uploads/ existe?
- ✅ Permissões de escrita?
- ✅ Tamanho do arquivo?

### Página não Carrega:
- ✅ Apache rodando?
- ✅ Arquivos na pasta correta?
- ✅ URL correta?

## 📞 Suporte

- 📖 Leia o `README.md` completo
- 🔍 Verifique logs de erro
- 🧪 Teste em ambiente limpo

---

**🎉 Pronto! Seu sistema yFood está funcionando!** 