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
            <li><a href="/explorar.php"><img src="/src/assets/img/logo-text.png" style="width: 25%" alt="LOGO"></a></li>
            <li><a href="explorar.php">Explorar</a></li>
            <li><a href="comunidade.php">Comunidade</a></li>
        </ul>
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

    <!-- Modal avaliacao conta -->
    <div id="modalAvaliacoes" class="modal">
        <div class="modal-login">
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

    <!-- Modal de Avaliações -->
    <div id="modalAvaliacoes" class="modal">
        <div class="modal-avaliacoes">
            <button class="close" id="closeAvaliacoes">
                <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg" style="stroke: #6A53B8;">
                    <path d="M11.5 22C17.299 22 22 17.299 22 11.5C22 5.70101 17.299 1 11.5 1C5.70101 1 1 5.70101 1 11.5C1 17.299 5.70101 22 11.5 22Z" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M7.30005 11.5H15.7" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M11.5 7.2998V15.6998" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                </svg>

            </button>

            <div class="modal-avaliacoes-header">
                <h2 class="titulo" id="tituloSerie"></h2>
                <button class="botao-nova-avaliacao" id="btnNovaAvaliacao">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="white" stroke-width="2" />
                        <path d="M12 8V16M8 12H16" stroke="white" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    Nova Avaliação
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

                    <style>
                        /* 1. MÁGICA DO CSS: Inverte a ordem visual das estrelas (para que o hover funcione corretamente) */
                        .rating-input {
                            direction: rtl;
                            display: inline-block;
                            /* Garante que o usuário possa selecionar (apertar) as estrelas */
                            user-select: none;
                        }

                        /* 2. Esconde os botões de rádio originais (input type="radio") */
                        .rating-input>input {
                            display: none;
                        }

                        /* 3. Estilo Básico para a Estrela (Label) */
                        .rating-input>label {
                            cursor: pointer;
                            font-size: 0;
                            /* Esconde o caractere '★' que está no HTML */
                            padding: 2px;
                            display: inline-block;
                            width: 22px;
                            /* Largura do seu SVG */
                            height: 22px;
                            /* Altura do seu SVG */
                            transition: background-image 0.2s;
                            /* Animação suave */

                            /* ESTRELA VAZIA (Padrão) */
                            /* Seu primeiro SVG (estrela vazia) em formato de Data URI */
                            background-image: url("data:image/svg+xml,%3Csvg width='22' height='22' viewBox='0 0 22 22' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M10.5288 1.29489C10.5726 1.20635 10.6403 1.13183 10.7242 1.07972C10.8081 1.02761 10.905 1 11.0038 1C11.1025 1 11.1994 1.02761 11.2833 1.07972C11.3672 1.13183 11.4349 1.20635 11.4788 1.29489L13.7888 5.97389C13.9409 6.28186 14.1656 6.5483 14.4434 6.75035C14.7212 6.95239 15.0439 7.08401 15.3838 7.13389L20.5498 7.88989C20.6476 7.90408 20.7396 7.94537 20.8152 8.00909C20.8909 8.07282 20.9472 8.15644 20.9778 8.2505C21.0084 8.34456 21.012 8.4453 20.9883 8.54133C20.9647 8.63736 20.9146 8.72485 20.8438 8.79389L17.1078 12.4319C16.8614 12.672 16.677 12.9684 16.5706 13.2955C16.4642 13.6227 16.4388 13.9708 16.4968 14.3099L17.3788 19.4499C17.396 19.5477 17.3855 19.6485 17.3483 19.7406C17.311 19.8327 17.2487 19.9125 17.1683 19.9709C17.0879 20.0293 16.9927 20.0639 16.8936 20.0708C16.7945 20.0777 16.6955 20.0566 16.6078 20.0099L11.9898 17.5819C11.6855 17.4221 11.3469 17.3386 11.0033 17.3386C10.6596 17.3386 10.321 17.4221 10.0168 17.5819L5.39975 20.0099C5.31208 20.0563 5.21315 20.0772 5.1142 20.0701C5.01526 20.0631 4.92027 20.0285 4.84005 19.9701C4.75982 19.9118 4.69759 19.8321 4.66041 19.7401C4.62323 19.6482 4.61261 19.5476 4.62975 19.4499L5.51075 14.3109C5.56895 13.9716 5.54374 13.6233 5.43729 13.2959C5.33084 12.9686 5.14636 12.672 4.89975 12.4319L1.16375 8.79489C1.09235 8.72593 1.04175 8.63829 1.01772 8.54197C0.993684 8.44565 0.99719 8.34451 1.02783 8.25008C1.05847 8.15566 1.11502 8.07174 1.19103 8.00788C1.26704 7.94402 1.35946 7.90279 1.45775 7.88889L6.62275 7.13389C6.96301 7.08439 7.28614 6.95295 7.56434 6.75088C7.84253 6.54881 8.06746 6.28216 8.21975 5.97389L10.5288 1.29489Z' stroke='black' stroke-opacity='0.6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
                            background-repeat: no-repeat;
                            background-size: contain;
                        }

                        /* 4. Mudar para ESTRELA CHEIA ao passar o mouse (HOVER) ou SELECIONAR (CHECKED) */
                        /* Seleciona a estrela clicada e todas as estrelas de maior valor depois dela */
                        .rating-input>input:checked~label,
                        /* Seleciona a estrela que está com o mouse e todas as estrelas de maior valor depois dela */
                        .rating-input>label:hover,
                        .rating-input>label:hover~label {
                            /* ESTRELA CHEIA */
                            /* Seu segundo SVG (estrela cheia) em formato de Data URI */
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
        // Carregar avaliações
        function carregarAvaliacoes(serieId) {
            const conteudo = document.getElementById('conteudoAvaliacoes');
            conteudo.innerHTML = '<div class="loading">Carregando avaliações...</div>';

            fetch(`buscar_avaliacoes.php?serie_id=${serieId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.avaliacoes && data.avaliacoes.length > 0) {
                        conteudo.innerHTML = data.avaliacoes.map(av => `
                    <div class="avaliacao-item" data-usuario-id="${av.usuario_id}" style="cursor: pointer;">
                        <div class="avaliacao-header">
                            <div class="avaliacao-usuario">
                                <div class="avatar-usuario">${av.usuario_nome.charAt(0).toUpperCase()}</div>
                                <span class="usuario-nome">${av.usuario_nome}</span>
                                <div class="avaliacao-nota">${gerarEstrelas(av.nota)}</div>
                            </div>
                        </div>
                        <p class="avaliacao-comentario">${av.comentario}</p>
                        <span class="avaliacao-data">${formatarData(av.data_avaliacao)}</span>
                    </div>
                `).join('');

                        // Adicionar clique para ir ao perfil
                        document.querySelectorAll('.avaliacao-item').forEach(item => {
                            item.addEventListener('click', function() {
                                window.location.href = `perfil.php?id=${this.dataset.usuarioId}`;
                            });
                        });
                    } else {
                        conteudo.innerHTML = '<p class="sem-avaliacoes">Nenhuma avaliação ainda. Seja o primeiro a avaliar!</p>';
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar avaliações:', error);
                    conteudo.innerHTML = '<p class="erro-avaliacoes">Erro ao carregar avaliações. Tente novamente.</p>';
                });
        }

        /**
         * Gera um conjunto de estrelas HTML (SVGs) baseado em uma nota de 1 a 5.
         * A nota determina quantas estrelas serão preenchidas.
         * * @param {number} nota A nota para exibição, de 1 a 5.
         * @returns {string} Uma string HTML contendo os SVGs das estrelas.
         */
        function gerarEstrelas(nota) {
            let estrelas = '';

            // SVG para uma estrela PREENCHIDA (amarela com contorno escuro)
            const estrelaPreenchida = `
        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10.5268 1.29489C10.5706 1.20635 10.6383 1.13183 10.7223 1.07972C10.8062 1.02761 10.903 1 11.0018 1C11.1006 1 11.1974 1.02761 11.2813 1.07972C11.3653 1.13183 11.433 1.20635 11.4768 1.29489L13.7868 5.97389C13.939 6.28186 14.1636 6.5483 14.4414 6.75035C14.7192 6.95239 15.0419 7.08401 15.3818 7.13389L20.5478 7.88989C20.6457 7.90408 20.7376 7.94537 20.8133 8.00909C20.8889 8.07282 20.9452 8.15644 20.9758 8.2505C21.0064 8.34456 21.0101 8.4453 20.9864 8.54133C20.9627 8.63736 20.9126 8.72485 20.8418 8.79389L17.1058 12.4319C16.8594 12.672 16.6751 12.9684 16.5686 13.2955C16.4622 13.6227 16.4369 13.9708 16.4948 14.3099L17.3768 19.4499C17.3941 19.5477 17.3835 19.6485 17.3463 19.7406C17.3091 19.8327 17.2467 19.9125 17.1663 19.9709C17.086 20.0293 16.9908 20.0639 16.8917 20.0708C16.7926 20.0777 16.6935 20.0566 16.6058 20.0099L11.9878 17.5819C11.6835 17.4221 11.345 17.3386 11.0013 17.3386C10.6576 17.3386 10.3191 17.4221 10.0148 17.5819L5.3978 20.0099C5.31013 20.0563 5.2112 20.0772 5.11225 20.0701C5.0133 20.0631 4.91832 20.0285 4.83809 19.9701C4.75787 19.9118 4.69563 19.8321 4.66041 19.7401C4.62323 19.6482 4.61261 19.5476 4.62975 19.4499L5.5088 14.3109C5.567 13.9716 5.54178 13.6233 5.43534 13.2959C5.32889 12.9686 5.14441 12.672 4.8978 12.4319L1.1618 8.79489C1.09039 8.72593 1.03979 8.63829 1.01576 8.54197C0.991731 8.44565 0.995237 8.34451 1.02588 8.25008C1.05652 8.15566 1.11307 8.07174 1.18908 8.00788C1.26509 7.94402 1.3575 7.90279 1.4558 7.88889L6.6208 7.13389C6.96106 7.08439 7.28419 6.95295 7.56238 6.75088C7.84058 6.54881 8.0655 6.28216 8.2178 5.97389L10.5268 1.29489Z"
                fill="#FFF600"
                stroke="black"
                stroke-opacity="0.6"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round" />
        </svg>
    `;

            // SVG para uma estrela VAZIA (apenas contorno escuro)
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
                // Se o índice (i) for menor ou igual à nota, adiciona a estrela preenchida
                if (i <= nota) {
                    estrelas += estrelaPreenchida;
                } else {
                    // Caso contrário, adiciona a estrela vazia
                    estrelas += estrelaVazia;
                }
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