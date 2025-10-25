<?php
// ===============================================
// CONFIGURAÇÃO DO BANCO DE DADOS
// ===============================================
$host = '127.0.0.1';
$db = 'frameflow';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

$imagem_selecionada_url = '';
$titulo_selecionado = 'Série';
$erro = '';

// ===============================================
// CONEXÃO E BUSCA
// ===============================================
try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $sql = "SELECT titulo, imagem_url FROM series ORDER BY RAND() LIMIT 1";
    $stmt = $pdo->query($sql);
    $serie_sorteada = $stmt->fetch();

    if (empty($serie_sorteada)) {
        $erro = "Nenhuma série encontrada no banco de dados.";
    } else {
        $imagem_selecionada_url = $serie_sorteada['imagem_url'];
        $titulo_selecionado = $serie_sorteada['titulo'];
    }
} catch (\PDOException $e) {
    $erro = "Erro de conexão ou de banco de dados: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quebra-Cabeça - <?= htmlspecialchars($titulo_selecionado) ?></title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #6A53B8;
            --primary-dark: #5842a0;
            --primary-light: #8b7cc8;
            --secondary: #FF6B6B;
            --success: #51cf66;
            --background: #0f0f1e;
            --surface: #1a1a2e;
            --surface-light: #242438;
            --text: #ffffff;
            --text-secondary: #b0b0c8;
            --border: #2d2d44;

            --puzzle-size: 400px;
            --grid-size: 3;
            --peca-size: calc(var(--puzzle-size) / var(--grid-size));
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--background) 0%, #16162e 100%);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
        }

        .header {
            text-align: center;
            margin-top: 20px;
        }

        .logo {
            font-size: 2.5em;
            font-weight: bold;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        h1 {
            font-size: 1.8em;
            color: var(--text);
            margin-bottom: 8px;
        }

        .serie-titulo {
            font-size: 1.3em;
            color: var(--primary-light);
            font-weight: 600;
        }

        .game-area {
            display: flex;
            gap: 40px;
            align-items: flex-start;
            flex-wrap: wrap;
            justify-content: center;
        }

        .preview-section {
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: center;
        }

        .preview-container {
            background: var(--surface);
            padding: 20px;
            border-radius: 16px;
            border: 2px solid var(--border);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        }

        #preview {
            max-width: 200px;
            width: 200px;
            height: auto;
            border-radius: 12px;
            border: 2px solid var(--primary);
            display: none;
            transition: all 0.3s ease;
        }

        #preview.show {
            display: block;
        }

        .tabuleiro-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
            align-items: center;
        }

        .tabuleiro-container {
            background: var(--surface);
            padding: 25px;
            border-radius: 20px;
            border: 3px solid var(--primary);
            box-shadow: 0 12px 48px rgba(106, 83, 184, 0.3);
            position: relative;
        }

        #tabuleiro {
            display: grid;
            grid-template-columns: repeat(var(--grid-size), var(--peca-size));
            grid-template-rows: repeat(var(--grid-size), var(--peca-size));
            gap: 0;
            width: var(--puzzle-size);
            height: var(--puzzle-size);
            background: var(--surface-light);
            border-radius: 12px;
            overflow: hidden;
        }

        .peca {
            width: var(--peca-size);
            height: var(--peca-size);
            background-size: var(--puzzle-size) var(--puzzle-size);
            background-repeat: no-repeat;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid rgba(106, 83, 184, 0.2);
            position: relative;
        }

        .peca:hover:not(.peca-vazia) {
            transform: scale(1.03);
            border: 2px solid var(--primary);
            z-index: 10;
            box-shadow: 0 4px 16px rgba(106, 83, 184, 0.5);
        }

        .peca-vazia {
            background: var(--surface-light) !important;
            cursor: default;
            opacity: 0.3;
            border: 1px dashed var(--border);
        }

        .controles {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }

        button {
            padding: 14px 28px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            border: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        #iniciar-jogo {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }

        #iniciar-jogo:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(106, 83, 184, 0.5);
        }

        #iniciar-jogo:active {
            transform: translateY(0);
        }

        #mostrar-original {
            background: var(--surface);
            color: var(--text);
            border: 2px solid var(--primary);
        }

        #mostrar-original:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        .mensagem {
            background: var(--surface);
            padding: 16px 32px;
            border-radius: 12px;
            font-size: 1.1em;
            font-weight: 500;
            text-align: center;
            border: 2px solid var(--border);
            min-height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
        }

        .mensagem.vitoria {
            background: linear-gradient(135deg, var(--success) 0%, #40c057 100%);
            color: white;
            border: 2px solid var(--success);
            animation: pulse 1s ease-in-out infinite;
            box-shadow: 0 8px 32px rgba(81, 207, 102, 0.4);
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.03);
            }
        }

        .erro-msg {
            background: var(--surface);
            padding: 24px;
            border-radius: 12px;
            color: var(--secondary);
            border: 2px solid var(--secondary);
            text-align: center;
            max-width: 600px;
            margin: 40px auto;
        }

        .stats {
            display: flex;
            gap: 30px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .stat-item {
            background: var(--surface);
            padding: 16px 24px;
            border-radius: 12px;
            border: 2px solid var(--border);
            text-align: center;
            min-width: 120px;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.9em;
            margin-bottom: 4px;
        }

        .stat-value {
            color: var(--primary-light);
            font-size: 1.8em;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            :root {
                --puzzle-size: 300px;
            }

            .game-area {
                gap: 20px;
            }

            .logo {
                font-size: 2em;
            }

            h1 {
                font-size: 1.4em;
            }
        }
    </style>
</head>

<body>

    <?php if ($erro) : ?>
        <div class="erro-msg">
            <h2>⚠️ Erro</h2>
            <p><?= htmlspecialchars($erro) ?></p>
        </div>
    <?php else : ?>

        <div class="container">
            <div class="header">
                <div class="logo">🎬 FRAMEFLOW</div>
                <h1>Quebra-Cabeça da Série</h1>
                <div class="serie-titulo">"<?= htmlspecialchars($titulo_selecionado) ?>"</div>
            </div>

            <div class="game-area">
                <div class="preview-section">
                    <div class="preview-container">
                        <img id="preview" src="<?= htmlspecialchars($imagem_selecionada_url) ?>" alt="Imagem original">
                    </div>
                    <button id="mostrar-original">👁️ Ver Original</button>
                </div>

                <div class="tabuleiro-section">
                    <div class="tabuleiro-container">
                        <div id="tabuleiro"></div>
                    </div>

                    <div class="stats">
                        <div class="stat-item">
                            <div class="stat-label">Movimentos</div>
                            <div class="stat-value" id="movimentos">0</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Tempo</div>
                            <div class="stat-value" id="tempo">0:00</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="controles">
                <button id="iniciar-jogo">🎮 Iniciar Jogo</button>
            </div>

            <div id="mensagem" class="mensagem">Clique em "Iniciar Jogo" para começar!</div>
        </div>

    <?php endif; ?>

    <script>
        // ==========================================================
        // CONSTANTES E VARIÁVEIS GLOBAIS
        // ==========================================================
        const IMAGEM_URL = "<?= addslashes($imagem_selecionada_url) ?>";
        const TAMANHO = 3;
        const NUM_PECAS = TAMANHO * TAMANHO;

        // Lê o tamanho real do CSS
        const computedStyle = getComputedStyle(document.documentElement);
        const PUZZLE_SIZE_PX = parseFloat(computedStyle.getPropertyValue('--puzzle-size'));
        const PECA_SIZE_PX = PUZZLE_SIZE_PX / TAMANHO;

        let tabuleiro = [];
        let jogando = false;
        let movimentos = 0;
        let segundos = 0;
        let intervaloTempo = null;

        const tabuleiroEl = document.getElementById('tabuleiro');
        const iniciarBtn = document.getElementById('iniciar-jogo');
        const mensagemEl = document.getElementById('mensagem');
        const previewEl = document.getElementById('preview');
        const mostrarOriginalBtn = document.getElementById('mostrar-original');
        const movimentosEl = document.getElementById('movimentos');
        const tempoEl = document.getElementById('tempo');

        // ==========================================================
        // FUNÇÕES DO JOGO
        // ==========================================================

        function criarPecas() {
            tabuleiroEl.innerHTML = '';
            tabuleiro = [];

            for (let i = 0; i < NUM_PECAS; i++) {
                tabuleiro.push(i);

                const peca = document.createElement('div');
                peca.className = 'peca';
                peca.dataset.id = i;

                // Calcula posição ORIGINAL desta peça na grade
                const row = Math.floor(i / TAMANHO);
                const col = i % TAMANHO;

                // Calcula a posição em pixels usando o tamanho exato da peça
                const bgPosX = -(col * PECA_SIZE_PX);
                const bgPosY = -(row * PECA_SIZE_PX);

                peca.style.backgroundImage = `url('${IMAGEM_URL}')`;
                peca.style.backgroundPosition = `${bgPosX}px ${bgPosY}px`;

                // A última peça é o espaço vazio
                if (i === NUM_PECAS - 1) {
                    peca.classList.add('peca-vazia');
                    peca.style.backgroundImage = 'none';
                }

                peca.addEventListener('click', () => moverPeca(i));
                tabuleiroEl.appendChild(peca);
            }
        }

        function renderizar() {
            const pecas = Array.from(tabuleiroEl.children);
            tabuleiroEl.innerHTML = '';

            tabuleiro.forEach(idPeca => {
                const peca = pecas.find(p => parseInt(p.dataset.id) === idPeca);
                if (peca) {
                    tabuleiroEl.appendChild(peca);
                }
            });
        }

        function moverPeca(idPeca) {
            if (!jogando) return;

            const indiceAtual = tabuleiro.indexOf(idPeca);
            const indiceVazio = tabuleiro.indexOf(NUM_PECAS - 1);

            const linhaAtual = Math.floor(indiceAtual / TAMANHO);
            const colunaAtual = indiceAtual % TAMANHO;
            const linhaVazio = Math.floor(indiceVazio / TAMANHO);
            const colunaVazio = indiceVazio % TAMANHO;

            const adjacente = (Math.abs(linhaAtual - linhaVazio) === 1 && colunaAtual === colunaVazio) ||
                (Math.abs(colunaAtual - colunaVazio) === 1 && linhaAtual === linhaVazio);

            if (adjacente) {
                [tabuleiro[indiceAtual], tabuleiro[indiceVazio]] = [tabuleiro[indiceVazio], tabuleiro[indiceAtual]];
                renderizar();
                movimentos++;
                movimentosEl.textContent = movimentos;
                verificarVitoria();
            }
        }

        function embaralhar() {
            do {
                for (let i = tabuleiro.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [tabuleiro[i], tabuleiro[j]] = [tabuleiro[j], tabuleiro[i]];
                }
            } while (!eSolucionavel() || estaResolvido());
        }

        function eSolucionavel() {
            let inversoes = 0;
            const semVazio = tabuleiro.filter(v => v !== NUM_PECAS - 1);

            for (let i = 0; i < semVazio.length; i++) {
                for (let j = i + 1; j < semVazio.length; j++) {
                    if (semVazio[i] > semVazio[j]) inversoes++;
                }
            }

            return inversoes % 2 === 0;
        }

        function estaResolvido() {
            return tabuleiro.every((valor, index) => valor === index);
        }

        function verificarVitoria() {
            if (estaResolvido()) {
                jogando = false;
                clearInterval(intervaloTempo);

                mensagemEl.textContent = `🎉 Parabéns! Você completou em ${movimentos} movimentos!`;
                mensagemEl.classList.add('vitoria');
                iniciarBtn.textContent = '🔄 Jogar Novamente';

                // Mostra a última peça
                const pecas = Array.from(tabuleiroEl.children);
                const pecaVazia = pecas.find(p => parseInt(p.dataset.id) === NUM_PECAS - 1);

                if (pecaVazia) {
                    const idPeca = NUM_PECAS - 1;
                    const row = Math.floor(idPeca / TAMANHO);
                    const col = idPeca % TAMANHO;

                    const bgPosX = -(col * PECA_SIZE_PX);
                    const bgPosY = -(row * PECA_SIZE_PX);

                    pecaVazia.classList.remove('peca-vazia');
                    pecaVazia.style.backgroundImage = `url('${IMAGEM_URL}')`;
                    pecaVazia.style.backgroundPosition = `${bgPosX}px ${bgPosY}px`;
                    pecaVazia.style.opacity = '1';
                    pecaVazia.style.border = '1px solid rgba(106, 83, 184, 0.2)';
                }
            }
        }

        function iniciarJogo() {
            criarPecas();
            embaralhar();
            renderizar();

            jogando = true;
            movimentos = 0;
            segundos = 0;

            movimentosEl.textContent = '0';
            tempoEl.textContent = '0:00';

            mensagemEl.textContent = 'Clique nas peças adjacentes ao espaço vazio!';
            mensagemEl.classList.remove('vitoria');
            iniciarBtn.textContent = '🔄 Embaralhar Novamente';
            previewEl.classList.remove('show');
            mostrarOriginalBtn.textContent = '👁️ Ver Original';

            clearInterval(intervaloTempo);
            intervaloTempo = setInterval(() => {
                segundos++;
                const min = Math.floor(segundos / 60);
                const seg = segundos % 60;
                tempoEl.textContent = `${min}:${seg.toString().padStart(2, '0')}`;
            }, 1000);
        }

        // ==========================================================
        // EVENT LISTENERS E INICIALIZAÇÃO
        // ==========================================================

        iniciarBtn.addEventListener('click', iniciarJogo);

        mostrarOriginalBtn.addEventListener('click', () => {
            previewEl.classList.toggle('show');
            mostrarOriginalBtn.textContent = previewEl.classList.contains('show') ? '🙈 Esconder' : '👁️ Ver Original';
        });

        window.onload = function() {
            if (!document.querySelector('.erro-msg')) {
                criarPecas();
                renderizar();
            }
        };
    </script>
</body>

</html>