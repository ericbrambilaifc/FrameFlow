<?php
session_start();
require_once 'src/ConexaoBD.php';

$imagem_selecionada_url = '';
$titulo_selecionado = 'Série';
$erro = '';

try {
    $conexao = ConexaoBD::conectar();

    $sql = "SELECT titulo, imagem_url FROM series ORDER BY RAND() LIMIT 1";
    $stmt = $conexao->query($sql);
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
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quebra-Cabeça - <?= htmlspecialchars($titulo_selecionado) ?></title>
    <link rel="stylesheet" href="global.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .container-puzzle {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header-puzzle {
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

        .header-puzzle h1 {
            color: #6a53b8;
            font-size: 2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .serie-titulo {
            color: #8b73d8;
            font-size: 1.2rem;
            font-weight: 600;
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

        .game-area {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 2rem;
            align-items: start;
        }

        .preview-section {
            background: white;
            padding: 1.5rem;
            border-radius: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .preview-section h3 {
            color: #6a53b8;
            margin-bottom: 1rem;
            text-align: center;
        }

        #preview {
            width: 100%;
            border-radius: 1rem;
            border: 3px solid #6a53b8;
            display: none;
            transition: all 0.3s ease;
        }

        #preview.show {
            display: block;
        }

        .btn-preview {
            width: 100%;
            margin-top: 1rem;
            background: linear-gradient(135deg, #6a53b8 0%, #8b73d8 100%);
            color: white;
            padding: 0.8rem;
            border: none;
            border-radius: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-preview:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(106, 83, 184, 0.4);
        }

        .puzzle-section {
            background: white;
            padding: 2rem;
            border-radius: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .puzzle-container {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        #tabuleiro {
            display: grid;
            grid-template-columns: repeat(3, 150px);
            grid-template-rows: repeat(3, 150px);
            gap: 0;
            border: 3px solid #6a53b8;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(106, 83, 184, 0.3);
        }

        .peca {
            width: 150px;
            height: 150px;
            background-size: 450px 450px;
            background-repeat: no-repeat;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid rgba(106, 83, 184, 0.2);
            position: relative;
        }

        .peca:hover:not(.peca-vazia) {
            transform: scale(1.05);
            border: 2px solid #6a53b8;
            z-index: 10;
            box-shadow: 0 4px 16px rgba(106, 83, 184, 0.5);
        }

        .peca-vazia {
            background: #f0f0f0 !important;
            cursor: default;
            opacity: 0.5;
            border: 2px dashed #6a53b8;
        }

        .controles {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
        }

        .btn-iniciar {
            background: linear-gradient(135deg, #6a53b8 0%, #8b73d8 100%);
            color: white;
            padding: 1rem 3rem;
            border: none;
            border-radius: 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(106, 83, 184, 0.3);
        }

        .btn-iniciar:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(106, 83, 184, 0.5);
        }

        .btn-embaralhar {
            background: #f0f0f0;
            color: #6a53b8;
            padding: 1rem 2rem;
            border: 2px solid #6a53b8;
            border-radius: 2rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-embaralhar:hover {
            background: #6a53b8;
            color: white;
            transform: translateY(-2px);
        }

        .mensagem {
            background: #f8f7fc;
            padding: 1.5rem;
            border-radius: 1rem;
            text-align: center;
            color: #666;
            font-size: 1.1rem;
            margin-top: 1.5rem;
            border: 2px solid #e0e0e0;
        }

        .mensagem.vitoria {
            background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%);
            color: white;
            border: none;
            animation: pulse 1s ease-in-out infinite;
            box-shadow: 0 4px 20px rgba(76, 175, 80, 0.4);
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }
        }

        .erro-msg {
            background: white;
            padding: 2rem;
            border-radius: 1.5rem;
            text-align: center;
            max-width: 600px;
            margin: 2rem auto;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .erro-msg h2 {
            color: #f44336;
            margin-bottom: 1rem;
        }

        .erro-msg p {
            color: #666;
        }

        @media (max-width: 1024px) {
            .game-area {
                grid-template-columns: 1fr;
            }

            .preview-section {
                max-width: 300px;
                margin: 0 auto;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .header-puzzle {
                flex-direction: column;
                text-align: center;
            }

            .header-puzzle h1 {
                font-size: 1.5rem;
            }

            .stats-container {
                justify-content: center;
            }

            #tabuleiro {
                grid-template-columns: repeat(3, 100px);
                grid-template-rows: repeat(3, 100px);
            }

            .peca {
                width: 100px;
                height: 100px;
                background-size: 300px 300px;
            }
        }

        @media (max-width: 480px) {
            #tabuleiro {
                grid-template-columns: repeat(3, 90px);
                grid-template-rows: repeat(3, 90px);
            }

            .peca {
                width: 90px;
                height: 90px;
                background-size: 270px 270px;
            }

            .btn-iniciar {
                padding: 0.8rem 2rem;
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <?php if ($erro): ?>
        <div class="erro-msg">
            <h2>⚠️ Erro</h2>
            <p><?= htmlspecialchars($erro) ?></p>
        </div>
    <?php else: ?>
        <div class="container-puzzle">
            <!-- Header -->
            <div class="header-puzzle">
                <div>
                    <h1>🧩 Quebra-Cabeça</h1>
                    <div class="serie-titulo"><?= htmlspecialchars($titulo_selecionado) ?></div>
                </div>

                <div class="stats-container">
                    <div class="stat-box">
                        <span>Movimentos</span>
                        <span id="movimentos">0</span>
                    </div>
                    <div class="stat-box">
                        <span>Tempo</span>
                        <span id="tempo">0:00</span>
                    </div>
                </div>

                <div class="btn-menu">
                    <a href="explorar.php" class="btn btn-voltar">← Voltar</a>
                </div>
            </div>

            <!-- Game Area -->
            <div class="game-area">
                <!-- Preview Section -->
                <div class="preview-section">
                    <h3>📷 Imagem Original</h3>
                    <img id="preview" src="<?= htmlspecialchars($imagem_selecionada_url) ?>" alt="Imagem original">
                    <button class="btn-preview" id="mostrar-original">👁️ Mostrar</button>
                </div>

                <!-- Puzzle Section -->
                <div class="puzzle-section">
                    <div class="puzzle-container">
                        <div id="tabuleiro"></div>
                    </div>

                    <div class="controles">
                        <button class="btn-iniciar" id="iniciar-jogo">🎮 Iniciar Jogo</button>
                        <button class="btn-embaralhar" id="embaralhar" style="display: none;">🔄 Embaralhar</button>
                    </div>

                    <div id="mensagem" class="mensagem">
                        Clique em "Iniciar Jogo" para começar!
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script>
        const IMAGEM_URL = "<?= addslashes($imagem_selecionada_url) ?>";
        const TAMANHO = 3;
        const NUM_PECAS = TAMANHO * TAMANHO;

        let tabuleiro = [];
        let jogando = false;
        let movimentos = 0;
        let segundos = 0;
        let intervaloTempo = null;

        const tabuleiroEl = document.getElementById('tabuleiro');
        const iniciarBtn = document.getElementById('iniciar-jogo');
        const embaralharBtn = document.getElementById('embaralhar');
        const mensagemEl = document.getElementById('mensagem');
        const previewEl = document.getElementById('preview');
        const mostrarOriginalBtn = document.getElementById('mostrar-original');
        const movimentosEl = document.getElementById('movimentos');
        const tempoEl = document.getElementById('tempo');

        function getPecaSize() {
            const firstPeca = document.querySelector('.peca');
            return firstPeca ? firstPeca.offsetWidth : 150;
        }

        function criarPecas() {
            tabuleiroEl.innerHTML = '';
            tabuleiro = [];

            for (let i = 0; i < NUM_PECAS; i++) {
                tabuleiro.push(i);

                const peca = document.createElement('div');
                peca.className = 'peca';
                peca.dataset.id = i;

                const row = Math.floor(i / TAMANHO);
                const col = i % TAMANHO;

                const pecaSize = getPecaSize();
                const bgPosX = -(col * pecaSize);
                const bgPosY = -(row * pecaSize);

                peca.style.backgroundImage = `url('${IMAGEM_URL}')`;
                peca.style.backgroundPosition = `${bgPosX}px ${bgPosY}px`;

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

                mensagemEl.textContent = `🎉 Parabéns! Você completou em ${movimentos} movimentos e ${tempoEl.textContent}!`;
                mensagemEl.classList.add('vitoria');
                iniciarBtn.textContent = '🔄 Jogar Novamente';
                embaralharBtn.style.display = 'none';

                const pecas = Array.from(tabuleiroEl.children);
                const pecaVazia = pecas.find(p => parseInt(p.dataset.id) === NUM_PECAS - 1);

                if (pecaVazia) {
                    const idPeca = NUM_PECAS - 1;
                    const row = Math.floor(idPeca / TAMANHO);
                    const col = idPeca % TAMANHO;

                    const pecaSize = getPecaSize();
                    const bgPosX = -(col * pecaSize);
                    const bgPosY = -(row * pecaSize);

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

            mensagemEl.textContent = 'Clique nas peças adjacentes ao espaço vazio para movê-las!';
            mensagemEl.classList.remove('vitoria');
            iniciarBtn.textContent = '🎮 Jogando...';
            embaralharBtn.style.display = 'inline-flex';
            previewEl.classList.remove('show');
            mostrarOriginalBtn.textContent = '👁️ Mostrar';

            clearInterval(intervaloTempo);
            intervaloTempo = setInterval(() => {
                segundos++;
                const min = Math.floor(segundos / 60);
                const seg = segundos % 60;
                tempoEl.textContent = `${min}:${seg.toString().padStart(2, '0')}`;
            }, 1000);
        }

        iniciarBtn.addEventListener('click', iniciarJogo);

        embaralharBtn.addEventListener('click', () => {
            if (jogando) {
                embaralhar();
                renderizar();
                movimentos = 0;
                movimentosEl.textContent = '0';
            }
        });

        mostrarOriginalBtn.addEventListener('click', () => {
            previewEl.classList.toggle('show');
            mostrarOriginalBtn.textContent = previewEl.classList.contains('show') ? '🙈 Esconder' : '👁️ Mostrar';
        });

        window.onload = function() {
            if (!document.querySelector('.erro-msg')) {
                criarPecas();
                renderizar();
            }
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