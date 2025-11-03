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
            background: white;
            min-height: 100vh;
            padding: 2rem;
        }

        .container-puzzle {
            max-width: 90%;
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
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #6A53B8;
            text-decoration: none;
            font-size: 16px;
            margin-bottom: 30px;
            transition: opacity 0.2s;
        }

        .btn-voltar:hover {
            opacity: 0.7;
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
            background: #6a53b8;
            color: white;
            padding: 1rem 4rem;
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
                    <h1>Quebra-Cabeça</h1>
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
                    <a href="explorar.php" class="btn-voltar"><svg width="26" height="26" viewBox="0 0 26 26"
                            fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M0.649902 12.6499C0.649902 19.2773 6.02248 24.6499 12.6499 24.6499C19.2773 24.6499 24.6499 19.2773 24.6499 12.6499C24.6499 6.02249 19.2773 0.649902 12.6499 0.649902C6.02248 0.649903 0.649902 6.02249 0.649902 12.6499Z"
                                stroke="#6A53B8" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M17.45 12.6499L7.84995 12.6499" stroke="#6A53B8" stroke-width="1.3"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M12.6499 7.8501L7.8499 12.6501L12.6499 17.4501" stroke="#6A53B8" stroke-width="1.3"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Voltar para explorar</a>
                </div>
            </div>

            <!-- Game Area -->
            <div class="game-area">
                <!-- Preview Section -->
                <div class="preview-section">
                    <h3>Imagem Original</h3>
                    <img id="preview" src="<?= htmlspecialchars($imagem_selecionada_url) ?>" alt="Imagem original">
                    <button class="btn-preview" id="mostrar-original"><svg width="22" height="16" viewBox="0 0 22 16"
                            fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M1.06251 8.34738C0.979165 8.12287 0.979165 7.8759 1.06251 7.65138C1.87421 5.68324 3.25202 4.00042 5.02128 2.81628C6.79053 1.63214 8.87155 1 11.0005 1C13.1295 1 15.2105 1.63214 16.9797 2.81628C18.749 4.00042 20.1268 5.68324 20.9385 7.65138C21.0218 7.8759 21.0218 8.12287 20.9385 8.34738C20.1268 10.3155 18.749 11.9983 16.9797 13.1825C15.2105 14.3666 13.1295 14.9988 11.0005 14.9988C8.87155 14.9988 6.79053 14.3666 5.02128 13.1825C3.25202 11.9983 1.87421 10.3155 1.06251 8.34738Z"
                                stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M11.0005 10.9994C12.6573 10.9994 14.0005 9.65624 14.0005 7.99939C14.0005 6.34254 12.6573 4.99939 11.0005 4.99939C9.34363 4.99939 8.00049 6.34254 8.00049 7.99939C8.00049 9.65624 9.34363 10.9994 11.0005 10.9994Z"
                                stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Mostrar</button>
                </div>

                <!-- Puzzle Section -->
                <div class="puzzle-section">
                    <div class="puzzle-container">
                        <div id="tabuleiro"></div>
                    </div>

                    <div class="controles">
                        <button class="btn-iniciar" id="iniciar-jogo">Iniciar Jogo</button>
                        <button class="btn-embaralhar" id="embaralhar" style="display: none;">Embaralhar</button>
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

            // Garante que o botão inicial seja "Mostrar" (Olho Aberto)
            mostrarOriginalBtn.innerHTML = SVG_MOSTRAR + ' Mostrar';

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

        // --- DEFINIÇÃO DOS SVGs (CORRIGIDA) ---

        // SVG para Mostrar (Olho Aberto)
        const SVG_MOSTRAR = `
<svg width="22" height="16" viewBox="0 0 22 16"
fill="none" xmlns="http://www.w3.org/2000/svg">
<path
d="M1.06251 8.34738C0.979165 8.12287 0.979165 7.8759 1.06251 7.65138C1.87421 5.68324 3.25202 4.00042 5.02128 2.81628C6.79053 1.63214 8.87155 1 11.0005 1C13.1295 1 15.2105 1.63214 16.9797 2.81628C18.749 4.00042 20.1268 5.68324 20.9385 7.65138C21.0218 7.8759 21.0218 8.12287 20.9385 8.34738C20.1268 10.3155 18.749 11.9983 16.9797 13.1825C15.2105 14.3666 13.1295 14.9988 11.0005 14.9988C8.87155 14.9988 6.79053 14.3666 5.02128 13.1825C3.25202 11.9983 1.87421 10.3155 1.06251 8.34738Z"
stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
<path
d="M11.0005 10.9994C12.6573 10.9994 14.0005 9.65624 14.0005 7.99939C14.0005 6.34254 12.6573 4.99939 11.0005 4.99939C9.34363 4.99939 8.00049 6.34254 8.00049 7.99939C8.00049 9.65624 9.34363 10.9994 11.0005 10.9994Z"
stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
</svg>
`;

        // SVG para Esconder (Olho Fechado/Piscando - O que você pediu primeiro)
        const SVG_ESCONDER = `
<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 22px; height: 16px;">
<path d="M15 18L14.278 14.75" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M2 8C2.74835 10.0508 4.10913 11.8219 5.8979 13.0733C7.68667 14.3247 9.81695 14.9959 12 14.9959C14.1831 14.9959 16.3133 14.3247 18.1021 13.0733C19.8909 11.8219 21.2516 10.0508 22 8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M20 15L18.274 12.95" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M4 15L5.726 12.95" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M9 18L9.722 14.75" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
`;

        mostrarOriginalBtn.addEventListener('click', () => {
            previewEl.classList.toggle('show');
            // Lógica de troca de ícones
            if (previewEl.classList.contains('show')) {
                mostrarOriginalBtn.innerHTML = SVG_ESCONDER + ' Esconder';
            } else {
                mostrarOriginalBtn.innerHTML = SVG_MOSTRAR + ' Mostrar';
            }
        });

        window.onload = function () {
            if (!document.querySelector('.erro-msg')) {
                criarPecas();
                renderizar();
                mostrarOriginalBtn.innerHTML = SVG_MOSTRAR + ' Mostrar';
            }
        };

        function salvarPontuacao(jogo, pontuacao, tempo, movimentos, nivel) {
            if (!<?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>) {
                return;
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