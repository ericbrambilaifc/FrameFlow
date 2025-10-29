<?php
session_start();
require_once 'src/ConexaoBD.php';

// Definir nível de dificuldade
$nivel = isset($_GET['nivel']) ? $_GET['nivel'] : 'facil';
$numeroPares = ($nivel === 'facil') ? 6 : (($nivel === 'medio') ? 8 : 10);

// Buscar séries para o jogo
function buscarSeriesJogo($limite)
{
    $conexao = ConexaoBD::conectar();

    $sql = "SELECT s.id, s.titulo, s.imagem_url, 
                   g.nome as genero, 
                   c.nome as classificacao
            FROM series s
            LEFT JOIN generos g ON s.genero_id = g.id
            LEFT JOIN classificacoes c ON s.classificacao_id = c.id
            ORDER BY RAND()
            LIMIT :limite";

    $stmt = $conexao->prepare($sql);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Buscar uma avaliação aleatória para dica difícil
function buscarAvaliacaoDica($serieId)
{
    $conexao = ConexaoBD::conectar();

    $sql = "SELECT comentario, nota 
            FROM avaliacoes 
            WHERE serie_id = :serie_id 
            ORDER BY RAND() 
            LIMIT 1";

    $stmt = $conexao->prepare($sql);
    $stmt->bindValue(':serie_id', $serieId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Buscar séries e preparar cartas
$series = buscarSeriesJogo($numeroPares);
$cartas = [];

foreach ($series as $serie) {
    // Buscar avaliação para o nível difícil
    $avaliacao = buscarAvaliacaoDica($serie['id']);

    // Criar duas cartas iguais (par)
    for ($i = 0; $i < 2; $i++) {
        $cartas[] = [
            'id' => $serie['id'],
            'titulo' => $serie['titulo'],
            'imagem' => $serie['imagem_url'],
            'genero' => $serie['genero'] ?? 'Desconhecido',
            'classificacao' => $serie['classificacao'] ?? 'Livre',
            'avaliacao' => $avaliacao['comentario'] ?? 'Série incrível!',
            'nota_avaliacao' => $avaliacao['nota'] ?? 5.0,
            'uniqueId' => uniqid() // ID único para cada carta
        ];
    }
}

// Embaralhar as cartas
shuffle($cartas);

// Configurações do jogo
$config = [
    'nivel' => $nivel,
    'pares' => $numeroPares,
    'totalCartas' => count($cartas),
    'pontuacao_base' => 1000,
    'penalidade_erro' => 50,
    'bonus_tempo' => 10, // pontos por segundo restante
    'tempo_limite' => 180 // 3 minutos
];
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jogo da Memória - FrameFlow</title>
    <link rel="stylesheet" href="global.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #fff;
            min-height: 100vh;
            padding: 2rem;
        }

        .container-jogo {
            max-width: 80%;
            margin: 0 auto;
        }

        .header-jogo {
            background: white;
            padding: 1.5rem 2rem;
            border-radius: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .header-jogo h1 {
            color: #6a53b8;
            font-size: 2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stats-container {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .stat-box {
            background: linear-gradient(135deg, #6a53b8 0%, #8b73d8 100%);
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 1rem;
            font-weight: bold;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.3rem;
        }

        .stat-box span:first-child {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .stat-box span:last-child {
            font-size: 1.3rem;
        }

        .btn-menu {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-voltar {
            background: #f0f0f0;
            color: #6a53b8;
        }

        .btn-voltar:hover {
            background: #e0e0e0;
            transform: translateY(-2px);
        }

        .btn-reiniciar {
            background: #f0f0f0;
            color: #6a53b8;
        }

        .btn-reiniciar:hover {
            background: #e0e0e0;
            transform: translateY(-2px);
        }

        .nivel-selector {
            background: white;
            padding: 1rem 2rem;
            border-radius: 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .nivel-selector h3 {
            color: #6a53b8;
            margin-bottom: 1rem;
        }

        .nivel-btns {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .nivel-btn {
            padding: 0.8rem 2rem;
            border: 2px solid #6a53b8;
            background: white;
            color: #6a53b8;
            border-radius: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .nivel-btn.active {
            background: linear-gradient(135deg, #6a53b8 0%, #8b73d8 100%);
            color: white;
        }

        .nivel-btn:hover {
            transform: translateY(-2px);
        }

        .game-board {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2rem;
            backdrop-filter: blur(10px);
        }

        .card {
            aspect-ratio: 3/4;
            position: relative;
            cursor: pointer;
            transform-style: preserve-3d;
            transition: transform 0.6s;
        }

        .card.flipped {
            transform: rotateY(180deg);
        }

        .card.matched {
            animation: matchPulse 0.6s;
            pointer-events: none;
        }

        @keyframes matchPulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        .card-face {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .card-front {
            background: linear-gradient(135deg, #6a53b8 0%, #8b73d8 100%);
            color: white;
            font-size: 3rem;
        }

        .card-back {
            background: white;
            transform: rotateY(180deg);
            overflow: hidden;
            padding: 0.5rem;
        }

        .card-back img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 0.5rem;
        }

        .card-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9), transparent);
            color: white;
            padding: 0.5rem;
            font-size: 0.75rem;
            text-align: center;
            font-weight: bold;
        }

        .nivel-medio .card-info,
        .nivel-dificil .card-info {
            display: none;
        }

        .nivel-medio .card-back::after,
        .nivel-dificil .card-back::after {
            content: attr(data-hint);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            padding: 1rem;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            text-align: center;
            max-width: 90%;
        }

        .modal-resultado {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-resultado.show {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 3rem;
            border-radius: 2rem;
            text-align: center;
            max-width: 500px;
            animation: slideDown 0.5s;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-content h2 {
            color: #6a53b8;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .resultado-stats {
            margin: 2rem 0;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .resultado-item {
            background: #f8f7fc;
            padding: 1rem;
            border-radius: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .resultado-item span:first-child {
            color: #666;
        }

        .resultado-item span:last-child {
            color: #6a53b8;
            font-weight: bold;
            font-size: 1.2rem;
        }

        @media (max-width: 768px) {
            .game-board {
                grid-template-columns: repeat(3, 1fr);
                gap: 0.5rem;
                padding: 1rem;
            }

            .header-jogo {
                flex-direction: column;
                text-align: center;
            }

            .header-jogo h1 {
                font-size: 1.5rem;
            }

            .stats-container {
                justify-content: center;
            }

            .btn-menu {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .game-board {
                grid-template-columns: repeat(2, 1fr);
            }

            .card-front {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="container-jogo">
        <!-- Header -->
        <div class="header-jogo">
            <h1>🎮 Jogo da Memória</h1>

            <div class="stats-container">
                <div class="stat-box">
                    <span>Tentativas</span>
                    <span id="tentativas">0</span>
                </div>
                <div class="stat-box">
                    <span>Pares</span>
                    <span id="pares">0 / <?php echo $numeroPares; ?></span>
                </div>
                <div class="stat-box">
                    <span>Tempo</span>
                    <span id="tempo">0:00</span>
                </div>
                <div class="stat-box">
                    <span>Pontuação</span>
                    <span id="pontuacao"><?php echo $config['pontuacao_base']; ?></span>
                </div>
            </div>

            <div class="btn-menu">
                <a href="explorar.php" class="btn btn-voltar">← Voltar</a>
                <button class="btn btn-reiniciar" onclick="reiniciarJogo()">🔄 Reiniciar</button>
            </div>
        </div>

        <!-- Seletor de Nível -->
        <div class="nivel-selector">
            <h3>Nível de Dificuldade</h3>
            <div class="nivel-btns">
                <button class="nivel-btn <?php echo $nivel === 'facil' ? 'active' : ''; ?>"
                    onclick="mudarNivel('facil')">
                    Fácil (6 pares)
                </button>
                <button class="nivel-btn <?php echo $nivel === 'medio' ? 'active' : ''; ?>"
                    onclick="mudarNivel('medio')">
                    Médio (8 pares)
                </button>
                <button class="nivel-btn <?php echo $nivel === 'dificil' ? 'active' : ''; ?>"
                    onclick="mudarNivel('dificil')">
                    Difícil (10 pares)
                </button>
            </div>
        </div>

        <!-- Tabuleiro do Jogo -->
        <div class="game-board nivel-<?php echo $nivel; ?>" id="gameBoard">
            <?php foreach ($cartas as $index => $carta): ?>
                <div class="card"
                    data-id="<?php echo $carta['id']; ?>"
                    data-unique="<?php echo $carta['uniqueId']; ?>"
                    onclick="virarCarta(this)">
                    <div class="card-face card-front">
                        🎬
                    </div>
                    <div class="card-face card-back"
                        data-hint="<?php
                                    if ($nivel === 'medio') {
                                        echo htmlspecialchars($carta['genero'] . ' | ' . $carta['classificacao']);
                                    } elseif ($nivel === 'dificil') {
                                    }
                                    ?>">
                        <img src="<?php echo htmlspecialchars($carta['imagem']); ?>"
                            alt="<?php echo htmlspecialchars($carta['titulo']); ?>">
                        <div class="card-info">
                            <?php echo htmlspecialchars($carta['titulo']); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal de Resultado -->
    <div class="modal-resultado" id="modalResultado">
        <div class="modal-content">
            <h2>🎉 Parabéns!</h2>
            <p style="color: #666; font-size: 1.1rem; margin: 1rem 0;">Você completou o jogo!</p>

            <div class="resultado-stats">
                <div class="resultado-item">
                    <span>Tentativas:</span>
                    <span id="resultTentativas">0</span>
                </div>
                <div class="resultado-item">
                    <span>Tempo:</span>
                    <span id="resultTempo">0:00</span>
                </div>
                <div class="resultado-item">
                    <span>Pontuação Final:</span>
                    <span id="resultPontuacao">0</span>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button class="btn btn-reiniciar" style="flex: 1;" onclick="reiniciarJogo()">
                    🔄 Jogar Novamente
                </button>
                <a href="explorar.php" class="btn btn-voltar" style="flex: 1;">
                    ← Menu Principal
                </a>
            </div>
        </div>
    </div>

    <script>
        const config = <?php echo json_encode($config); ?>;
        let primeiraCarta = null;
        let segundaCarta = null;
        let bloqueado = false;
        let paresEncontrados = 0;
        let tentativas = 0;
        let pontuacao = config.pontuacao_base;
        let tempoInicio = Date.now();
        let timerInterval;

        // Iniciar timer
        function iniciarTimer() {
            timerInterval = setInterval(() => {
                const tempoDecorrido = Math.floor((Date.now() - tempoInicio) / 1000);
                const minutos = Math.floor(tempoDecorrido / 60);
                const segundos = tempoDecorrido % 60;
                document.getElementById('tempo').textContent =
                    `${minutos}:${segundos.toString().padStart(2, '0')}`;
            }, 1000);
        }

        // Virar carta
        function virarCarta(carta) {
            if (bloqueado) return;
            if (carta === primeiraCarta) return;
            if (carta.classList.contains('matched')) return;

            carta.classList.add('flipped');

            if (!primeiraCarta) {
                primeiraCarta = carta;
                return;
            }

            segundaCarta = carta;
            bloqueado = true;
            tentativas++;
            document.getElementById('tentativas').textContent = tentativas;

            verificarPar();
        }

        // Verificar se as cartas formam um par
        function verificarPar() {
            const id1 = primeiraCarta.getAttribute('data-id');
            const id2 = segundaCarta.getAttribute('data-id');

            if (id1 === id2) {
                // Par encontrado!
                primeiraCarta.classList.add('matched');
                segundaCarta.classList.add('matched');
                paresEncontrados++;
                document.getElementById('pares').textContent =
                    `${paresEncontrados} / ${config.pares}`;

                resetarCartas();

                if (paresEncontrados === config.pares) {
                    finalizarJogo();
                }
            } else {
                // Par errado
                pontuacao = Math.max(0, pontuacao - config.penalidade_erro);
                document.getElementById('pontuacao').textContent = pontuacao;

                setTimeout(() => {
                    primeiraCarta.classList.remove('flipped');
                    segundaCarta.classList.remove('flipped');
                    resetarCartas();
                }, 1000);
            }
        }

        // Resetar seleção de cartas
        function resetarCartas() {
            [primeiraCarta, segundaCarta] = [null, null];
            bloqueado = false;
        }

        // Finalizar jogo
        function finalizarJogo() {
            clearInterval(timerInterval);

            const tempoDecorrido = Math.floor((Date.now() - tempoInicio) / 1000);
            const tempoRestante = Math.max(0, config.tempo_limite - tempoDecorrido);
            pontuacao += tempoRestante * config.bonus_tempo;

            document.getElementById('resultTentativas').textContent = tentativas;
            document.getElementById('resultTempo').textContent =
                document.getElementById('tempo').textContent;
            document.getElementById('resultPontuacao').textContent = pontuacao;

            setTimeout(() => {
                document.getElementById('modalResultado').classList.add('show');
            }, 500);
        }

        // Reiniciar jogo
        function reiniciarJogo() {
            window.location.href = window.location.href;
        }

        // Mudar nível
        function mudarNivel(nivel) {
            window.location.href = `?nivel=${nivel}`;
        }

        // Iniciar timer ao carregar
        window.onload = () => {
            iniciarTimer();
        };

        function salvarPontuacao(jogo, pontuacao, tempo, movimentos, nivel) {
            if (!<?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>) {
                return; // Não salva se não estiver logado
            }

            fetch('salvar_pontuacao.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `jogo=${jogo}&pontuacao=${pontuacao}&tempo=${tempo}&movimentos=${movimentos}&nivel=${nivel}`
            });
        }
    </script>
</body>

</html>