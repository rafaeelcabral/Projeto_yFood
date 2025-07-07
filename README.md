# 🍔 yFood - Sistema de Lanchonete Online

## Funcionalidades

### 1. Plataforma de Pedidos Online
- Menu interativo com categorias (Combos, Hambúrgueres, Batatas, Acompanhamentos, Bebidas)
- Adição, remoção e atualização de itens no carrinho
- Cadastro e login de clientes
- Checkout com confirmação de pedido
- Histórico de pedidos realizados
- Interface responsiva e intuitiva para clientes

### 2. Painel Administrativo
- Gerenciamento de produtos (CRUD)
- Visualização e controle de pedidos
- Gerenciamento de clientes cadastrados
- Login e logout de administradores

## Pré-requisitos

- XAMPP (Apache + MySQL + PHP)
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Navegador web moderno

## Tecnologias Utilizadas

- **Backend:** PHP 7.4+ (PDO)
- **Banco de Dados:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Servidor:** Apache (XAMPP)
- **Ícones:** Font Awesome 6.0
- **Design:** Responsivo

## Estrutura do Projeto

```
Projeto_yFood/
├── index.php                 # Página inicial do cliente
├── login.php                 # Login de clientes
├── logout.php                # Logout de clientes
├── cadastro.php              # Cadastro de clientes
├── carrinho.php              # Carrinho de compras
├── checkout.php              # Finalização de pedidos
├── historico.php             # Histórico de pedidos do cliente
├── admin/
│   ├── index.php             # Painel administrativo
│   ├── login.php             # Login do admin
│   ├── logout.php            # Logout do admin
│   ├── clientes.php          # Gerenciamento de clientes
│   ├── pedidos.php           # Gerenciamento de pedidos
│   └── produtos.php          # Gerenciamento de produtos
├── ajax/
│   ├── adicionar_carrinho.php
│   ├── atualizar_carrinho.php
│   └── remover_carrinho.php
├── config/
│   └── database.php          # Configuração do banco de dados
├── assets/
│   ├── css/
│   │   └── style.css         # Estilos gerais
│   ├── js/
│   │   └── script.js         # Scripts JS
│   └── img/
│       └── logo.png          # Logo do sistema
├── database_setup.sql        # Script de criação do banco
└── README.md
```

---

**Desenvolvido com ❤️ para o yFood** 