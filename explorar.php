<?php
session_start();
require_once('src/SerieDAO.php');
require_once('src/ClassificacaoDAO.php');
require_once('src/GeneroDAO.php');
require_once('src/AvaliacaoDAO.php');
require_once('src/UsuarioDAO.php');
require_once('src/FavoritoDAO.php');

$classificacoes = ClassificacaoDAO::listar();
$generos = GeneroDAO::listar();

// Capturar filtros
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$genero_id = isset($_GET['genero']) && $_GET['genero'] !== '' ? $_GET['genero'] : null;
$classificacao_id = isset($_GET['classificacao']) && $_GET['classificacao'] !== '' ? $_GET['classificacao'] : null;

// Verificar se há algum filtro ativo
$temFiltro = !empty($buscar) || $genero_id !== null || $classificacao_id !== null;

// Verifica se o usuário logado é admin
$eh_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

// Buscar séries com ou sem filtros
if ($temFiltro) {
    $series = SerieDao::buscar($buscar, $genero_id, $classificacao_id);
} else {
    $series = SerieDao::listar();
}

// Buscar favoritos do usuário logado
$favoritos_usuario = [];
if (isset($_SESSION['usuario_id'])) {
    $favoritos_lista = FavoritoDAO::listarPorUsuario($_SESSION['usuario_id']);
    foreach ($favoritos_lista as $fav) {
        $favoritos_usuario[] = $fav['id'];
    }
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
    <link rel="shortcut icon" href="/src/assets/icons/favicon.ico" type="image/x-icon">
    <title>FrameFlow | Opiniões que guiam suas próximas maratonas</title>
</head>

<body>
    <header>
        <!-- Menu Toggle para dispositivos móveis -->
        <input type="checkbox" id="menu-toggle">
        <label for="menu-toggle" class="menu-icon">
            <svg fill="#ffffff" width="30" height="30" viewBox="0 0 100 80">
                <rect width="100" height="10"></rect>
                <rect y="30" width="100" height="10"></rect>
                <rect y="60" width="100" height="10"></rect>
            </svg>
        </label>

        <!-- Navegação principal -->
        <ul>
            <!-- Busca de Usuários -->
            <div class="search-container">
                <div class="search-box">
                    <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="11" cy="11" r="8" stroke="#6A53B8" stroke-width="2" />
                        <path d="M21 21L16.65 16.65" stroke="#6A53B8" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    <input type="text" class="search-input" placeholder="Buscar usuários..." id="searchUsers" autocomplete="off">
                    <button class="clear-search" id="clearSearch">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
                <div class="search-results" id="searchResults"></div>
            </div>

            <li style="background-color: #fff; padding: 10px; border-radius: 20px;">
                <a href="#" onclick="abrirModalJogos(); return false;">
                    <svg width="26" height="19" viewBox="0 0 26 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.80005 8.2H10.6" stroke="#6A53B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M8.19995 5.79999V10.6" stroke="#6A53B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M16.6001 9.39999H16.6121" stroke="#6A53B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M20.2 6.99999H20.212" stroke="#6A53B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M19.384 1H6.616C5.42834 1.00028 4.28294 1.44085 3.40124 2.23656C2.51954 3.03226 1.96414 4.12659 1.8424 5.308C1.8352 5.3704 1.8304 5.4292 1.822 5.4904C1.7248 6.2992 1 12.3472 1 14.2C1 15.1548 1.37928 16.0705 2.05442 16.7456C2.72955 17.4207 3.64522 17.8 4.6 17.8C5.8 17.8 6.4 17.2 7 16.6L8.6968 14.9032C9.14678 14.4531 9.75713 14.2001 10.3936 14.2H15.6064C16.2429 14.2001 16.8532 14.4531 17.3032 14.9032L19 16.6C19.6 17.2 20.2 17.8 21.4 17.8C22.3548 17.8 23.2705 17.4207 23.9456 16.7456C24.6207 16.0705 25 15.1548 25 14.2C25 12.346 24.2752 6.2992 24.178 5.4904C24.1696 5.4304 24.1648 5.3704 24.1576 5.3092C24.0361 4.12757 23.4809 3.03295 22.5991 2.237C21.7174 1.44105 20.5719 1.00031 19.384 1Z" stroke="#6A53B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a>
            </li>
            <li><a href="explorar.php">Explorar</a></li>
            <li><a href="comunidade.php">Comunidade</a></li>
        </ul>
        <!-- Perfil do usuário -->
        <div class="header-perfil">
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
                    Fazer Login
                </a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Barra de busca de séries -->
    <section style="padding: 20px; max-width: 90%; margin: 0 auto;">
        <!-- Formulário de Busca em Linha -->
        <form method="GET" action="explorar.php" style="margin: 0 auto 30px; width: 100%;">
            <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center; justify-content: center;">
                <input type="text" name="buscar" placeholder="Pesquisar por título de série" class="input-estilizado" value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>" style="flex: 1; min-width: 200px; padding: 12px; font-size: 16px;">

                <select name="genero" class="input-estilizado" style="flex: 1; min-width: 200px; padding: 12px; font-size: 16px;">
                    <option value="">Todos os gêneros</option>
                    <?php foreach ($generos as $genero): ?>
                        <option value="<?php echo $genero['id']; ?>" <?php echo (isset($_GET['genero']) && $_GET['genero'] == $genero['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($genero['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="classificacao" class="input-estilizado" style="flex: 1; min-width: 200px; padding: 12px; font-size: 16px;">
                    <option value="">Todas as classificações</option>
                    <?php foreach ($classificacoes as $classificacao): ?>
                        <option value="<?php echo $classificacao['id']; ?>" <?php echo (isset($_GET['classificacao']) && $_GET['classificacao'] == $classificacao['id']) ? 'selected' : ''; ?>>
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
                    <?php $isFavorito = in_array($serie['id'], $favoritos_usuario); ?>
                    <div class="card-serie" data-serie-id="<?php echo $serie['id']; ?>">
                        <!-- Botão de Favoritar -->
                        <button class="btn-favorito <?php echo $isFavorito ? 'favorito-ativo' : ''; ?>"
                            data-serie-id="<?php echo $serie['id']; ?>"
                            onclick="toggleFavorito(event, <?php echo $serie['id']; ?>)"
                            title="<?php echo $isFavorito ? 'Remover dos favoritos' : 'Adicionar aos favoritos'; ?>">
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                            </svg>
                        </button>
                        <img src="<?php echo htmlspecialchars($serie['imagem_url']); ?>" alt="<?php echo htmlspecialchars($serie['titulo']); ?>">
                        <h3><?php echo htmlspecialchars($serie['titulo']); ?></h3>
                        <p>Avaliações: <?php echo $serie['total_avaliacoes']; ?></p>
                        <p>Nota média: <?php echo number_format(($serie['media_nota'] * 2), 1); ?>/10</p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #666;">Nenhuma série encontrada com os filtros selecionados.</p>
        <?php endif; ?>

    </section>

    <!-- Modal de Jogos (ÚNICA VERSÃO) -->
    <div id="modalJogos" class="modal">
        <div class="modal-jogos-content">
            <button class="close" id="closeJogos">
                <svg width="24" height="24" viewBox="0 0 276 275" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M137.798 271C211.528 271 271.298 211.23 271.298 137.5C271.298 63.77 211.528 4 137.798 4C64.0683 4 4.29834 63.77 4.29834 137.5C4.29834 211.23 64.0683 271 137.798 271Z" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M177.848 97.4497L97.7479 177.55" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M97.7479 97.4497L177.848 177.55" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>

            <h2 class="modal-jogos-titulo">Escolha seu jogo</h2>

            <div class="jogos-grid">
                <!-- Quebra-Cabeça -->
                <a href="#" onclick="verificarLoginJogo(event, 'quebra_cabeca.php')" class="jogo-card">
                    <div class="jogo-icon">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 14H7C7.55228 14 8 14.4477 8 15V19C8 19.5523 7.55228 20 7 20H4C3.44772 20 3 19.5523 3 19V15C3 14.4477 3.44772 14 4 14Z" fill="white" />
                            <path d="M10 14H13C13.5523 14 14 14.4477 14 15V19C14 19.5523 13.5523 20 13 20H10C9.44772 20 9 19.5523 9 19V15C9 14.4477 9.44772 14 10 14Z" fill="white" />
                            <path d="M16 14H19C19.5523 14 20 14.4477 20 15V19C20 19.5523 19.5523 20 19 20H16C15.4477 20 15 19.5523 15 19V15C15 14.4477 15.4477 14 16 14Z" fill="white" />
                            <path d="M4 8H7C7.55228 8 8 8.44772 8 9V12C8 12.5523 7.55228 13 7 13H4C3.44772 13 3 12.5523 3 12V9C3 8.44772 3.44772 8 4 8Z" fill="white" />
                            <path d="M10 4H13C13.5523 4 14 4.44772 14 5V8C14 8.55228 13.5523 9 13 9H10C9.44772 9 9 8.55228 9 8V5C9 4.44772 9.44772 4 10 4Z" fill="white" />
                            <path d="M16 8H19C19.5523 8 20 8.44772 20 9V12C20 12.5523 19.5523 13 19 13H16C15.4477 13 15 12.5523 15 12V9C15 8.44772 15.4477 8 16 8Z" fill="white" />
                        </svg>
                    </div>
                    <h3 class="jogo-nome">Quebra-Cabeça</h3>
                    <p class="jogo-descricao">Monte a imagem do seu filme favorito</p>
                </a>

                <!-- Jogo da Memória -->
                <a href="#" onclick="verificarLoginJogo(event, 'jogo_memoria.php')" class="jogo-card">
                    <div class="jogo-icon">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="white" />
                            <path d="M12 8V16M8 12H16" stroke="#6a53b8" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>
                    <h3 class="jogo-nome">Jogo da Memória</h3>
                    <p class="jogo-descricao">Encontre os pares de filmes e séries</p>
                </a>

                <!-- Cruzadinha -->
                <a href="#" onclick="verificarLoginJogo(event, 'cruzadinha.php')" class="jogo-card">
                    <div class="jogo-icon">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="3" width="5" height="5" fill="white" />
                            <rect x="9" y="3" width="5" height="5" fill="white" />
                            <rect x="15" y="3" width="5" height="5" fill="white" />
                            <rect x="3" y="9" width="5" height="5" fill="white" />
                            <rect x="9" y="9" width="5" height="5" fill="#6a53b8" />
                            <rect x="15" y="9" width="5" height="5" fill="white" />
                            <rect x="3" y="15" width="5" height="5" fill="white" />
                            <rect x="9" y="15" width="5" height="5" fill="white" />
                            <rect x="15" y="15" width="5" height="5" fill="white" />
                        </svg>
                    </div>
                    <h3 class="jogo-nome">Cruzadinha</h3>
                    <p class="jogo-descricao">Descubra o nome dos filmes e séries</p>
                </a>
            </div>
        </div>
    </div>
    <!-- Modal fazer login -->
    <div id="modal" class="modal">
        <div class="modal-login">
            <button class="close" id="closeLogin">
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
                        <input type="text" name="nome_completo" placeholder="Digite seu nome completo" class="input-estilizado" required>
                        <input type="email" name="email" placeholder="Digite seu e-mail" class="input-estilizado" required>
                        <input type="password" name="senha" id="senha" placeholder="Crie uma senha" class="input-estilizado" required>
                        <input type="password" name="confirmar_senha" id="confirmar_senha" placeholder="Confirme sua senha" class="input-estilizado" required>
                    </div>
                    <a href="#" id="voltarLogin">Já tem uma conta? <strong>Faça login aqui</strong></a>
                </div>

                <button class="botao-entrar" type="submit">Cadastrar</button>
            </form>
        </div>
    </div>

    <!-- Modal de Avaliações -->
    <div id="modalAvaliacoes" class="modal">
        <div class="modal-avaliacoes">
            <button class="close" id="closeAvaliacoes">
                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 38C29.9411 38 38 29.9411 38 20C38 10.0589 29.9411 2 20 2C10.0589 2 2 10.0589 2 20C2 29.9411 10.0589 38 20 38Z" stroke="black" stroke-opacity="0.6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M25.3999 14.6L14.5999 25.4" stroke="black" stroke-opacity="0.6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M14.5999 14.6L25.3999 25.4" stroke="black" stroke-opacity="0.6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>

            <div class="modal-avaliacoes-header">
                <h2 class="titulo" id="tituloSerie"></h2>

                <?php if (!$eh_admin): ?>
                    <button class="botao-nova-avaliacao" id="btnNovaAvaliacao">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" stroke="white" stroke-width="2" />
                            <path d="M12 8V16M8 12H16" stroke="white" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        Nova Avaliação
                    </button>
                <?php endif; ?>
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

                    <style>
                        .rating-input {
                            direction: rtl;
                            display: inline-block;
                            user-select: none;
                        }

                        .rating-input>input {
                            display: none;
                        }

                        .rating-input>label {
                            cursor: pointer;
                            font-size: 0;
                            padding: 2px;
                            display: inline-block;
                            width: 22px;
                            height: 22px;
                            transition: background-image 0.2s;
                            background-image: url("data:image/svg+xml,%3Csvg width='22' height='22' viewBox='0 0 22 22' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M10.5288 1.29489C10.5726 1.20635 10.6403 1.13183 10.7242 1.07972C10.8081 1.02761 10.905 1 11.0038 1C11.1025 1 11.1994 1.02761 11.2833 1.07972C11.3672 1.13183 11.4349 1.20635 11.4788 1.29489L13.7888 5.97389C13.9409 6.28186 14.1656 6.5483 14.4434 6.75035C14.7212 6.95239 15.0439 7.08401 15.3838 7.13389L20.5498 7.88989C20.6476 7.90408 20.7396 7.94537 20.8152 8.00909C20.8909 8.07282 20.9472 8.15644 20.9778 8.2505C21.0084 8.34456 21.012 8.4453 20.9883 8.54133C20.9647 8.63736 20.9146 8.72485 20.8438 8.79389L17.1078 12.4319C16.8614 12.672 16.677 12.9684 16.5706 13.2955C16.4642 13.6227 16.4388 13.9708 16.4968 14.3099L17.3788 19.4499C17.396 19.5477 17.3855 19.6485 17.3483 19.7406C17.311 19.8327 17.2487 19.9125 17.1683 19.9709C17.0879 20.0293 16.9927 20.0639 16.8936 20.0708C16.7945 20.0777 16.6955 20.0566 16.6078 20.0099L11.9898 17.5819C11.6855 17.4221 11.3469 17.3386 11.0033 17.3386C10.6596 17.3386 10.321 17.4221 10.0168 17.5819L5.39975 20.0099C5.31208 20.0563 5.21315 20.0772 5.1142 20.0701C5.01526 20.0631 4.92027 20.0285 4.84005 19.9701C4.75982 19.9118 4.69759 19.8321 4.66041 19.7401C4.62323 19.6482 4.61261 19.5476 4.62975 19.4499L5.51075 14.3109C5.56895 13.9716 5.54374 13.6233 5.43729 13.2959C5.33084 12.9686 5.14636 12.672 4.89975 12.4319L1.16375 8.79489C1.09235 8.72593 1.04175 8.63829 1.01772 8.54197C0.993684 8.44565 0.99719 8.34451 1.02783 8.25008C1.05847 8.15566 1.11502 8.07174 1.19103 8.00788C1.26704 7.94402 1.35946 7.90279 1.45775 7.88889L6.62275 7.13389C6.96301 7.08439 7.28614 6.95295 7.56434 6.75088C7.84253 6.54881 8.06746 6.28216 8.21975 5.97389L10.5288 1.29489Z' stroke='black' stroke-opacity='0.6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
                            background-repeat: no-repeat;
                            background-size: contain;
                        }

                        .rating-input>input:checked~label,
                        .rating-input>label:hover,
                        .rating-input>label:hover~label {
                            background-image: url("data:image/svg+xml,%3Csvg width='22' height='22' viewBox='0 0 22 22' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M10.5268 1.29489C10.5706 1.20635 10.6383 1.13183 10.7223 1.07972C10.8062 1.02761 10.903 1 11.0018 1C11.1006 1 11.1974 1.02761 11.2813 1.07972C11.3653 1.13183 11.433 1.20635 11.4768 1.29489L13.7868 5.97389C13.939 6.28186 14.1636 6.5483 14.4414 6.75035C14.7192 6.95239 15.0419 7.08401 15.3818 7.13389L20.5478 7.88989C20.6457 7.90408 20.7376 7.94537 20.8133 8.00909C20.8889 8.07282 20.9452 8.15644 20.9758 8.2505C21.0064 8.34456 21.0101 8.4453 20.9864 8.54133C20.9627 8.63736 20.9126 8.72485 20.8418 8.79389L17.1058 12.4319C16.8594 12.672 16.6751 12.9684 16.5686 13.2955C16.4622 13.6227 16.4369 13.9708 16.4948 14.3099L17.3768 19.4499C17.3941 19.5477 17.3835 19.6485 17.3463 19.7406C17.3091 19.8327 17.2467 19.9125 17.1663 19.9709C17.086 20.0293 16.9908 20.0639 16.8917 20.0708C16.7926 20.0777 16.6935 20.0566 16.6058 20.0099L11.9878 17.5819C11.6835 17.4221 11.345 17.3386 11.0013 17.3386C10.6576 17.3386 10.3191 17.4221 10.0148 17.5819L5.3978 20.0099C5.31013 20.0563 5.2112 20.0772 5.11225 20.0701C5.0133 20.0631 4.91832 20.0285 4.83809 19.9701C4.75787 19.9118 4.69563 19.8321 4.65846 19.7401C4.62128 19.6482 4.61066 19.5476 4.6278 19.4499L5.5088 14.3109C5.567 13.9716 5.54178 13.6233 5.43534 13.2959C5.32889 12.9686 5.14441 12.672 4.8978 12.4319L1.1618 8.79489C1.09039 8.72593 1.03979 8.63829 1.01576 8.54197C0.991731 8.44565 0.995237 8.34451 1.02588 8.25008C1.05652 8.15566 1.11307 8.07174 1.18908 8.00788C1.26509 7.94402 1.3575 7.90279 1.4558 7.88889L6.6208 7.13389C6.96106 7.08439 7.28419 6.95295 7.56238 6.75088C7.84058 6.54881 8.0655 6.28216 8.2178 5.97389L10.5268 1.29489Z' fill='%23FFF600' stroke='black' stroke-opacity='0.6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
                        }
                    </style>

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
                    <textarea name="comentario" class="input-estilizado" rows="5" placeholder="Escreva sua opinião sobre a série..." required style="resize: vertical; min-height: 120px; max-width: 100%;"></textarea>
                </div>

                <button class="botao-entrar" type="submit">Publicar Avaliação</button>
            </form>
        </div>
    </div>

    <script>
        // Verifica se deve abrir o modal ao carregar a página
        window.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('login') === 'true') {
                document.getElementById('modal').style.display = 'block';
            }
        });

        // Voltar para o modal de login
        const voltarLoginBtn = document.getElementById('voltarLogin');
        if (voltarLoginBtn) {
            voltarLoginBtn.addEventListener('click', function(event) {
                event.preventDefault();
                document.getElementById('modalCriarConta').style.display = 'none';
                document.getElementById('modal').style.display = 'block';
            });
        }

        function abrirModalJogos() {
            const modal = document.getElementById('modalJogos');
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

        // Função para verificar login antes de acessar os jogos
        function verificarLoginJogo(event, urlJogo) {
            event.preventDefault();

            <?php if (isset($_SESSION['usuario_id'])): ?>
                window.location.href = urlJogo;
            <?php else: ?>
                fecharModal('modalJogos');
                mostrarNotificacao('erro', 'Login necessário', 'Você precisa estar logado para jogar!');

                setTimeout(function() {
                    document.getElementById('modal').style.display = 'block';
                }, 500);
            <?php endif; ?>
        }

        // Sistema de notificações popup
        function mostrarNotificacao(tipo, titulo, mensagem) {
            const notificacoesExistentes = document.querySelectorAll('.notificacao-popup');
            notificacoesExistentes.forEach(n => n.remove());

            const notificacao = document.createElement('div');
            notificacao.className = `notificacao-popup ${tipo}`;

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

            setTimeout(() => {
                notificacao.classList.add('show');
            }, 10);

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

        // Função para favoritar/desfavoritar série
        function toggleFavorito(event, serieId) {
            event.stopPropagation(); // Impede que o card seja clicado

            <?php if (!isset($_SESSION['usuario_id'])): ?>
                // Se não estiver logado, mostra notificação e abre modal de login
                mostrarNotificacao('erro', 'Login necessário', 'Você precisa estar logado para favoritar séries!');
                setTimeout(() => {
                    document.getElementById('modal').style.display = 'block';
                }, 500);
                return;
            <?php endif; ?>

            const btn = event.currentTarget;
            const isFavorito = btn.classList.contains('favorito-ativo');

            // Desabilita o botão temporariamente
            btn.disabled = true;
            btn.style.opacity = '0.6';

            fetch('toggle_favorito.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `serie_id=${serieId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        // Alterna a classe de favorito
                        if (data.isFavorito) {
                            btn.classList.add('favorito-ativo');
                            btn.title = 'Remover dos favoritos';
                        } else {
                            btn.classList.remove('favorito-ativo');
                            btn.title = 'Adicionar aos favoritos';
                        }

                        mostrarNotificacao('sucesso', 'Sucesso', data.mensagem);
                    } else {
                        mostrarNotificacao('erro', 'Erro', data.mensagem);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    mostrarNotificacao('erro', 'Erro', 'Erro ao processar favorito');
                })
                .finally(() => {
                    // Reabilita o botão
                    btn.disabled = false;
                    btn.style.opacity = '1';
                });
        }


        // Event Listeners dos Modais - CORRIGIDO
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['sucesso'])): ?>
                mostrarNotificacao('sucesso', 'Login efetuado com sucesso', '<?php echo addslashes($_SESSION['sucesso']); ?>');
                <?php unset($_SESSION['sucesso']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['sucesso_avaliacao'])): ?>
                mostrarNotificacao('sucesso', 'Avaliação registrada com sucesso', '<?php echo addslashes($_SESSION['sucesso_avaliacao']); ?>');
                <?php unset($_SESSION['sucesso_avaliacao']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['erro'])): ?>
                mostrarNotificacao('erro', 'Erro', '<?php echo addslashes($_SESSION['erro']); ?>');
                <?php unset($_SESSION['erro']); ?>
            <?php endif; ?>

            // Abrir modais
            const openModalBtn = document.getElementById('openModal');
            if (openModalBtn) {
                openModalBtn.addEventListener('click', function(event) {
                    event.preventDefault();
                    document.getElementById('modal').style.display = 'block';
                });
            }

            const abrirCriarContaBtn = document.getElementById('abrirCriarConta');
            if (abrirCriarContaBtn) {
                abrirCriarContaBtn.addEventListener('click', function(event) {
                    event.preventDefault();
                    document.getElementById('modal').style.display = 'none';
                    document.getElementById('modalCriarConta').style.display = 'block';
                });
            }

            // FECHAR MODAIS COM IDs ÚNICOS
            const closeLogin = document.getElementById('closeLogin');
            if (closeLogin) {
                closeLogin.addEventListener('click', function() {
                    document.getElementById('modal').style.display = 'none';
                });
            }

            const closeCriar = document.getElementById('closeCriar');
            if (closeCriar) {
                closeCriar.addEventListener('click', function() {
                    document.getElementById('modalCriarConta').style.display = 'none';
                });
            }

            const closeJogos = document.getElementById('closeJogos');
            if (closeJogos) {
                closeJogos.addEventListener('click', function() {
                    fecharModal('modalJogos');
                });
            }

            const closeAvaliacoes = document.getElementById('closeAvaliacoes');
            if (closeAvaliacoes) {
                closeAvaliacoes.addEventListener('click', function() {
                    document.getElementById('modalAvaliacoes').style.display = 'none';
                });
            }

            const closeNovaAvaliacao = document.getElementById('closeNovaAvaliacao');
            if (closeNovaAvaliacao) {
                closeNovaAvaliacao.addEventListener('click', function() {
                    document.getElementById('modalNovaAvaliacao').style.display = 'none';
                });
            }

            // Fechar modais clicando fora
            window.addEventListener('click', function(event) {
                if (event.target.classList.contains('modal')) {
                    event.target.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });

            // Fechar com ESC
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

            // Validar senhas
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
                    const serieId = this.dataset.serieId;
                    abrirModalAvaliacoes(serieId, titulo);
                });
            });
        });

        // Abrir modal de avaliações
        function abrirModalAvaliacoes(serieId, titulo) {
            document.getElementById('modalAvaliacoes').style.display = 'block';
            document.getElementById('tituloSerie').textContent = titulo;
            carregarAvaliacoes(serieId);
            document.getElementById('modalAvaliacoes').dataset.serieId = serieId;
        }

        // ============================================
        // FUNÇÃO carregarAvaliacoes ATUALIZADA
        // ============================================

        // Substitua a função carregarAvaliacoes completa no explorar.php por esta versão corrigida:

        function carregarAvaliacoes(serieId) {
            const conteudo = document.getElementById('conteudoAvaliacoes');
            conteudo.innerHTML = '<div class="loading">Carregando avaliações...</div>';

            fetch(`buscar_avaliacoes.php?serie_id=${serieId}`)
                .then(response => {
                    // Verifica se a resposta HTTP foi bem-sucedida (status 200-299)
                    if (!response.ok) {
                        // Se não foi, lê a resposta como texto para ver a mensagem de erro do PHP
                        return response.text().then(text => {
                            throw new Error(`Erro do Servidor (HTTP ${response.status}): ${text}`);
                        });
                    }

                    // Verifica se o servidor realmente enviou JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new TypeError("Oops, não recebemos JSON! A resposta foi: " + response.statusText);
                    }

                    // Se tudo estiver OK, processa o JSON
                    return response.json();
                })
                .then(data => {
                    if (data.avaliacoes && data.avaliacoes.length > 0) {
                        conteudo.innerHTML = data.avaliacoes.map(av => {
                            const avatarHtml = av.foto_perfil && av.foto_perfil !== '' ?
                                `<img src="uploads/perfil/${av.foto_perfil}" alt="${av.usuario_nome}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">` :
                                av.usuario_nome.charAt(0).toUpperCase();

                            let botoesVotacao = '';
                            if (av.pode_curtir) {
                                const likeAtivo = av.usuario_voto === 1 ? 'ativo' : '';
                                const dislikeAtivo = av.usuario_voto === -1 ? 'ativo' : '';
                                const likeBg = av.usuario_voto === 1 ? '#6A53B8' : 'transparent';
                                const likeColor = av.usuario_voto === 1 ? 'white' : '#6A53B8';
                                const dislikeBg = av.usuario_voto === -1 ? '#6A53B8' : 'transparent';
                                const dislikeColor = av.usuario_voto === -1 ? 'white' : '#6A53B8';

                                botoesVotacao = `
                            <div class="votacao-container" style="display: flex; gap: 15px;">
                                <button class="btn-voto btn-like ${likeAtivo}" 
                                    data-avaliacao-id="${av.id}"
                                    data-tipo="like"
                                    onclick="event.stopPropagation(); processarVoto(${av.id}, 1);"
                                    title="Gostei desta avaliação"
                                    style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; border: 2px solid #6A53B8; background: ${likeBg}; color: ${likeColor}; border-radius: 20px; cursor: pointer; font-weight: 600; transition: all 0.3s;">
                                    👍 <span class="contador-votos">${av.total_likes || 0}</span>
                                </button>
                                <button class="btn-voto btn-dislike ${dislikeAtivo}" 
                                    data-avaliacao-id="${av.id}"
                                    data-tipo="dislike"
                                    onclick="event.stopPropagation(); processarVoto(${av.id}, -1);"
                                    title="Não gostei desta avaliação"
                                    style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; border: 2px solid #6A53B8; background: ${dislikeBg}; color: ${dislikeColor}; border-radius: 20px; cursor: pointer; font-weight: 600; transition: all 0.3s;">
                                    👎 <span class="contador-votos">${av.total_dislikes || 0}</span>
                                </button>
                            </div>
                        `;
                            }

                            return `
                        <div class="avaliacao-item">
                            <div class="avaliacao-header-flex">
                                <div class="avaliacao-usuario-info" onclick="window.location.href='perfil.php?id=${av.usuario_id}'" style="cursor: pointer;">
                                    <div class="avatar-usuario">
                                        ${avatarHtml}
                                    </div>
                                    <div class="usuario-detalhes">
                                        <span class="usuario-nome">${av.usuario_nome}</span>
                                        <div class="avaliacao-nota">${gerarEstrelas(av.nota)}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="avaliacao-conteudo">
                                <p class="avaliacao-comentario">${av.comentario}</p>
                            </div>
                            <div class="avaliacao-footer-data" style="display: flex; justify-content: space-between; align-items: center;">
                                <span class="avaliacao-data">${formatarData(av.data_avaliacao)}</span>
                                ${botoesVotacao}
                            </div>
                        </div>
                    `;
                        }).join('');
                    } else {
                        conteudo.innerHTML = '<p class="sem-avaliacoes">Nenhuma avaliação ainda. Seja o primeiro a avaliar!</p>';
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar avaliações:', error);
                    // Agora o erro exibido no console será muito mais informativo!
                    conteudo.innerHTML = `<p class="erro-avaliacoes">Erro ao carregar avaliações. Verifique o console para mais detalhes.</p>`;
                });
        }


        // ============================================
        // FUNÇÃO PARA PROCESSAR VOTOS (LIKE/DISLIKE)
        // ============================================

        function processarVoto(avaliacaoId, tipoVoto) {
            // Verifica se o usuário está logado (adicione esta linha no seu PHP)
            <?php if (!isset($_SESSION['usuario_id'])): ?>
                mostrarNotificacao('erro', 'Login necessário', 'Você precisa estar logado para votar em avaliações!');
                setTimeout(() => {
                    document.getElementById('modal').style.display = 'block';
                }, 500);
                return;
            <?php endif; ?>

            const btnLike = document.querySelector(`[data-avaliacao-id="${avaliacaoId}"][data-tipo="like"]`);
            const btnDislike = document.querySelector(`[data-avaliacao-id="${avaliacaoId}"][data-tipo="dislike"]`);

            if (!btnLike || !btnDislike) return;

            // Desabilita ambos os botões temporariamente
            btnLike.disabled = true;
            btnDislike.disabled = true;
            btnLike.style.opacity = '0.6';
            btnDislike.style.opacity = '0.6';

            fetch('processar_voto.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `avaliacao_id=${avaliacaoId}&tipo_voto=${tipoVoto}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        // Atualiza os contadores
                        const contadorLikes = btnLike.querySelector('.contador-votos');
                        const contadorDislikes = btnDislike.querySelector('.contador-votos');

                        contadorLikes.textContent = data.total_likes;
                        contadorDislikes.textContent = data.total_dislikes;

                        // Remove classes ativas e reseta estilos
                        btnLike.classList.remove('ativo');
                        btnDislike.classList.remove('ativo');
                        btnLike.style.background = 'transparent';
                        btnLike.style.color = '#6A53B8';
                        btnDislike.style.background = 'transparent';
                        btnDislike.style.color = '#6A53B8';

                        // Adiciona classe ativa no botão correspondente (se houver voto)
                        if (data.voto_atual === 1) {
                            btnLike.classList.add('ativo');
                            btnLike.style.background = '#6A53B8';
                            btnLike.style.color = 'white';
                        } else if (data.voto_atual === -1) {
                            btnDislike.classList.add('ativo');
                            btnDislike.style.background = '#6A53B8';
                            btnDislike.style.color = 'white';
                        }

                        mostrarNotificacao('sucesso', 'Sucesso', data.mensagem);
                    } else {
                        mostrarNotificacao('erro', 'Erro', data.mensagem || 'Erro ao processar voto');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    mostrarNotificacao('erro', 'Erro', 'Erro ao processar voto');
                })
                .finally(() => {
                    // Reabilita ambos os botões
                    btnLike.disabled = false;
                    btnDislike.disabled = false;
                    btnLike.style.opacity = '1';
                    btnDislike.style.opacity = '1';
                });
        }

        function gerarEstrelas(nota) {
            let estrelas = '';
            const estrelaPreenchida = `
        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10.5268 1.29489C10.5706 1.20635 10.6383 1.13183 10.7223 1.07972C10.8062 1.02761 10.903 1 11.0018 1C11.1006 1 11.1974 1.02761 11.2813 1.07972C11.3653 1.13183 11.433 1.20635 11.4768 1.29489L13.7868 5.97389C13.939 6.28186 14.1636 6.5483 14.4414 6.75035C14.7192 6.95239 15.0419 7.08401 15.3818 7.13389L20.5478 7.88989C20.6457 7.90408 20.7376 7.94537 20.8133 8.00909C20.8889 8.07282 20.9452 8.15644 20.9758 8.2505C21.0064 8.34456 21.0101 8.4453 20.9864 8.54133C20.9627 8.63736 20.9126 8.72485 20.8418 8.79389L17.1058 12.4319C16.8594 12.672 16.6751 12.9684 16.5686 13.2955C16.4622 13.6227 16.4369 13.9708 16.4948 14.3099L17.3768 19.4499C17.3941 19.5477 17.3835 19.6485 17.3463 19.7406C17.3091 19.8327 17.2467 19.9125 17.1663 19.9709C17.086 20.0293 16.9908 20.0639 16.8917 20.0708C16.7926 20.0777 16.6935 20.0566 16.6058 20.0099L11.9878 17.5819C11.6835 17.4221 11.345 17.3386 11.0013 17.3386C10.6576 17.3386 10.3191 17.4221 10.0148 17.5819L5.3978 20.0099C5.31013 20.0563 5.2112 20.0772 5.11225 20.0701C5.0133 20.0631 4.91832 20.0285 4.83809 19.9701C4.75787 19.9118 4.69563 19.8321 4.65846 19.7401C4.62128 19.6482 4.61066 19.5476 4.6278 19.4499L5.5088 14.3109C5.567 13.9716 5.54178 13.6233 5.43534 13.2959C5.32889 12.9686 5.14441 12.672 4.8978 12.4319L1.1618 8.79489C1.09039 8.72593 1.03979 8.63829 1.01576 8.54197C0.991731 8.44565 0.995237 8.34451 1.02588 8.25008C1.05652 8.15566 1.11307 8.07174 1.18908 8.00788C1.26509 7.94402 1.3575 7.90279 1.4558 7.88889L6.6208 7.13389C6.96106 7.08439 7.28419 6.95295 7.56238 6.75088C7.84058 6.54881 8.0655 6.28216 8.2178 5.97389L10.5268 1.29489Z"
                fill="#FFF600"
                stroke="black"
                stroke-opacity="0.6"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round" />
        </svg>
    `;
            const estrelaVazia = `
        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10.5288 1.29489C10.5726 1.20635 10.6403 1.13183 10.7242 1.07972C10.8081 1.02761 10.905 1 11.0038 1C11.1025 1 11.1994 1.02761 11.2833 1.07972C11.3672 1.13183 11.4349 1.20635 11.4788 1.29489L13.7888 5.97389C13.9409 6.28186 14.1656 6.5483 14.4434 6.75035C14.7212 6.95239 15.0439 7.08401 15.3838 7.13389L20.5498 7.88989C20.6476 7.90408 20.7396 7.94537 20.8152 8.00909C20.8909 8.07282 20.9472 8.15644 20.9778 8.2505C21.0084 8.34456 21.012 8.4453 20.9883 8.54133C20.9647 8.63736 20.9146 8.72485 20.8438 8.79389L17.1078 12.4319C16.8614 12.672 16.677 12.9684 16.5706 13.2955C16.4642 13.6227 16.4388 13.9708 16.4968 14.3099L17.3788 19.4499C17.396 19.5477 17.3855 19.6485 17.3483 19.7406C17.311 19.8327 17.2487 19.9125 17.1683 19.9709C17.0879 20.0293 16.9927 20.0639 16.8936 20.0708C16.7945 20.0777 16.6955 20.0566 16.6078 20.0099L11.9898 17.5819C11.6855 17.4221 11.3469 17.3386 11.0033 17.3386C10.6596 17.3386 10.321 17.4221 10.0168 17.5819L5.39975 20.0099C5.31208 20.0563 5.21315 20.0772 5.1142 20.0701C5.01526 20.0631 4.92027 20.0285 4.84005 19.9701C4.75982 19.9118 4.69759 19.8321 4.66041 19.7401C4.62323 19.6482 4.61261 19.5476 4.62975 19.4499L5.51075 14.3109C5.56895 13.9716 5.54374 13.6233 5.43729 13.2959C5.33084 12.9686 5.14636 12.672 4.89975 12.4319L1.16375 8.79489C1.09235 8.72593 1.04175 8.63829 1.01772 8.54197C0.993684 8.44565 0.99719 8.34451 1.02783 8.25008C1.05847 8.15566 1.11502 8.07174 1.19103 8.00788C1.26704 7.94402 1.35946 7.90279 1.45775 7.88889L6.62275 7.13389C6.96301 7.08439 7.28614 6.95295 7.56434 6.75088C7.84253 6.54881 8.06746 6.28216 8.21975 5.97389L10.5288 1.29489Z"
                stroke="black"
                stroke-opacity="0.6"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round" />
        </svg>
    `;

            for (let i = 1; i <= 5; i++) {
                if (i <= nota) {
                    estrelas += estrelaPreenchida;
                } else {
                    estrelas += estrelaVazia;
                }
            }

            return estrelas;
        }

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
        const btnNovaAvaliacao = document.getElementById('btnNovaAvaliacao');
        if (btnNovaAvaliacao) {
            btnNovaAvaliacao.addEventListener('click', function() {
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
        }

        // Busca de usuários
        const searchInput = document.getElementById('searchUsers');
        const searchResults = document.getElementById('searchResults');
        const clearSearch = document.getElementById('clearSearch');
        let searchTimeout;

        searchInput.addEventListener('input', function() {
            const termo = this.value.trim();

            if (termo.length > 0) {
                clearSearch.classList.add('show');
            } else {
                clearSearch.classList.remove('show');
                searchResults.classList.remove('show');
                return;
            }

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                buscarUsuarios(termo);
            }, 300);
        });

        clearSearch.addEventListener('click', function() {
            searchInput.value = '';
            searchResults.classList.remove('show');
            clearSearch.classList.remove('show');
            searchInput.focus();
        });

        async function buscarUsuarios(termo) {
            if (termo.length < 2) {
                searchResults.classList.remove('show');
                return;
            }

            searchResults.innerHTML = '<div class="search-loading">Buscando...</div>';
            searchResults.classList.add('show');

            try {
                const response = await fetch(`buscar_usuarios.php?termo=${encodeURIComponent(termo)}`);
                const usuarios = await response.json();

                if (usuarios.length === 0) {
                    searchResults.innerHTML = '<div class="search-empty">Nenhum usuário encontrado</div>';
                } else {
                    let html = '';
                    usuarios.forEach(usuario => {
                        const avatarHtml = usuario.foto_perfil && usuario.foto_perfil !== '' ?
                            `<img src="uploads/perfil/${usuario.foto_perfil}" alt="${usuario.nome_completo}">` :
                            usuario.iniciais;

                        html += `
                    <a href="perfil.php?id=${usuario.id}" class="search-result-item">
                        <div class="user-avatar">${avatarHtml}</div>
                        <div class="user-info">
                            <div class="user-name">${usuario.nome_completo}</div>
                            <div class="user-email">${usuario.email}</div>
                        </div>
                    </a>
                `;
                    });
                    searchResults.innerHTML = html;
                }
            } catch (error) {
                searchResults.innerHTML = '<div class="search-empty">Erro ao buscar usuários</div>';
            }
        }

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.search-container')) {
                searchResults.classList.remove('show');
            }
        });

        searchResults.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    </script>

</body>

</html>