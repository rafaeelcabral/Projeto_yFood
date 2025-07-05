# 🍔 yFood - Sistema de Lanchonete

Um sistema completo de lanchonete desenvolvido em PHP, MySQL, HTML, CSS e JavaScript, inspirado no estilo visual do McDonald's.

## 🚀 Funcionalidades

### Para Clientes:
- **Página Inicial**: Menu organizado por categorias (Combos, Hambúrgueres, Batatas, Acompanhamentos, Bebidas)
- **Carrinho de Compras**: Adicionar, remover e gerenciar quantidades de produtos
- **Sistema de Login**: Autenticação de usuários
- **Checkout**: Finalização de pedidos com confirmação

### Para Clientes:
- **Cadastro de Conta**: Criar conta pessoal no sistema
- **Login Seguro**: Autenticação com validação de dados
- **Histórico**: Visualizar pedidos realizados

## 🛠️ Tecnologias Utilizadas

- **Backend**: PHP 7.4+ com PDO
- **Banco de Dados**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Servidor**: Apache (XAMPP)
- **Ícones**: Font Awesome 6.0
- **Estilo**: Design responsivo inspirado no McDonald's

## 📋 Pré-requisitos

- XAMPP (Apache + MySQL + PHP)
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Navegador web moderno

## 🔧 Instalação

### 1. Configurar XAMPP
1. Baixe e instale o XAMPP
2. Inicie o Apache e MySQL
3. Acesse o phpMyAdmin: `http://localhost/phpmyadmin`

### 2. Criar Banco de Dados
1. No phpMyAdmin, crie um novo banco de dados chamado: `raf-projeto_yFood`
2. O sistema criará automaticamente as tabelas na primeira execução

### 3. Configurar Projeto
1. Copie todos os arquivos para a pasta: `C:\xampp\htdocs\yfood\`
2. Acesse: `http://localhost/yfood/`

### 4. Configurações do Banco (se necessário)
Edite o arquivo `config/database.php` se suas credenciais forem diferentes:
```php
$host = 'localhost';
$dbname = 'raf-projeto_yFood';
$username = 'root';  // Seu usuário MySQL
$password = '';      // Sua senha MySQL
```

## 👤 Sistema de Usuários

### Cadastro de Clientes:
- Os clientes podem criar suas próprias contas
- Validação de dados e verificação de duplicatas
- Senhas criptografadas com segurança

## 📁 Estrutura do Projeto

```
yfood/
├── index.php                 # Página inicial
├── login.php                 # Página de login
├── carrinho.php             # Página do carrinho
├── checkout.php             # Página de finalização
├── logout.php               # Logout
├── config/
│   └── database.php         # Configuração do banco
├── cadastro.php             # Página de cadastro de clientes
├── ajax/
│   ├── adicionar_carrinho.php
│   ├── atualizar_carrinho.php
│   └── remover_carrinho.php
├── assets/
│   ├── css/
│   │   └── style.css        # Estilos CSS
│   └── js/
│       └── script.js        # JavaScript
├── uploads/                 # Pasta para imagens (criada automaticamente)
└── README.md
```

## 🎨 Características Visuais

- **Paleta de Cores**: Vermelho (#d32f2f) e branco, inspirada no McDonald's
- **Design Responsivo**: Funciona em desktop, tablet e mobile
- **Animações**: Efeitos suaves e transições
- **Ícones**: Font Awesome para melhor experiência visual
- **Tipografia**: Fontes legíveis e modernas

## 🔐 Segurança

- **Senhas**: Hash com `password_hash()` e `password_verify()`
- **SQL Injection**: Protegido com PDO e prepared statements
- **XSS**: Escape de dados com `htmlspecialchars()`
- **Sessões**: Gerenciamento seguro de sessões
- **Upload de Arquivos**: Validação de tipos e tamanhos

## 📱 Funcionalidades do Carrinho

- **Adicionar Produtos**: Clique no botão "Adicionar" em qualquer produto
- **Gerenciar Quantidades**: Use os botões + e - no carrinho
- **Remover Itens**: Botão "Remover" para cada item
- **Cálculo Automático**: Total atualizado em tempo real
- **Persistência**: Carrinho mantido durante a sessão

## 🛒 Fluxo de Compra

1. **Navegar**: Explore o menu na página inicial
2. **Adicionar**: Clique em "Adicionar" nos produtos desejados
3. **Carrinho**: Visualize e gerencie itens no carrinho
4. **Login**: Faça login para continuar (se necessário)
5. **Checkout**: Confirme e finalize o pedido
6. **Confirmação**: Receba confirmação do pedido

## 🔧 Personalização

### Cadastro de Clientes:
1. Acesse a página de login
2. Clique no botão "Cadastrar" (amarelo)
3. Preencha os dados obrigatórios
4. Faça login com suas credenciais

### Modificar Estilos:
1. Edite o arquivo `assets/css/style.css`
2. As cores principais estão definidas como variáveis CSS

### Adicionar Funcionalidades:
1. Crie novos arquivos PHP na estrutura apropriada
2. Adicione validações de segurança
3. Teste todas as funcionalidades

## 🐛 Solução de Problemas

### Erro de Conexão com Banco:
- Verifique se o MySQL está rodando
- Confirme as credenciais em `config/database.php`
- Certifique-se de que o banco `RAF_ProjetoFoodCursor` existe

### Upload de Imagens não Funciona:
- Verifique permissões da pasta `uploads/`
- Confirme se o PHP tem permissão de escrita
- Verifique o tamanho máximo de upload no `php.ini`

### Página não Carrega:
- Verifique se o Apache está rodando
- Confirme se os arquivos estão na pasta correta
- Verifique logs de erro do Apache

## 📞 Suporte

Para dúvidas ou problemas:
1. Verifique este README
2. Consulte os logs de erro do PHP/Apache
3. Teste em um ambiente limpo

## 📄 Licença

Este projeto foi desenvolvido para fins educacionais e de demonstração.

---

**Desenvolvido com ❤️ para o yFood** 