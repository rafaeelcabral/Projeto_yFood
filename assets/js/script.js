// Função para adicionar produto ao carrinho
function adicionarAoCarrinho(id, nome, preco) {
    // Mostrar loading no botão
    const botao = event.target;
    const textoOriginal = botao.innerHTML;
    botao.innerHTML = '<div class="loading"></div>';
    botao.disabled = true;

    // Fazer requisição AJAX para adicionar ao carrinho
    fetch('ajax/adicionar_carrinho.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: id,
            nome: nome,
            preco: preco
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar mensagem de sucesso
            mostrarNotificacao('Produto adicionado ao carrinho!', 'success');
            
            // Atualizar o total do carrinho no header
            atualizarCarrinhoHeader(data.total);
            
            // Recarregar a página após um breve delay para mostrar o carrinho
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            mostrarNotificacao(data.message || 'Erro ao adicionar produto', 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarNotificacao('Erro ao adicionar produto', 'error');
    })
    .finally(() => {
        // Restaurar botão
        botao.innerHTML = textoOriginal;
        botao.disabled = false;
    });
}

// Função para atualizar quantidade no carrinho
function atualizarQuantidade(id, acao) {
    fetch('ajax/atualizar_carrinho.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: id,
            acao: acao // 'adicionar' ou 'remover'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Atualizar a página do carrinho
            location.reload();
        } else {
            mostrarNotificacao(data.message || 'Erro ao atualizar carrinho', 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarNotificacao('Erro ao atualizar carrinho', 'error');
    });
}

// Função para remover item do carrinho
function removerDoCarrinho(id) {
    if (confirm('Tem certeza que deseja remover este item do carrinho?')) {
        fetch('ajax/remover_carrinho.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                mostrarNotificacao(data.message || 'Erro ao remover item', 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarNotificacao('Erro ao remover item', 'error');
        });
    }
}

// Função para atualizar o header do carrinho
function atualizarCarrinhoHeader(total) {
    const carrinhoLink = document.querySelector('.carrinho');
    if (carrinhoLink) {
        const totalSpan = carrinhoLink.querySelector('.carrinho-total');
        if (totalSpan) {
            totalSpan.textContent = `R$ ${parseFloat(total).toFixed(2).replace('.', ',')}`;
        }
    }
}

// Função para mostrar notificações
function mostrarNotificacao(mensagem, tipo) {
    // Remover notificações existentes
    const notificacoesExistentes = document.querySelectorAll('.notificacao');
    notificacoesExistentes.forEach(notif => notif.remove());

    // Criar nova notificação
    const notificacao = document.createElement('div');
    notificacao.className = `notificacao notificacao-${tipo}`;
    notificacao.innerHTML = `
        <div class="notificacao-conteudo">
            <span>${mensagem}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="notificacao-fechar">&times;</button>
        </div>
    `;

    // Adicionar estilos CSS inline
    notificacao.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${tipo === 'success' ? '#d4edda' : '#f8d7da'};
        color: ${tipo === 'success' ? '#155724' : '#721c24'};
        border: 1px solid ${tipo === 'success' ? '#c3e6cb' : '#f5c6cb'};
        border-radius: 8px;
        padding: 1rem;
        z-index: 10000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease-out;
    `;

    // Adicionar ao body
    document.body.appendChild(notificacao);

    // Remover automaticamente após 3 segundos
    setTimeout(() => {
        if (notificacao.parentElement) {
            notificacao.remove();
        }
    }, 3000);
}

// Função para validar formulários
function validarFormulario(form) {
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let valido = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = '#d32f2f';
            valido = false;
        } else {
            input.style.borderColor = '#ddd';
        }
    });

    return valido;
}

// Função para formatar preços
function formatarPreco(preco) {
    return parseFloat(preco).toFixed(2).replace('.', ',');
}

// Função para animar elementos quando entram na viewport
function animarElementos() {
    const elementos = document.querySelectorAll('.produto-card, .form-container, .carrinho-container');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });

    elementos.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
}

// Função para inicializar funcionalidades
function inicializar() {
    // Adicionar estilos CSS para notificações
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .notificacao-conteudo {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .notificacao-fechar {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: inherit;
            padding: 0;
            margin-left: auto;
        }
        
        .notificacao-fechar:hover {
            opacity: 0.7;
        }
    `;
    document.head.appendChild(style);

    // Animar elementos
    animarElementos();

    // Adicionar validação aos formulários
    const formularios = document.querySelectorAll('form');
    formularios.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validarFormulario(this)) {
                e.preventDefault();
                mostrarNotificacao('Por favor, preencha todos os campos obrigatórios', 'error');
            }
        });
    });

    // Adicionar efeitos hover aos botões
    const botoes = document.querySelectorAll('.btn, .btn-adicionar');
    botoes.forEach(botao => {
        botao.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        botao.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}

// Inicializar quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', inicializar);

// Função para fazer logout
function fazerLogout() {
    if (confirm('Tem certeza que deseja sair?')) {
        fetch('logout.php')
        .then(() => {
            window.location.href = 'index.php';
        })
        .catch(error => {
            console.error('Erro ao fazer logout:', error);
            window.location.href = 'index.php';
        });
    }
}

// Função para alternar visibilidade da senha
function alternarSenha(inputId) {
    const input = document.getElementById(inputId);
    const tipo = input.type === 'password' ? 'text' : 'password';
    input.type = tipo;
    
    const botao = input.nextElementSibling;
    const icone = botao.querySelector('i');
    icone.className = tipo === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
} 