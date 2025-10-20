<?php
session_start();
require_once('src/UsuarioDAO.php');
require_once('src/AvaliacaoDAO.php');
require_once('src/SeguidorDAO.php');

// Pega o ID do usuário da URL
$usuario_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Se não tiver ID, redireciona
if (!$usuario_id) {
    header('Location: explorar.php');
    exit;
}

// Busca dados do usuário com estatísticas
$usuario = UsuarioDAO::obterPerfil($usuario_id);
$foto_perfil = $usuario['foto_perfil'] ?? null;

// Se usuário não existe, redireciona
if (!$usuario) {
    $_SESSION['erro'] = 'Usuário não encontrado.';
    header('Location: explorar.php');
    exit;
}

// Verifica se o usuário logado é admin
$eh_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

// Busca as avaliações do usuário
$avaliacoes = AvaliacaoDAO::listarPorUsuario($usuario_id);

// Verifica se é o próprio perfil
$eh_proprio_perfil = isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $usuario_id;

// Verifica se está seguindo (apenas se não for o próprio perfil e estiver logado)
$esta_seguindo = false;
if (!$eh_proprio_perfil && isset($_SESSION['usuario_id'])) {
    $esta_seguindo = SeguidorDAO::estaSeguindo($_SESSION['usuario_id'], $usuario_id);
}
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
    <title><?php echo htmlspecialchars($usuario['nome_completo']); ?> | Perfil</title>
</head>

