<?php
session_start();
require_once('src/SerieDAO.php');
require_once('src/ClassificacaoDAO.php');
require_once('src/GeneroDAO.php');
// Buscar séries para exibir
$series = SerieDao::listar();
$classificacoes = ClassificacaoDAO::listar();
$generos = GeneroDAO::listar();

// Busca de séries
$resultadoBusca = [];
if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
    $resultadoBusca = SerieDao::buscar($_GET['buscar']);
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
        <?php if (isset($_GET['buscar'])): ?>
            <h2 style="color: #6A53B8; margin-bottom: 20px;">
                Resultados para: "<?php echo htmlspecialchars($_GET['buscar']); ?>"
            </h2>
            <?php if (count($resultadoBusca) > 0): ?>
                <div class="grid-series">
                    <?php foreach ($resultadoBusca as $serie): ?>
                        <div class="card-serie">
                            <img src="<?php echo htmlspecialchars($serie['imagem_url']); ?>" alt="<?php echo htmlspecialchars($serie['titulo']); ?>">
                            <h3><?php echo htmlspecialchars($serie['titulo']); ?></h3>
                            <p>Avaliações: <?php echo $serie['total_avaliacoes']; ?></p>
                            <p>Nota média: <?php echo number_format($serie['media_nota'], 1); ?>/10</p>
                            <a href="serie.php?id=<?php echo $serie['id']; ?>" class="botao-entrar">Ver detalhes</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #666;">Nenhuma série encontrada.</p>
            <?php endif; ?>
        <?php else: ?>
            <!-- Exibir todas as séries -->
            <h2 style="color: #6A53B8; margin-bottom: 20px;">Todas as Séries</h2>
            <?php if (count($series) > 0): ?>
                <div class="grid-series">
                    <?php foreach ($series as $serie): ?>
                        <div class="card-serie">
                            <img src="<?php echo htmlspecialchars($serie['imagem_url']); ?>" alt="<?php echo htmlspecialchars($serie['titulo']); ?>">
                            <h3><?php echo htmlspecialchars($serie['titulo']); ?></h3>
                            <p>Avaliações: <?php echo $serie['total_avaliacoes']; ?></p>
                            <p>Nota média: <?php echo number_format($serie['media_nota'], 1); ?>/10</p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #666;">Nenhuma série cadastrada ainda.</p>
            <?php endif; ?>
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
    </script>

</body>

</html>