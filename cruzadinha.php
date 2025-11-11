<?php
session_start();
require_once 'src/ConexaoBD.php';

// Buscar séries aleatórias para a cruzadinha
function buscarPalavrasCruzadinha($limite = 8)
{
    $conexao = ConexaoBD::conectar();

    // Busca títulos de séries do banco
    $sql = "SELECT id, titulo, 'serie' as tipo 
            FROM series 
            WHERE LENGTH(REPLACE(REPLACE(REPLACE(titulo, ' ', ''), ':', ''), '-', '')) BETWEEN 4 AND 12 
            ORDER BY RAND() 
            LIMIT :limite";

    $stmt = $conexao->prepare($sql);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Limpar título para usar na cruzadinha
function limparTitulo($titulo)
{
    $titulo = strtoupper($titulo);
    $titulo = preg_replace('/[^A-Z]/', '', $titulo);
    return $titulo;
}

// Verificar se duas palavras compartilham uma letra
function encontrarIntersecao($palavra1, $palavra2)
{
    for ($i = 0; $i < strlen($palavra1); $i++) {
        $letra = $palavra1[$i];
        $pos = strpos($palavra2, $letra);
        if ($pos !== false) {
            return [
                'letra' => $letra,
                'pos1' => $i,
                'pos2' => $pos
            ];
        }
    }
    return null;
}

// Gerar cruzadinha com palavras interligadas
function gerarCruzadinha($palavrasBanco)
{
    $palavras = [];
    $grid = [];
    $tamanhoGrid = 30; // Grid 30x30

    // Primeira palavra - horizontal no centro
    $primeiraPalavra = $palavrasBanco[0];
    $tituloLimpo = limparTitulo($primeiraPalavra['titulo']);

    $linhaInicial = floor($tamanhoGrid / 2);
    $colunaInicial = floor(($tamanhoGrid - strlen($tituloLimpo)) / 2);

    $palavras[] = [
        'id' => 1,
        'resposta' => $tituloLimpo,
        'pos_l' => $linhaInicial,
        'pos_c' => $colunaInicial,
        'orientacao' => 'H',
        'titulo_original' => $primeiraPalavra['titulo'],
        'tipo' => $primeiraPalavra['tipo']
    ];

    // Marcar letras no grid
    for ($i = 0; $i < strlen($tituloLimpo); $i++) {
        $grid[$linhaInicial][$colunaInicial + $i] = [
            'letra' => $tituloLimpo[$i],
            'palavra_id' => 1
        ];
    }

    // Adicionar palavras restantes tentando interligar
    $idPalavra = 2;
    for ($idx = 1; $idx < count($palavrasBanco); $idx++) {
        $novaPalavra = limparTitulo($palavrasBanco[$idx]['titulo']);
        $adicionada = false;

        // Tentar interligar com cada palavra já adicionada
        foreach ($palavras as $palavraExistente) {
            $intersecao = encontrarIntersecao($palavraExistente['resposta'], $novaPalavra);

            if ($intersecao) {
                // Calcular posição da nova palavra
                if ($palavraExistente['orientacao'] === 'H') {
                    // Palavra existente é horizontal, nova será vertical
                    $novaLinha = $palavraExistente['pos_l'] - $intersecao['pos2'];
                    $novaColuna = $palavraExistente['pos_c'] + $intersecao['pos1'];
                    $orientacao = 'V';
                } else {
                    // Palavra existente é vertical, nova será horizontal
                    $novaLinha = $palavraExistente['pos_l'] + $intersecao['pos1'];
                    $novaColuna = $palavraExistente['pos_c'] - $intersecao['pos2'];
                    $orientacao = 'H';
                }

                // Verificar se a posição é válida
                if (
                    $novaLinha >= 0 && $novaColuna >= 0 &&
                    $novaLinha < $tamanhoGrid && $novaColuna < $tamanhoGrid
                ) {

                    // Verificar se não há conflitos
                    $temConflito = false;
                    for ($i = 0; $i < strlen($novaPalavra); $i++) {
                        $checkL = $orientacao === 'H' ? $novaLinha : $novaLinha + $i;
                        $checkC = $orientacao === 'H' ? $novaColuna + $i : $novaColuna;

                        if (isset($grid[$checkL][$checkC])) {
                            if ($grid[$checkL][$checkC]['letra'] !== $novaPalavra[$i]) {
                                $temConflito = true;
                                break;
                            }
                        }
                    }

                    if (!$temConflito) {
                        $palavras[] = [
                            'id' => $idPalavra,
                            'resposta' => $novaPalavra,
                            'pos_l' => $novaLinha,
                            'pos_c' => $novaColuna,
                            'orientacao' => $orientacao,
                            'titulo_original' => $palavrasBanco[$idx]['titulo'],
                            'tipo' => $palavrasBanco[$idx]['tipo']
                        ];

                        // Marcar no grid
                        for ($i = 0; $i < strlen($novaPalavra); $i++) {
                            $markL = $orientacao === 'H' ? $novaLinha : $novaLinha + $i;
                            $markC = $orientacao === 'H' ? $novaColuna + $i : $novaColuna;
                            $grid[$markL][$markC] = [
                                'letra' => $novaPalavra[$i],
                                'palavra_id' => $idPalavra
                            ];
                        }

                        $adicionada = true;
                        $idPalavra++;
                        break;
                    }
                }
            }
        }
    }

    // Calcular tamanho real da matriz (área ocupada)
    $minL = $tamanhoGrid;
    $maxL = 0;
    $minC = $tamanhoGrid;
    $maxC = 0;

    foreach ($palavras as $palavra) {
        $minL = min($minL, $palavra['pos_l']);
        $minC = min($minC, $palavra['pos_c']);

        if ($palavra['orientacao'] === 'H') {
            $maxL = max($maxL, $palavra['pos_l']);
            $maxC = max($maxC, $palavra['pos_c'] + strlen($palavra['resposta']) - 1);
        } else {
            $maxL = max($maxL, $palavra['pos_l'] + strlen($palavra['resposta']) - 1);
            $maxC = max($maxC, $palavra['pos_c']);
        }
    }

    // Ajustar posições para começar de (1,1) e adicionar margem
    $margemL = 1 - $minL;
    $margemC = 1 - $minC;

    foreach ($palavras as &$palavra) {
        $palavra['pos_l'] += $margemL;
        $palavra['pos_c'] += $margemC;
    }

    $tamanhoMatriz = [$maxL - $minL + 3, $maxC - $minC + 3];

    return [
        'palavras' => $palavras,
        'tamanho_matriz' => $tamanhoMatriz
    ];
}

// Gerar dicas baseadas no título
function gerarDicas($titulo, $tipo, $resposta)
{
    $vogais = preg_replace('/[^AEIOU]/', '', $resposta);
    $vogaisUnicas = implode(', ', array_unique(str_split($vogais)));

    return [
        'facil' => "É uma $tipo com " . strlen($resposta) . " letras (sem espaços)",
        'medio' => "Começa com: " . substr($resposta, 0, 1) . " | Termina com: " . substr($resposta, -1),
        'dificil' => $vogaisUnicas ? "Vogais na palavra: " . $vogaisUnicas : "Título: " . substr($titulo, 0, 3) . "..."
    ];
}

// Buscar palavras do banco
$palavrasBanco = buscarPalavrasCruzadinha(8);

// Gerar cruzadinha interligada
$resultado = gerarCruzadinha($palavrasBanco);
$palavras = $resultado['palavras'];
$tamanhoMatriz = $resultado['tamanho_matriz'];

// Adicionar dicas
foreach ($palavras as &$palavra) {
    $palavra['dicas'] = gerarDicas($palavra['titulo_original'], $palavra['tipo'], $palavra['resposta']);
}

// Configurações do jogo
$gameData = [
    'tamanho_matriz' => $tamanhoMatriz,
    'palavras' => $palavras,
    'pontuacao_base' => 1000,
    'penalidade_dica' => 50,
    'penalidade_erro' => 20
];
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cruzadinha FrameFlow</title>
    <link rel="stylesheet" href="global.css">
    <style>
        body {
            background: white;
            min-height: 100vh;
            padding: 2rem;
        }

        .container-cruzadinha {
            max-width: 90%;
            margin: 0 auto;
        }

        .header-cruzadinha {
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

        .header-cruzadinha h1 {
            color: #6a53b8;
            font-size: 2rem;
            display: flex;
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
            transition: opacity 0.2s;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
        }

        .btn-voltar:hover {
            opacity: 0.7;
        }

        .btn-nova-cruzadinha {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #6A53B8;
            text-decoration: none;
            font-size: 16px;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: opacity 0.2s;
            padding: 0;
            background-color: #F0F0F0;
            padding: 0.8rem 1.5rem;
            border-radius: 1rem;
            font-weight: 600;
        }

        .btn-nova-cruzadinha:hover {
            opacity: 0.7;
        }


        .pontuacao-box {
            background: linear-gradient(135deg, #6a53b8 0%, #8b73d8 100%);
            color: white;
            padding: 1rem 2rem;
            border-radius: 2rem;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .game-area {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 2rem;
        }

        .crossword-container {
            background: white;
            padding: 2rem;
            border-radius: 1.5rem;
            overflow-x: auto;
        }

        .crossword-grid {
            display: grid;
            gap: 2px;
            width: fit-content;
            margin: 0 auto;
        }

        .cell-wrapper {
            position: relative;
        }

        .cell {
            border: 2px solid #333;
            width: 40px;
            height: 40px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            transition: all 0.3s;
        }

        .cell:focus {
            outline: none;
            border-color: #6a53b8;
            box-shadow: 0 0 0 3px rgba(106, 83, 184, 0.3);
            z-index: 10;
        }

        .blank {
            background-color: #2c2c2c;
            border: none;
        }

        .correct {
            background-color: #4caf50;
            color: white;
            border-color: #388e3c;
        }

        .error {
            background-color: #f44336;
            color: white;
            border-color: #d32f2f;
            animation: shake 0.5s;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .numero-palavra {
            position: absolute;
            top: 2px;
            left: 2px;
            font-size: 10px;
            font-weight: bold;
            color: #6a53b8;
            background: white;
            padding: 0 2px;
            border-radius: 2px;
            z-index: 5;
        }

        .dicas-container {
            background: white;
            padding: 2rem;
            border-radius: 1.5rem;
            max-height: 80vh;
            overflow-y: auto;
        }

        .dicas-container h2 {
            color: #6a53b8;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .palavra-dica {
            background: #f8f7fc;
            padding: 1rem;
            border-radius: 1rem;
            margin-bottom: 1rem;
        }

        .palavra-dica.completa {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
        }

        .palavra-dica h3 {
            color: #6a53b8;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .palavra-dica p {
            color: #666;
            font-size: 0.9rem;
            margin: 0.5rem 0;
        }

        .btn-dica {
            background: linear-gradient(135deg, #6a53b8 0%, #8b73d8 100%);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 1rem;
            cursor: pointer;
            font-size: 0.85rem;
            margin-right: 0.5rem;
            margin-top: 0.5rem;
            transition: all 0.3s;
        }

        .btn-dica:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(106, 83, 184, 0.4);
        }

        .btn-dica:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .dica-texto {
            background: #fff3cd;
            padding: 0.8rem;
            border-radius: 0.5rem;
            margin-top: 0.5rem;
            border-left: 4px solid #ffc107;
            display: none;
        }

        .dica-texto.show {
            display: block;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 1024px) {
            .game-area {
                grid-template-columns: 1fr;
            }

            .dicas-container {
                max-height: none;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .header-cruzadinha {
                text-align: center;
                padding: 1.5rem;
            }

            .header-cruzadinha h1 {
                font-size: 1.5rem;
                width: 100%;
            }

            .pontuacao-box {
                font-size: 1.2rem;
                width: 100%;
            }

            .cell {
                width: 35px;
                height: 35px;
                font-size: 16px;
            }

            .crossword-container {
                padding: 1rem;
            }
        }

        @media (max-width: 480px) {
            .cell {
                width: 30px;
                height: 30px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="container-cruzadinha">
        <div class="header-cruzadinha">
            <h1>Cruzadinha FrameFlow</h1>
            <div class="pontuacao-box">
                Pontuação: <span id="pontuacao"><?php echo $gameData['pontuacao_base']; ?></span>
            </div>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <button class="btn-nova-cruzadinha" onclick="location.reload()"><svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M19 10C19 7.61305 18.0518 5.32387 16.364 3.63604C14.6761 1.94821 12.3869 1 10 1C7.48395 1.00947 5.06897 1.99122 3.26 3.74L1 6"
                            stroke="#6A53B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M1 1V6H6" stroke="#6A53B8" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path
                            d="M1 10C1 12.3869 1.94821 14.6761 3.63604 16.364C5.32387 18.0518 7.61305 19 10 19C12.516 18.9905 14.931 18.0088 16.74 16.26L19 14"
                            stroke="#6A53B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M14 14H19V19" stroke="#6A53B8" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg> Nova Cruzadinha</button>
                <a href="explorar.php" class="btn-voltar">
                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M0.649902 12.6499C0.649902 19.2773 6.02248 24.6499 12.6499 24.6499C19.2773 24.6499 24.6499 19.2773 24.6499 12.6499C24.6499 6.02249 19.2773 0.649902 12.6499 0.649902C6.02248 0.649903 0.649902 6.02249 0.649902 12.6499Z"
                            stroke="#6A53B8" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M17.45 12.6499L7.84995 12.6499" stroke="#6A53B8" stroke-width="1.3"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M12.6499 7.8501L7.8499 12.6501L12.6499 17.4501" stroke="#6A53B8" stroke-width="1.3"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Voltar para o explorar</a>
            </div>
        </div>

        <div class="game-area">
            <div class="crossword-container">
                <div id="cruzadinha-matriz" class="crossword-grid"></div>
            </div>

            <div class="dicas-container">
                <h2>📝 Dicas</h2>
                <p style="color: #666; font-size: 0.9rem; margin-bottom: 1rem;">
                    As palavras estão interligadas! Uma letra em comum conecta cada palavra.
                </p>
                <div id="dicas-list">
                    <?php foreach ($palavras as $palavra): ?>
                        <div class="palavra-dica" data-palavra-id="<?php echo $palavra['id']; ?>" id="dica-card-<?php echo $palavra['id']; ?>">
                            <h3><?php echo $palavra['id']; ?>. <?php echo ucfirst($palavra['tipo']); ?> (<?php echo strlen($palavra['resposta']); ?> letras)</h3>
                            <p style="font-size: 0.85rem; color: #999;">
                                <?php echo $palavra['orientacao'] === 'H' ? 'Horizontal →' : 'Vertical ↓'; ?>
                            </p>

                            <button class="btn-dica" onclick="pedirDica(<?php echo $palavra['id']; ?>, 'facil')">
                                Dica Fácil (-<?php echo $gameData['penalidade_dica']; ?>)
                            </button>
                            <button class="btn-dica" onclick="pedirDica(<?php echo $palavra['id']; ?>, 'medio')">
                                Dica Média (-<?php echo $gameData['penalidade_dica']; ?>)
                            </button>
                            <button class="btn-dica" onclick="pedirDica(<?php echo $palavra['id']; ?>, 'dificil')">
                                Dica Difícil (-<?php echo $gameData['penalidade_dica']; ?>)
                            </button>

                            <div class="dica-texto" id="dica-facil-<?php echo $palavra['id']; ?>">
                                💡 <?php echo $palavra['dicas']['facil']; ?>
                            </div>
                            <div class="dica-texto" id="dica-medio-<?php echo $palavra['id']; ?>">
                                💡 <?php echo $palavra['dicas']['medio']; ?>
                            </div>
                            <div class="dica-texto" id="dica-dificil-<?php echo $palavra['id']; ?>">
                                💡 <?php echo $palavra['dicas']['dificil']; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        const gameData = <?php echo json_encode($gameData); ?>;
        let pontuacaoAtual = gameData.pontuacao_base;
        const dicasUsadas = {};
        const palavrasCompletas = new Set();

        function reduzirPontuacao(penalidade) {
            pontuacaoAtual = Math.max(0, pontuacaoAtual - penalidade);
            document.getElementById('pontuacao').textContent = pontuacaoAtual;
        }

        function montarCruzadinha() {
            const [rows, cols] = gameData.tamanho_matriz;
            const gridContainer = document.getElementById('cruzadinha-matriz');
            gridContainer.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;

            const grid = Array(rows).fill(0).map(() => Array(cols).fill(null));

            // Preenche o grid
            gameData.palavras.forEach(palavra => {
                for (let i = 0; i < palavra.resposta.length; i++) {
                    let r = palavra.pos_l - 1;
                    let c = palavra.pos_c - 1;

                    if (palavra.orientacao === 'H') {
                        c += i;
                    } else {
                        r += i;
                    }

                    if (!grid[r][c]) {
                        const cellWrapper = document.createElement('div');
                        cellWrapper.className = 'cell-wrapper';

                        if (i === 0) {
                            const numero = document.createElement('span');
                            numero.className = 'numero-palavra';
                            numero.textContent = palavra.id;
                            cellWrapper.appendChild(numero);
                        }

                        const input = document.createElement('input');
                        input.type = 'text';
                        input.maxLength = 1;
                        input.className = 'cell';
                        input.setAttribute('data-l', r);
                        input.setAttribute('data-c', c);
                        input.setAttribute('data-palavra-id', palavra.id);
                        input.setAttribute('data-pos', i);
                        input.setAttribute('data-letra', palavra.resposta[i]);
                        input.addEventListener('input', verificarResposta);
                        input.addEventListener('keydown', navegarTeclado);

                        cellWrapper.appendChild(input);
                        grid[r][c] = cellWrapper;
                    } else {
                        // Célula compartilhada por duas palavras
                        const input = grid[r][c].querySelector('input');
                        input.setAttribute('data-palavra-id', input.getAttribute('data-palavra-id') + ',' + palavra.id);
                    }
                }
            });

            // Renderiza
            for (let r = 0; r < rows; r++) {
                for (let c = 0; c < cols; c++) {
                    if (grid[r][c]) {
                        gridContainer.appendChild(grid[r][c]);
                    } else {
                        const blankDiv = document.createElement('div');
                        blankDiv.className = 'cell blank';
                        gridContainer.appendChild(blankDiv);
                    }
                }
            }
        }

        function navegarTeclado(event) {
            const input = event.target;
            const [rows, cols] = gameData.tamanho_matriz;
            const linha = parseInt(input.getAttribute('data-l'));
            const coluna = parseInt(input.getAttribute('data-c'));

            let novaLinha = linha;
            let novaColuna = coluna;

            switch (event.key) {
                case 'ArrowUp':
                    novaLinha = Math.max(0, linha - 1);
                    break;
                case 'ArrowDown':
                    novaLinha = Math.min(rows - 1, linha + 1);
                    break;
                case 'ArrowLeft':
                    novaColuna = Math.max(0, coluna - 1);
                    break;
                case 'ArrowRight':
                    novaColuna = Math.min(cols - 1, coluna + 1);
                    break;
                default:
                    return;
            }

            event.preventDefault();
            const proximoInput = document.querySelector(`[data-l="${novaLinha}"][data-c="${novaColuna}"]`);
            if (proximoInput && !proximoInput.disabled) {
                proximoInput.focus();
            }
        }

        function verificarResposta(event) {
            const input = event.target;
            const letra = input.value.toUpperCase();
            const letraCorreta = input.getAttribute('data-letra');
            const palavrasIds = input.getAttribute('data-palavra-id').split(',');

            if (letra === letraCorreta) {
                input.className = 'cell correct';
                input.disabled = true;
                palavrasIds.forEach(id => verificarPalavraCompleta(id));
            } else if (letra !== '') {
                input.className = 'cell error';
                reduzirPontuacao(gameData.penalidade_erro);
                setTimeout(() => {
                    input.value = '';
                    input.className = 'cell';
                }, 500);
            }
        }

        function verificarPalavraCompleta(palavraId) {
            const inputs = document.querySelectorAll(`[data-palavra-id*="${palavraId}"]`);
            const todosCorretos = Array.from(inputs).every(input => input.classList.contains('correct'));

            if (todosCorretos && !palavrasCompletas.has(palavraId)) {
                palavrasCompletas.add(palavraId);

                const card = document.getElementById(`dica-card-${palavraId}`);
                card.classList.add('completa');

                const palavra = gameData.palavras.find(p => p.id == palavraId);
                const mensagem = `🎉 Parabéns! Você completou: ${palavra.titulo_original}!`;
                mostrarNotificacao(mensagem);

                if (palavrasCompletas.size === gameData.palavras.length) {
                    setTimeout(() => {
                        alert(`🏆 VOCÊ VENCEU!\n\nPontuação final: ${pontuacaoAtual}\n\nParabéns por completar toda a cruzadinha!`);
                    }, 1000);
                }
            }
        }

        function mostrarNotificacao(mensagem) {
            const div = document.createElement('div');
            div.style.cssText = 'position: fixed; top: 20px; right: 20px; background: #4caf50; color: white; padding: 1rem 2rem; border-radius: 1rem; z-index: 9999; animation: slideIn 0.3s ease;';
            div.textContent = mensagem;
            document.body.appendChild(div);
            setTimeout(() => div.remove(), 3000);
        }

        function pedirDica(palavraId, nivelDica) {
            const chave = `${palavraId}-${nivelDica}`;

            if (!dicasUsadas[chave]) {
                reduzirPontuacao(gameData.penalidade_dica);
                document.getElementById(`dica-${nivelDica}-${palavraId}`).classList.add('show');
                dicasUsadas[chave] = true;

                event.target.disabled = true;
            }
        }

        window.onload = montarCruzadinha;

        function salvarPontuacaoCruzadinha() {
            const usuarioLogado = <?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>;

            if (!usuarioLogado) {
                console.warn('⚠️ Usuário não está logado');
                return;
            }

            // Calcular tempo total
            const tempoTotal = Math.floor((Date.now() - tempoInicio) / 1000);

            console.log('📝 Salvando pontuação da cruzadinha...', {
                jogo: 'cruzadinha',
                pontuacao: pontuacaoAtual,
                tempo: tempoTotal,
                nivel: 'normal'
            });

            const formData = new FormData();
            formData.append('jogo', 'cruzadinha');
            formData.append('pontuacao', pontuacaoAtual);
            formData.append('tempo', tempoTotal);
            formData.append('movimentos', null); // Cruzadinha não usa movimentos
            formData.append('nivel', 'normal');

            fetch('salvar_pontuacao.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(text => {
                    console.log('📥 Resposta:', text);
                    try {
                        const data = JSON.parse(text);
                        if (data.sucesso) {
                            console.log('✅ Pontuação da cruzadinha salva!');
                        } else {
                            console.error('❌ Erro:', data.erro);
                        }
                    } catch (e) {
                        console.error('❌ Erro ao parsear:', e, text);
                    }
                })
                .catch(error => console.error('❌ Erro na requisição:', error));
        }

        // Na função verificarPalavraCompleta, quando todas as palavras forem completadas, adicione:
        if (palavrasCompletas.size === gameData.palavras.length) {
            setTimeout(() => {
                salvarPontuacaoCruzadinha(); // ⭐ ADICIONE ESTA LINHA
                alert(`🏆 VOCÊ VENCEU!\n\nPontuação final: ${pontuacaoAtual}\n\nParabéns por completar toda a cruzadinha!`);
            }, 1000);
        }
    </script>
</body>

</html>