<body>

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
                <div class="avatar-container">
                    <?php if ($eh_proprio_perfil): ?>
                        <!-- Form de upload apenas para o próprio perfil -->
                        <form id="form-foto-perfil" action="atualizar_foto_perfil.php" method="POST" enctype="multipart/form-data">
                            <div class="avatar" style="cursor: pointer;" onclick="document.getElementById('foto_perfil_input').click()">
                                <?php if ($foto_perfil && file_exists('uploads/perfil/' . $foto_perfil)): ?>
                                    <img src="uploads/perfil/<?php echo htmlspecialchars($foto_perfil); ?>" alt="Foto de perfil" id="avatar-preview">
                                <?php else: ?>
                                    <svg width="80" height="80" viewBox="0 0 276 275" fill="none" xmlns="http://www.w3.org/2000/svg" id="avatar-svg">
                                        <circle cx="138" cy="137.5" r="137.5" fill="<?php echo $eh_admin ? '#070706ff' : '#6A53B8'; ?>" />
                                        <path d="M217.898 244.3C217.898 223.056 209.459 202.683 194.438 187.661C179.416 172.639 159.042 164.2 137.798 164.2C116.555 164.2 96.1808 172.639 81.1591 187.661C66.1375 202.683 57.6984 223.056 57.6984 244.3" stroke="white" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M137.798 164.2C167.29 164.2 191.198 140.292 191.198 110.8C191.198 81.3081 167.29 57.4001 137.798 57.4001C108.306 57.4001 84.3983 81.3081 84.3983 110.8C84.3983 140.292 108.306 164.2 137.798 164.2Z" stroke="white" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                <?php endif; ?>

                                <div class="avatar-upload-overlay">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm0 14c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6zm-1-9v3H8v2h3v3h2v-3h3v-2h-3V9h-2z" />
                                    </svg>
                                </div>
                            </div>

                            <input
                                type="file"
                                name="foto_perfil"
                                id="foto_perfil_input"
                                accept="image/jpeg,image/png,image/jpg,image/gif"
                                onchange="previewAndSubmit(this)">
                        </form>
                    <?php else: ?>
                        <!-- Avatar apenas visualização para outros perfis -->
                        <div class="avatar">
                            <?php if ($foto_perfil && file_exists('uploads/perfil/' . $foto_perfil)): ?>
                                <img src="uploads/perfil/<?php echo htmlspecialchars($foto_perfil); ?>" alt="Foto de perfil">
                            <?php else: ?>
                                <svg width="80" height="80" viewBox="0 0 276 275" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="138" cy="137.5" r="137.5" fill="<?php echo $eh_admin ? '#070706ff' : '#6A53B8'; ?>" />
                                    <path d="M217.898 244.3C217.898 223.056 209.459 202.683 194.438 187.661C179.416 172.639 159.042 164.2 137.798 164.2C116.555 164.2 96.1808 172.639 81.1591 187.661C66.1375 202.683 57.6984 223.056 57.6984 244.3" stroke="white" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M137.798 164.2C167.29 164.2 191.198 140.292 191.198 110.8C191.198 81.3081 167.29 57.4001 137.798 57.4001C108.306 57.4001 84.3983 81.3081 84.3983 110.8C84.3983 140.292 108.306 164.2 137.798 164.2Z" stroke="white" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <script>
                    function previewAndSubmit(input) {
                        if (input.files && input.files[0]) {
                            const file = input.files[0];

                            // Validar tamanho (5MB)
                            if (file.size > 5 * 1024 * 1024) {
                                alert('A imagem deve ter no máximo 5MB');
                                input.value = '';
                                return;
                            }

                            // Validar tipo
                            const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                            if (!tiposPermitidos.includes(file.type)) {
                                alert('Formato não permitido. Use JPG, PNG ou GIF');
                                input.value = '';
                                return;
                            }

                            // Preview da imagem
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const avatarDiv = document.querySelector('.avatar-editavel');
                                const svg = document.getElementById('avatar-svg');

                                if (svg) {
                                    svg.remove();
                                }

                                let img = document.getElementById('avatar-preview');
                                if (!img) {
                                    img = document.createElement('img');
                                    img.id = 'avatar-preview';
                                    img.alt = 'Foto de perfil';
                                    avatarDiv.insertBefore(img, avatarDiv.firstChild);
                                }
                                img.src = e.target.result;
                            };
                            reader.readAsDataURL(file);

                            // Submeter o formulário automaticamente
                            document.getElementById('form-foto-perfil').submit();
                        }
                    }
                </script>
                <div>
                    <h1 class="perfil-nome">
                        <?php echo htmlspecialchars($usuario['nome_completo']); ?>
                    </h1>
                    <p class="perfil-email"><?php echo htmlspecialchars($usuario['email']); ?></p>

                    <?php if (!$eh_admin): ?>
                        <div class="perfil-status">
                            <span><strong><?php echo $usuario['total_avaliacoes']; ?></strong> avaliações</span>
                            <span><strong><?php echo $usuario['total_seguidores']; ?></strong> seguidores</span>
                            <div class="perfil-acoes">
                                <?php if (!$eh_proprio_perfil && isset($_SESSION['usuario_id'])): ?>
                                    <!-- Botão de Seguir (apenas quando está visitando perfil de outra pessoa) -->
                                    <button class="btn-seguir <?php echo $esta_seguindo ? 'seguindo' : ''; ?>"
                                        onclick="toggleSeguir(<?php echo $usuario_id; ?>)"
                                        id="btnSeguir">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <?php if ($esta_seguindo): ?>
                                                <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            <?php else: ?>
                                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            <?php endif; ?>
                                        </svg>
                                        <span><?php echo $esta_seguindo ? 'Seguindo' : 'Seguir'; ?></span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="btn-editar-container">
                <?php if ($eh_proprio_perfil): ?>
                    <?php if (!$eh_admin): ?>
                        <button class="btn-editar" onclick="abrirModalEditar()">Editar usuário</button>
                    <?php endif; ?>
                    <button class="btn-editar" onclick="abrirModalSenha()">Editar senha</button>
                    <a href="logout.php" class="btn-sair">Sair</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Avaliações (apenas para não-admin) -->
        <?php if (!$eh_admin): ?>
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
                                            $corPreenchimento = $preenchida ? '#FFF600' : 'none';
                                        ?>
                                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M10.5268 1.29489C10.5706 1.20635 10.6383 1.13183 10.7223 1.07972C10.8062 1.02761 10.903 1 11.0018 1C11.1006 1 11.1974 1.02761 11.2813 1.07972C11.3653 1.13183 11.433 1.20635 11.4768 1.29489L13.7868 5.97389C13.939 6.28186 14.1636 6.5483 14.4414 6.75035C14.7192 6.95239 15.0419 7.08401 15.3818 7.13389L20.5478 7.88989C20.6457 7.90408 20.7376 7.94537 20.8133 8.00909C20.8889 8.07282 20.9452 8.15644 20.9758 8.2505C21.0064 8.34456 21.0101 8.4453 20.9864 8.54133C20.9627 8.63736 20.9126 8.72485 20.8418 8.79389L17.1058 12.4319C16.8594 12.672 16.6751 12.9684 16.5686 13.2955C16.4622 13.6227 16.4369 13.9708 16.4948 14.3099L17.3768 19.4499C17.3941 19.5477 17.3835 19.6485 17.3463 19.7406C17.3091 19.8327 17.2467 19.9125 17.1663 19.9709C17.086 20.0293 16.9908 20.0639 16.8917 20.0708C16.7926 20.0777 16.6935 20.0566 16.6058 20.0099L11.9878 17.5819C11.6835 17.4221 11.345 17.3386 11.0013 17.3386C10.6576 17.3386 10.3191 17.4221 10.0148 17.5819L5.3978 20.0099C5.31013 20.0563 5.2112 20.0772 5.11225 20.0701C5.0133 20.0631 4.91832 20.0285 4.83809 19.9701C4.75787 19.9118 4.69563 19.8321 4.65846 19.7401C4.62128 19.6482 4.61066 19.5476 4.6278 19.4499L5.5088 14.3109C5.567 13.9716 5.54178 13.6233 5.43534 13.2959C5.32889 12.9686 5.14441 12.672 4.8978 12.4319L1.1618 8.79489C1.09039 8.72593 1.03979 8.63829 1.01576 8.54197C0.991731 8.44565 0.995237 8.34451 1.02588 8.25008C1.05652 8.15566 1.11307 8.07174 1.18908 8.00788C1.26509 7.94402 1.3575 7.90279 1.4558 7.88889L6.6208 7.13389C6.96106 7.08439 7.28419 6.95295 7.56238 6.75088C7.84058 6.54881 8.0655 6.28216 8.2178 5.97389L10.5268 1.29489Z"
                                                    fill="<?php echo $corPreenchimento; ?>"
                                                    stroke="black"
                                                    stroke-opacity="0.6"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round" />
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
        <?php else: ?>
            <!-- Mensagem para Admin -->
            <div class="admin-info" style="text-align: center; padding: 40px; background: linear-gradient(135deg, #070706ff 0%, #2b2828ff 100%); border-radius: 15px; margin-top: 30px;">
                <h2 style="color: #fff; margin: 15px 0;">Painel do Administrador</h2>
                <p style="color: #fff; font-size: 16px; margin-bottom: 20px;">Você tem acesso total para gerenciar as séries da plataforma</p>
                <a href="cadastrar_serie.php" style="display: inline-block; background: #fff; color: #000000ff; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: bold; transition: transform 0.3s;">
                    Cadastrar Nova Série
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Editar Usuário -->
    <?php if ($eh_proprio_perfil && !$eh_admin): ?>
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
    <?php endif; ?>

    <!-- Modal Editar Senha -->
    <?php if ($eh_proprio_perfil): ?>
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
        // Função para seguir/deixar de seguir
        async function toggleSeguir(usuarioId) {
            const btn = document.getElementById('btnSeguir');
            const estaSeguindo = btn.classList.contains('seguindo');

            // Adiciona classe de loading
            btn.classList.add('loading');

            try {
                const response = await fetch('seguir_usuario.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        usuario_id: usuarioId,
                        acao: estaSeguindo ? 'deixar_seguir' : 'seguir'
                    })
                });

                const data = await response.json();

                if (data.sucesso) {
                    // Atualiza o botão
                    if (estaSeguindo) {
                        btn.classList.remove('seguindo');
                        btn.querySelector('span').textContent = 'Seguir';
                        btn.querySelector('svg').innerHTML = '<path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
                    } else {
                        btn.classList.add('seguindo');
                        btn.querySelector('span').textContent = 'Seguindo';
                        btn.querySelector('svg').innerHTML = '<path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
                    }

                    // Atualiza contador de seguidores
                    const seguidoresElement = document.querySelector('.perfil-status span:last-child strong');
                    if (seguidoresElement) {
                        const novoTotal = parseInt(seguidoresElement.textContent) + (estaSeguindo ? -1 : 1);
                        seguidoresElement.textContent = novoTotal;
                    }

                    mostrarNotificacao('sucesso', 'Sucesso', data.mensagem);
                } else {
                    mostrarNotificacao('erro', 'Erro', data.mensagem);
                }
            } catch (error) {
                mostrarNotificacao('erro', 'Erro', 'Erro ao processar solicitação');
            } finally {
                btn.classList.remove('loading');
            }
        }

        function abrirModalEditar() {
            const modal = document.getElementById('modalEditarUsuario');
            if (modal) {
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }
        }

        function abrirModalSenha() {
            const modal = document.getElementById('modalEditarSenha');
            if (modal) {
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }
        }

        function fecharModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }

        window.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });

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