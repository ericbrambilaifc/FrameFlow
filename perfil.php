<?php
session_start();
require_once('src/UsuarioDAO.php');
require_once('src/AvaliacaoDAO.php');

// Pega o ID do usuário da URL
$usuario_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Se não tiver ID, redireciona
if (!$usuario_id) {
    header('Location: explorar.php');
    exit;
}

// Busca dados do usuário com estatísticas
$usuario = UsuarioDAO::obterPerfil($usuario_id);

// Se usuário não existe, redireciona
if (!$usuario) {
    $_SESSION['erro'] = 'Usuário não encontrado.';
    header('Location: explorar.php');
    exit;
}

// Busca as avaliações do usuário
$avaliacoes = AvaliacaoDAO::listarPorUsuario($usuario_id);

// Verifica se é o próprio perfil
$eh_proprio_perfil = isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $usuario_id;
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="explorar.css">
    <link rel="stylesheet" href="modal.css">
    <link rel="stylesheet" href="global.css">
    <link rel="stylesheet" href="perfil.css">
    <link rel="stylesheet" href="alert.css">
    <title><?php echo htmlspecialchars($usuario['nome_completo']); ?> | Seu Perfil</title>
</head>

<body>
    <header>
        <ul>
            <li><a href="explorar.php">Explorar</a></li>
            <li><a href="comunidade.php">Comunidade</a></li>
        </ul>
        <div>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="perfil.php?id=<?php echo $_SESSION['usuario_id']; ?>">
                    <i>
                        <svg width="30" height="30" viewBox="0 0 276 275" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M217.898 244.3C217.898 223.056 209.459 202.683 194.438 187.661C179.416 172.639 159.042 164.2 137.798 164.2C116.555 164.2 96.1808 172.639 81.1591 187.661C66.1375 202.683 57.6984 223.056 57.6984 244.3" stroke="#6A53B8" stroke-width="20" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M137.798 164.2C167.29 164.2 191.198 140.292 191.198 110.8C191.198 81.3081 167.29 57.4001 137.798 57.4001C108.306 57.4001 84.3983 81.3081 84.3983 110.8C84.3983 140.292 108.306 164.2 137.798 164.2Z" stroke="#6A53B8" stroke-width="20" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M137.799 271C211.529 271 271.299 211.23 271.299 137.5C271.299 63.77 211.529 4 137.799 4C64.0686 4 4.29858 63.77 4.29858 137.5C4.29858 211.23 64.0686 271 137.799 271Z" stroke="#6A53B8" stroke-width="20" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </i>
                    <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
                </a>
            <?php else: ?>
                <a href="explorar.php" style="color: #6A53B8;">Fazer Login</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="container-perfil">
        <!-- Botão Voltar -->
        <a href="javascript:history.back()" class="btn-voltar">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Voltar
        </a>

        <!-- Cabeçalho do Perfil -->
        <div class="perfil-header">
            <div class="perfil-info">
                <div class="avatar">
                    <svg width="80" height="80" viewBox="0 0 276 275" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="138" cy="137.5" r="137.5" fill="#6A53B8" />
                        <path d="M217.898 244.3C217.898 223.056 209.459 202.683 194.438 187.661C179.416 172.639 159.042 164.2 137.798 164.2C116.555 164.2 96.1808 172.639 81.1591 187.661C66.1375 202.683 57.6984 223.056 57.6984 244.3" stroke="white" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M137.798 164.2C167.29 164.2 191.198 140.292 191.198 110.8C191.198 81.3081 167.29 57.4001 137.798 57.4001C108.306 57.4001 84.3983 81.3081 84.3983 110.8C84.3983 140.292 108.306 164.2 137.798 164.2Z" stroke="white" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <div>
                    <h1 class="perfil-nome"><?php echo htmlspecialchars($usuario['nome_completo']); ?></h1>
                    <p class="perfil-email"><?php echo htmlspecialchars($usuario['email']); ?></p>
                    <div class="perfil-stats">
                        <span><strong><?php echo $usuario['total_avaliacoes']; ?></strong> avaliações</span>
                    </div>
                </div>
            </div>

            <?php if ($eh_proprio_perfil): ?>
                <div class="perfil-acoes">
                    <button class="btn-editar" onclick="abrirModalEditar()">Editar usuário</button>
                    <button class="btn-editar" onclick="abrirModalSenha()">Editar senha</button>
                    <a href="logout.php" class="btn-sair">Sair</a>

                </div>
            <?php endif; ?>
        </div>

        <!-- Avaliações -->
        <div class="avaliacoes-secao">
            <h2>Suas últimas avaliações</h2>

            <?php if (count($avaliacoes) > 0): ?>
                <div class="avaliacoes-lista">
                    <?php foreach ($avaliacoes as $avaliacao): ?>
                        <div class="avaliacao-card">
                            <div class="avaliacao-header">
                                <h3 class="serie-titulo"><?php echo htmlspecialchars($avaliacao['titulo']); ?></h3>
                                <div class="estrelas">
                                    <?php
                                    for ($i = 1; $i <= 5; $i++):
                                        $preenchida = $i <= $avaliacao['nota'];
                                    ?>
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="<?php echo $preenchida ? '#FFD700' : 'none'; ?>" stroke="#FFD700" stroke-width="2">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                        </svg>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <p class="avaliacao-comentario"><?php echo nl2br(htmlspecialchars($avaliacao['comentario'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="sem-avaliacoes">Nenhuma avaliação realizada ainda.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Editar Usuário -->
    <?php if ($eh_proprio_perfil): ?>
        <div id="modalEditarUsuario" class="modal">
            <div class="modal-login">
                <button class="close" onclick="fecharModal('modalEditarUsuario')">
                    <svg width="24" height="24" viewBox="0 0 276 275" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M137.798 271C211.528 271 271.298 211.23 271.298 137.5C271.298 63.77 211.528 4 137.798 4C64.0683 4 4.29834 63.77 4.29834 137.5C4.29834 211.23 64.0683 271 137.798 271Z" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M177.848 97.4497L97.7479 177.55" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M97.7479 97.4497L177.848 177.55" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

                <h2 class="titulo">Editar Perfil</h2>
                <form action="editar_perfil.php" method="post" id="formEditarPerfil">
                    <div class="form-grupo">
                        <div class="label-estilizado">
                            <input type="text" name="nome_completo" id="nome_completo" placeholder="Crie seu novo nome de usuário" class="input-estilizado" required>

                            <input type="password" name="senha_confirmacao" id="senha_confirmacao" placeholder="Digite sua senha" class="input-estilizado" required>
                        </div>
                    </div>
                    <button class="botao-entrar" type="submit">Salvar alterações</button>
                </form>
            </div>
        </div>

        <!-- Modal Editar Senha -->
        <div id="modalEditarSenha" class="modal">
            <div class="modal-login">
                <button class="close" onclick="fecharModal('modalEditarSenha')">
                    <svg width="24" height="24" viewBox="0 0 276 275" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M137.798 271C211.528 271 271.298 211.23 271.298 137.5C271.298 63.77 211.528 4 137.798 4C64.0683 4 4.29834 63.77 4.29834 137.5C4.29834 211.23 64.0683 271 137.798 271Z" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M177.848 97.4497L97.7479 177.55" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M97.7479 97.4497L177.848 177.55" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

                <h2 class="titulo">Alterar Senha</h2>
                <form action="editar_senha.php" method="post" id="formSenha">
                    <div class="form-grupo">
                        <div class="label-estilizado">
                            <input type="password" name="senha_atual" placeholder="Senha atual" class="input-estilizado" required>
                            <input type="password" name="nova_senha" id="nova_senha" placeholder="Nova senha" class="input-estilizado" required>
                            <input type="password" name="confirmar_senha" id="confirmar_nova_senha" placeholder="Confirmar nova senha" class="input-estilizado" required>
                        </div>
                    </div>
                    <button class="botao-entrar" type="submit">Alterar senha</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <script>
        function abrirModalEditar() {
            const modal = document.getElementById('modalEditarUsuario');
            if (modal) {
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden'; // Previne scroll
            }
        }

        function abrirModalSenha() {
            const modal = document.getElementById('modalEditarSenha');
            if (modal) {
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden'; // Previne scroll
            }
        }

        function fecharModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto'; // Restaura scroll
            }
        }

        // Fechar modais clicando fora (no fundo escuro)
        window.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });

        // Fechar modal com tecla ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modais = document.querySelectorAll('.modal');
                modais.forEach(modal => {
                    if (modal.style.display === 'block') {
                        modal.style.display = 'none';
                        document.body.style.overflow = 'auto';
                    }
                });
            }
        });

        // Validar senhas no formulário
        const formSenha = document.getElementById('formSenha');
        if (formSenha) {
            formSenha.addEventListener('submit', function(event) {
                const novaSenha = document.getElementById('nova_senha').value;
                const confirmarSenha = document.getElementById('confirmar_nova_senha').value;

                if (novaSenha !== confirmarSenha) {
                    event.preventDefault();
                    mostrarNotificacao('erro', 'Erro', 'As senhas não coincidem!');
                    return false;
                }

                if (novaSenha.length < 6) {
                    event.preventDefault();
                    mostrarNotificacao('erro', 'Erro', 'A senha deve ter no mínimo 6 caracteres!');
                    return false;
                }
            });
        }

        // Sistema de notificações
        function mostrarNotificacao(tipo, titulo, mensagem) {
            const notificacoesExistentes = document.querySelectorAll('.notificacao-popup');
            notificacoesExistentes.forEach(n => n.remove());

            const notificacao = document.createElement('div');
            notificacao.className = `notificacao-popup ${tipo}`;

            const iconeSucesso = `<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M20 6L9 17L4 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>`;
            const iconeErro = `<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6L18 18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>`;
            const icone = tipo === 'sucesso' ? iconeSucesso : iconeErro;

            notificacao.innerHTML = `
            <button class="notificacao-fechar" onclick="fecharNotificacao(this)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
            <div class="notificacao-header">
                <div class="notificacao-icone">${icone}</div>
                <h3 class="notificacao-titulo">${titulo}</h3>
            </div>
            <p class="notificacao-mensagem">${mensagem}</p>
        `;

            document.body.appendChild(notificacao);
            setTimeout(() => notificacao.classList.add('show'), 10);
            setTimeout(() => fecharNotificacao(notificacao), 5000);
        }

        function fecharNotificacao(elemento) {
            const notificacao = elemento.classList ? elemento : elemento.closest('.notificacao-popup');
            if (notificacao) {
                notificacao.classList.remove('show');
                notificacao.classList.add('hide');
                setTimeout(() => notificacao.remove(), 300);
            }
        }

        // Executar quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['sucesso'])): ?>
                mostrarNotificacao('sucesso', 'Sucesso', '<?php echo addslashes($_SESSION['sucesso']); ?>');
                <?php unset($_SESSION['sucesso']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['erro'])): ?>
                mostrarNotificacao('erro', 'Erro', '<?php echo addslashes($_SESSION['erro']); ?>');
                <?php unset($_SESSION['erro']); ?>
            <?php endif; ?>
        });
    </script>
</body>

</html>