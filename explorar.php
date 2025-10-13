<?php
session_start();
require_once('src/SerieDAO.php');
require_once('src/ClassificacaoDAO.php');
require_once('src/GeneroDAO.php');
require_once('src/AvaliacaoDAO.php');
require_once('src/UsuarioDAO.php');
require_once('src/AvaliacaoDAO.php');


$classificacoes = ClassificacaoDAO::listar();
$generos = GeneroDAO::listar();

// Capturar filtros
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$genero_id = isset($_GET['genero']) && $_GET['genero'] !== '' ? $_GET['genero'] : null;
$classificacao_id = isset($_GET['classificacao']) && $_GET['classificacao'] !== '' ? $_GET['classificacao'] : null;

// Verificar se há algum filtro ativo
$temFiltro = !empty($buscar) || $genero_id !== null || $classificacao_id !== null;

// Buscar séries com ou sem filtros
if ($temFiltro) {
    // Se tiver filtros, usa o método buscar
    $series = SerieDao::buscar($buscar, $genero_id, $classificacao_id);
} else {
    // Se não tiver filtros, lista todas
    $series = SerieDao::listar();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="global.css">
    <link rel="stylesheet" href="modal.css">
    <link rel="stylesheet" href="explorar.css">
    <link rel="stylesheet" href="alert.css">
    <title>FrameFlow | Opiniões que guiam suas próximas maratonas</title>
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
                <a href="#" id="openModal">
                    <i>
                        <svg width="30" height="30" viewBox="0 0 276 275" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M217.898 244.3C217.898 223.056 209.459 202.683 194.438 187.661C179.416 172.639 159.042 164.2 137.798 164.2C116.555 164.2 96.1808 172.639 81.1591 187.661C66.1375 202.683 57.6984 223.056 57.6984 244.3" stroke="#6A53B8" stroke-width="20" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M137.798 164.2C167.29 164.2 191.198 140.292 191.198 110.8C191.198 81.3081 167.29 57.4001 137.798 57.4001C108.306 57.4001 84.3983 81.3081 84.3983 110.8C84.3983 140.292 108.306 164.2 137.798 164.2Z" stroke="#6A53B8" stroke-width="20" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M137.799 271C211.529 271 271.299 211.23 271.299 137.5C271.299 63.77 211.529 4 137.799 4C64.0686 4 4.29858 63.77 4.29858 137.5C4.29858 211.23 64.0686 271 137.799 271Z" stroke="#6A53B8" stroke-width="20" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </i>
                    Seu Perfil
                </a>
            <?php endif; ?>
        </div>
    </header>


    <!-- Barra de busca de séries -->
    <section style="padding: 20px; max-width: 90%; margin: 0 auto;">
        <!-- Formulário de Busca em Linha -->
        <form method="GET" action="explorar.php" style="margin-bottom: 30px;">
            <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: stretch;">
                <input
                    type="text"
                    name="buscar"
                    placeholder="Pesquisar por título de série"
                    class="input-estilizado"
                    value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>"
                    style="flex: 1; min-width: 200px; padding: 12px; font-size: 16px;">

                <select
                    name="genero"
                    class="input-estilizado"
                    style="flex: 1; min-width: 200px; padding: 12px; font-size: 16px;">
                    <option value="">Todos os gêneros</option>
                    <?php foreach ($generos as $genero): ?>
                        <option value="<?php echo $genero['id']; ?>"
                            <?php echo (isset($_GET['genero']) && $_GET['genero'] == $genero['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($genero['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select
                    name="classificacao"
                    class="input-estilizado"
                    style="flex: 1; min-width: 200px; padding: 12px; font-size: 16px;">
                    <option value="">Todas as classificações</option>
                    <?php foreach ($classificacoes as $classificacao): ?>
                        <option value="<?php echo $classificacao['id']; ?>"
                            <?php echo (isset($_GET['classificacao']) && $_GET['classificacao'] == $classificacao['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($classificacao['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="botao-buscar" style="padding: 12px 30px; font-size: 16px;">
                    Buscar
                </button>
            </div>
        </form>




        <!-- Resultados da busca -->
        <!-- Resultados da busca -->
        <h2 style="color: #6A53B8; margin-bottom: 20px;">
            <?php if ($temFiltro): ?>
                Resultados da busca
                <?php if (!empty($buscar)): ?>
                    para: "<?php echo htmlspecialchars($buscar); ?>"
                <?php endif; ?>
            <?php else: ?>
                Todas as Séries
            <?php endif; ?>
        </h2>

        <?php if (count($series) > 0): ?>
            <div class="grid-series">
                <?php foreach ($series as $serie): ?>
                    <div class="card-serie" data-serie-id="<?php echo $serie['id']; ?>"> <img src="<?php echo htmlspecialchars($serie['imagem_url']); ?>" alt="<?php echo htmlspecialchars($serie['titulo']); ?>">
                        <h3><?php echo htmlspecialchars($serie['titulo']); ?></h3>
                        <p>Avaliações: <?php echo $serie['total_avaliacoes']; ?></p>
                        <p>Nota média: <?php echo number_format($serie['media_nota'], 1); ?>/10</p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #666;">Nenhuma série encontrada com os filtros selecionados.</p>
        <?php endif; ?>
    </section>

    <!-- Modal fazer login -->
    <div id="modal" class="modal">
        <div class="modal-login">
            <button class="close">
                <svg width="24" height="24" viewBox="0 0 276 275" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M137.798 271C211.528 271 271.298 211.23 271.298 137.5C271.298 63.77 211.528 4 137.798 4C64.0683 4 4.29834 63.77 4.29834 137.5C4.29834 211.23 64.0683 271 137.798 271Z" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M177.848 97.4497L97.7479 177.55" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M97.7479 97.4497L177.848 177.55" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>

            <h2 class="titulo">Fazer Login</h2>
            <form action="login.php" method="post">
                <div class="form-grupo">
                    <div class="label-estilizado">
                        <input type="email" name="email" placeholder="Digite seu e-mail" class="input-estilizado" required>
                        <input type="password" name="senha" placeholder="Digite sua senha" class="input-estilizado" required>
                    </div>
                    <a href="#" id="abrirCriarConta">Não tem uma conta? <strong>Cadastre-se clicando aqui</strong></a>
                </div>

                <button class="botao-entrar" type="submit">Entrar</button>
            </form>
        </div>
    </div>

    <!-- Modal criar conta -->
    <div id="modalCriarConta" class="modal">
        <div class="modal-login">
            <button class="close" id="closeCriar">
                <svg width="24" height="24" viewBox="0 0 276 275" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M137.798 271C211.528 271 271.298 211.23 271.298 137.5C271.298 63.77 211.528 4 137.798 4C64.0683 4 4.29834 63.77 4.29834 137.5C4.29834 211.23 64.0683 271 137.798 271Z" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M177.848 97.4497L97.7479 177.55" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M97.7479 97.4497L177.848 177.55" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>

            <h2 class="titulo">Criar Conta</h2>
            <form action="cadastro.php" method="post" id="formCadastro">
                <div class="form-grupo">
                    <div class="label-estilizado">
                        <input type="email" name="email" placeholder="Digite seu e-mail" class="input-estilizado" required>
                        <p class="texto-ajuda">*Após criar sua conta, não será possível alterar o endereço de e-mail</p>

                        <input type="text" name="nome_completo" placeholder="Digite seu nome completo" class="input-estilizado" required>

                        <input type="password" name="senha" id="senha" placeholder="Crie sua senha" class="input-estilizado" required>

                        <input type="password" name="confirmar_senha" id="confirmar_senha" placeholder="Repita sua senha" class="input-estilizado" required>
                    </div>
                </div>

                <button class="botao-entrar" type="submit">Criar conta</button>
            </form>
        </div>
    </div>

    <!-- Modal de Avaliações -->
    <div id="modalAvaliacoes" class="modal">
        <div class="modal-avaliacoes">
            <button class="close" id="closeAvaliacoes">
                <svg width="24" height="24" viewBox="0 0 276 275" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M137.798 271C211.528 271 271.298 211.23 271.298 137.5C271.298 63.77 211.528 4 137.798 4C64.0683 4 4.29834 63.77 4.29834 137.5C4.29834 211.23 64.0683 271 137.798 271Z" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M177.848 97.4497L97.7479 177.55" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M97.7479 97.4497L177.848 177.55" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>

            <div class="modal-avaliacoes-header">
                <h2 class="titulo" id="tituloSerie"></h2>
                <button class="botao-nova-avaliacao" id="btnNovaAvaliacao">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="white" stroke-width="2" />
                        <path d="M12 8V16M8 12H16" stroke="white" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    Nova avaliação
                </button>
            </div>

            <div id="conteudoAvaliacoes">
                <div class="loading">Carregando avaliações...</div>
            </div>
        </div>
    </div>

    <!-- Modal de Nova Avaliação -->
    <div id="modalNovaAvaliacao" class="modal">
        <div class="modal-login">
            <button class="close" id="closeNovaAvaliacao">
                <svg width="24" height="24" viewBox="0 0 276 275" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M137.798 271C211.528 271 271.298 211.23 271.298 137.5C271.298 63.77 211.528 4 137.798 4C64.0683 4 4.29834 63.77 4.29834 137.5C4.29834 211.23 64.0683 271 137.798 271Z" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M177.848 97.4497L97.7479 177.55" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M97.7479 97.4497L177.848 177.55" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>

            <h2 class="titulo">Nova Avaliação</h2>
            <form id="formNovaAvaliacao" method="POST" action="salvar_avaliacao.php">
                <input type="hidden" name="serie_id" id="serieIdAvaliacao">

                <div class="form-grupo">
                    <label style="color: #6A53B8; font-weight: 600; margin-bottom: 10px; display: block;">
                        Sua nota:
                    </label>
                    <div class="rating-input">
                        <input type="radio" name="nota" value="5" id="star5" required>
                        <label for="star5">★</label>
                        <input type="radio" name="nota" value="4" id="star4">
                        <label for="star4">★</label>
                        <input type="radio" name="nota" value="3" id="star3">
                        <label for="star3">★</label>
                        <input type="radio" name="nota" value="2" id="star2">
                        <label for="star2">★</label>
                        <input type="radio" name="nota" value="1" id="star1">
                        <label for="star1">★</label>
                    </div>

                    <label style="color: #6A53B8; font-weight: 600; margin: 20px 0 10px; display: block;">
                        Seu comentário:
                    </label>
                    <textarea
                        name="comentario"
                        class="input-estilizado"
                        rows="5"
                        placeholder="Escreva sua opinião sobre a série..."
                        required
                        style="resize: vertical; min-height: 120px; max-width: 100%;"></textarea>
                </div>

                <button class="botao-entrar" type="submit">Publicar Avaliação</button>
            </form>
        </div>
    </div>

    <script>
        // Sistema de notificações popup
        function mostrarNotificacao(tipo, titulo, mensagem) {
            // Remove notificações existentes
            const notificacoesExistentes = document.querySelectorAll('.notificacao-popup');
            notificacoesExistentes.forEach(n => n.remove());

            // Cria a estrutura da notificação
            const notificacao = document.createElement('div');
            notificacao.className = `notificacao-popup ${tipo}`;

            // Define os ícones SVG
            const iconeSucesso = `
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 6L9 17L4 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    `;

            const iconeErro = `
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M18 6L6 18M6 6L18 18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    `;

            const icone = tipo === 'sucesso' ? iconeSucesso : iconeErro;

            notificacao.innerHTML = `
        <button class="notificacao-fechar" onclick="fecharNotificacao(this)">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M13 1L1 13" stroke="#BABABA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M1 1L13 13" stroke="#BABABA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>


        </button>
        <div class="notificacao-header">
            <div class="notificacao-icone">
                ${icone}
            </div>
            <h3 class="notificacao-titulo">${titulo}</h3>
        </div>
        <p class="notificacao-mensagem">${mensagem}</p>
    `;

            document.body.appendChild(notificacao);

            // Mostra a notificação com animação
            setTimeout(() => {
                notificacao.classList.add('show');
            }, 10);

            // Remove automaticamente após 5 segundos
            setTimeout(() => {
                fecharNotificacao(notificacao);
            }, 5000);
        }

        function fecharNotificacao(elemento) {
            const notificacao = elemento.classList ? elemento : elemento.closest('.notificacao-popup');
            if (notificacao) {
                notificacao.classList.remove('show');
                notificacao.classList.add('hide');

                setTimeout(() => {
                    notificacao.remove();
                }, 300);
            }
        }

        // Aguarda o DOM carregar completamente
        document.addEventListener('DOMContentLoaded', function() {
            // Verifica se há mensagens de sessão e exibe como popup
            <?php if (isset($_SESSION['sucesso'])): ?>
                mostrarNotificacao('sucesso', 'Login efetuado com sucesso', '<?php echo addslashes($_SESSION['sucesso']); ?>');
                <?php unset($_SESSION['sucesso']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['erro'])): ?>
                mostrarNotificacao('erro', 'Não foi possível efetuar o login', '<?php echo addslashes($_SESSION['erro']); ?>');
                <?php unset($_SESSION['erro']); ?>
            <?php endif; ?>

            // Abrir o modal de LOGIN
            const openModalBtn = document.getElementById('openModal');
            if (openModalBtn) {
                openModalBtn.addEventListener('click', function(event) {
                    event.preventDefault();
                    document.getElementById('modal').style.display = 'block';
                });
            }

            // Abrir o modal de CRIAR CONTA
            const abrirCriarContaBtn = document.getElementById('abrirCriarConta');
            if (abrirCriarContaBtn) {
                abrirCriarContaBtn.addEventListener('click', function(event) {
                    event.preventDefault();
                    document.getElementById('modal').style.display = 'none';
                    document.getElementById('modalCriarConta').style.display = 'block';
                });
            }

            // Fechar o modal de LOGIN
            const closeBtn = document.querySelector('.close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    document.getElementById('modal').style.display = 'none';
                });
            }

            // Fechar o modal de CRIAR CONTA
            const closeCriarBtn = document.getElementById('closeCriar');
            if (closeCriarBtn) {
                closeCriarBtn.addEventListener('click', function() {
                    document.getElementById('modalCriarConta').style.display = 'none';
                });
            }

            // Fechar modais clicando fora
            window.addEventListener('click', function(event) {
                const modal = document.getElementById('modal');
                const modalCriarConta = document.getElementById('modalCriarConta');

                if (event.target === modal) {
                    modal.style.display = 'none';
                }
                if (event.target === modalCriarConta) {
                    modalCriarConta.style.display = 'none';
                }
            });

            // Validar senhas no cadastro
            const formCadastro = document.getElementById('formCadastro');
            if (formCadastro) {
                formCadastro.addEventListener('submit', function(event) {
                    const senha = document.getElementById('senha').value;
                    const confirmarSenha = document.getElementById('confirmar_senha').value;

                    if (senha !== confirmarSenha) {
                        event.preventDefault();
                        mostrarNotificacao('erro', 'Erro no cadastro', 'As senhas não coincidem!');
                        return false;
                    }

                    if (senha.length < 6) {
                        event.preventDefault();
                        mostrarNotificacao('erro', 'Erro no cadastro', 'A senha deve ter no mínimo 6 caracteres!');
                        return false;
                    }
                });
            }
        });

        // Tornar cards clicáveis
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card-serie');

            cards.forEach(card => {
                card.style.cursor = 'pointer';
                card.addEventListener('click', function() {
                    const titulo = this.querySelector('h3').textContent;
                    const serieId = this.dataset.serieId; // Você precisará adicionar data-serie-id no card
                    abrirModalAvaliacoes(serieId, titulo);
                });
            });
        });

        // Abrir modal de avaliações
        function abrirModalAvaliacoes(serieId, titulo) {
            document.getElementById('modalAvaliacoes').style.display = 'block';
            document.getElementById('tituloSerie').textContent = titulo;

            // Carregar avaliações via AJAX
            carregarAvaliacoes(serieId);

            // Salvar serieId para uso posterior
            document.getElementById('modalAvaliacoes').dataset.serieId = serieId;
        }

        // Carregar avaliações
        function carregarAvaliacoes(serieId) {
            const conteudo = document.getElementById('conteudoAvaliacoes');
            conteudo.innerHTML = '<div class="loading">Carregando avaliações...</div>';

            fetch(`buscar_avaliacoes.php?serie_id=${serieId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.avaliacoes && data.avaliacoes.length > 0) {
                        conteudo.innerHTML = data.avaliacoes.map(av => `
                    <div class="avaliacao-item">
                        <div class="avaliacao-header">
                            <div class="avaliacao-usuario">
                                <div class="avatar-usuario">${av.usuario_nome.charAt(0).toUpperCase()}</div>
                                <span class="usuario-nome">${av.usuario_nome}</span>
                            </div>
                            <div class="avaliacao-nota">
                                ${gerarEstrelas(av.nota)}
                            </div>
                        </div>
                        <p class="avaliacao-comentario">${av.comentario}</p>
                        <span class="avaliacao-data">${formatarData(av.data_avaliacao)}</span>
                    </div>
                `).join('');
                    } else {
                        conteudo.innerHTML = '<p class="sem-avaliacoes">Nenhuma avaliação ainda. Seja o primeiro a avaliar!</p>';
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar avaliações:', error);
                    conteudo.innerHTML = '<p class="erro-avaliacoes">Erro ao carregar avaliações. Tente novamente.</p>';
                });
        }

        // Gerar estrelas HTML
        function gerarEstrelas(nota) {
            let estrelas = '';
            for (let i = 1; i <= 5; i++) {
                estrelas += i <= nota ? '★' : '☆';
            }
            return estrelas;
        }

        // Formatar data
        function formatarData(data) {
            const date = new Date(data);
            const opcoes = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            return date.toLocaleDateString('pt-BR', opcoes);
        }

        // Botão Nova Avaliação
        document.getElementById('btnNovaAvaliacao').addEventListener('click', function() {
            <?php if (isset($_SESSION['usuario_id'])): ?>
                const serieId = document.getElementById('modalAvaliacoes').dataset.serieId;
                document.getElementById('serieIdAvaliacao').value = serieId;
                document.getElementById('modalAvaliacoes').style.display = 'none';
                document.getElementById('modalNovaAvaliacao').style.display = 'block';
            <?php else: ?>
                mostrarNotificacao('erro', 'Login necessário', 'Você precisa estar logado para avaliar uma série!');
                document.getElementById('modalAvaliacoes').style.display = 'none';
                document.getElementById('modal').style.display = 'block';
            <?php endif; ?>
        });

        // Fechar modal de avaliações
        document.getElementById('closeAvaliacoes').addEventListener('click', function() {
            document.getElementById('modalAvaliacoes').style.display = 'none';
        });

        // Fechar modal de nova avaliação
        document.getElementById('closeNovaAvaliacao').addEventListener('click', function() {
            document.getElementById('modalNovaAvaliacao').style.display = 'none';
        });

        // Fechar modais clicando fora (adicionar aos existentes)
        window.addEventListener('click', function(event) {
            const modalAvaliacoes = document.getElementById('modalAvaliacoes');
            const modalNovaAvaliacao = document.getElementById('modalNovaAvaliacao');

            if (event.target === modalAvaliacoes) {
                modalAvaliacoes.style.display = 'none';
            }
            if (event.target === modalNovaAvaliacao) {
                modalNovaAvaliacao.style.display = 'none';
            }
        });
    </script>

</body>

</html>