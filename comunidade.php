<?php
session_start();
require_once('src/UsuarioDAO.php');

// Buscar ranking de avaliadores
$rankingAvaliacoes = UsuarioDAO::obterRankingAvaliacoes();

// Buscar ranking de jogadores
$rankingJogadores = UsuarioDAO::obterRankingJogadores();

// Informações do usuário logado
$usuario_logado = null;
$foto_perfil_logado = null;

if (isset($_SESSION['usuario_id'])) {
    $usuario_logado = UsuarioDAO::obterPorId($_SESSION['usuario_id']);
    $foto_perfil_logado = $usuario_logado['foto_perfil'] ?? null;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunidade FrameFlow</title>
    <link rel="stylesheet" href="global.css">
    <link rel="stylesheet" href="comunidade.css">
</head>

<body>
    <div class="container-comunidade">
        <!-- Header -->
        <div class="comunidade-header">
            <?php if ($usuario_logado): ?>
                <div class="perfil-usuario">
                    <div class="avatar-pequeno">
                        <?php if ($foto_perfil_logado): ?>
                            <img src="uploads/perfil/<?php echo htmlspecialchars($foto_perfil_logado); ?>"
                                alt="<?php echo htmlspecialchars($usuario_logado['nome_completo']); ?>">
                        <?php else: ?>
                            <svg viewBox="0 0 24 24" fill="#6A53B8">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                            </svg>
                        <?php endif; ?>
                    </div>
                    <span class="nome-usuario"><?php echo htmlspecialchars($usuario_logado['nome_completo']); ?></span>
                </div>
            <?php endif; ?>

            <a href="explorar.php" class="btn-voltar">
                ← Voltar para Explorar
            </a>
        </div>

        <!-- Título -->
        <div class="titulo-secao">
            <svg width="50" height="50" viewBox="0 0 24 24" fill="#6A53B8">
                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
            </svg>
            <h1>Comunidade FrameFlow</h1>
        </div>

        <!-- Toggle Ranking -->
        <div class="toggle-ranking">
            <button class="toggle-btn active" id="btnAvaliacoes" onclick="alternarRanking('avaliacoes')">
                ⭐ Maiores Avaliadores
            </button>
            <button class="toggle-btn" id="btnJogadores" onclick="alternarRanking('jogadores')">
                🎮 Melhores Jogadores
            </button>
        </div>

        <!-- RANKING DE AVALIADORES -->
        <div class="ranking-section active" id="rankingAvaliacoes">
            <!-- Pódio -->
            <?php if (count($rankingAvaliacoes) >= 3): ?>
                <div class="podio">
                    <!-- 2º Lugar -->
                    <div class="posicao posicao-2">
                        <div class="avatar-podio">
                            <?php if ($rankingAvaliacoes[1]['foto_perfil']): ?>
                                <img src="uploads/perfil/<?php echo htmlspecialchars($rankingAvaliacoes[1]['foto_perfil']); ?>"
                                    alt="<?php echo htmlspecialchars($rankingAvaliacoes[1]['nome_completo']); ?>">
                            <?php else: ?>
                                <svg viewBox="0 0 24 24" fill="#6A53B8">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                </svg>
                            <?php endif; ?>
                        </div>
                        <a href="perfil.php?id=<?php echo $rankingAvaliacoes[1]['id']; ?>" class="nome-link">
                            <?php echo htmlspecialchars($rankingAvaliacoes[1]['nome_completo']); ?>
                        </a>
                        <div class="badge-posicao segundo">🥈 2º Lugar</div>
                        <p class="total-avaliacoes"><?php echo $rankingAvaliacoes[1]['total_avaliacoes']; ?> avaliações</p>
                    </div>

                    <!-- 1º Lugar -->
                    <div class="posicao posicao-1">
                        <div class="avatar-podio">
                            <?php if ($rankingAvaliacoes[0]['foto_perfil']): ?>
                                <img src="uploads/perfil/<?php echo htmlspecialchars($rankingAvaliacoes[0]['foto_perfil']); ?>"
                                    alt="<?php echo htmlspecialchars($rankingAvaliacoes[0]['nome_completo']); ?>">
                            <?php else: ?>
                                <svg viewBox="0 0 24 24" fill="white">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                </svg>
                            <?php endif; ?>
                        </div>
                        <a href="perfil.php?id=<?php echo $rankingAvaliacoes[0]['id']; ?>" class="nome-link">
                            <?php echo htmlspecialchars($rankingAvaliacoes[0]['nome_completo']); ?>
                        </a>
                        <div class="badge-posicao primeiro">👑 1º Lugar</div>
                        <p class="total-avaliacoes"><?php echo $rankingAvaliacoes[0]['total_avaliacoes']; ?> avaliações</p>
                    </div>

                    <!-- 3º Lugar -->
                    <div class="posicao posicao-3">
                        <div class="avatar-podio">
                            <?php if ($rankingAvaliacoes[2]['foto_perfil']): ?>
                                <img src="uploads/perfil/<?php echo htmlspecialchars($rankingAvaliacoes[2]['foto_perfil']); ?>"
                                    alt="<?php echo htmlspecialchars($rankingAvaliacoes[2]['nome_completo']); ?>">
                            <?php else: ?>
                                <svg viewBox="0 0 24 24" fill="#6A53B8">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                </svg>
                            <?php endif; ?>
                        </div>
                        <a href="perfil.php?id=<?php echo $rankingAvaliacoes[2]['id']; ?>" class="nome-link">
                            <?php echo htmlspecialchars($rankingAvaliacoes[2]['nome_completo']); ?>
                        </a>
                        <div class="badge-posicao terceiro">🥉 3º Lugar</div>
                        <p class="total-avaliacoes"><?php echo $rankingAvaliacoes[2]['total_avaliacoes']; ?> avaliações</p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Demais Colocados -->
            <?php if (count($rankingAvaliacoes) > 3): ?>
                <div class="lista-demais">
                    <?php for ($i = 3; $i < count($rankingAvaliacoes); $i++): ?>
                        <div class="item-comunidade">
                            <span class="numero-posicao"><?php echo ($i + 1); ?>º</span>
                            <a href="perfil.php?id=<?php echo $rankingAvaliacoes[$i]['id']; ?>" class="nome-usuario-item">
                                <?php echo htmlspecialchars($rankingAvaliacoes[$i]['nome_completo']); ?>
                            </a>
                            <span class="total-avaliacoes-item">
                                ⭐ <?php echo $rankingAvaliacoes[$i]['total_avaliacoes']; ?> avaliações
                            </span>
                        </div>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- RANKING DE JOGADORES -->
        <div class="ranking-section" id="rankingJogadores">
            <?php if (count($rankingJogadores) >= 3): ?>
                <!-- Pódio Jogadores -->
                <div class="podio">
                    <!-- 2º Lugar -->
                    <div class="posicao posicao-2">
                        <div class="avatar-podio">
                            <?php if ($rankingJogadores[1]['foto_perfil']): ?>
                                <img src="uploads/perfil/<?php echo htmlspecialchars($rankingJogadores[1]['foto_perfil']); ?>"
                                    alt="<?php echo htmlspecialchars($rankingJogadores[1]['nome_completo']); ?>">
                            <?php else: ?>
                                <svg viewBox="0 0 24 24" fill="#6A53B8">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                </svg>
                            <?php endif; ?>
                        </div>
                        <a href="perfil.php?id=<?php echo $rankingJogadores[1]['id']; ?>" class="nome-link">
                            <?php echo htmlspecialchars($rankingJogadores[1]['nome_completo']); ?>
                        </a>
                        <div class="badge-posicao segundo">🥈 2º Lugar</div>
                        <p class="total-avaliacoes"><?php echo number_format($rankingJogadores[1]['total_pontos']); ?> pontos</p>
                        <div class="stats-extras">
                            <span>🎮 <?php echo $rankingJogadores[1]['total_jogos']; ?> jogos</span>
                        </div>
                    </div>

                    <!-- 1º Lugar -->
                    <div class="posicao posicao-1">
                        <div class="avatar-podio">
                            <?php if ($rankingJogadores[0]['foto_perfil']): ?>
                                <img src="uploads/perfil/<?php echo htmlspecialchars($rankingJogadores[0]['foto_perfil']); ?>"
                                    alt="<?php echo htmlspecialchars($rankingJogadores[0]['nome_completo']); ?>">
                            <?php else: ?>
                                <svg viewBox="0 0 24 24" fill="white">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                </svg>
                            <?php endif; ?>
                        </div>
                        <a href="perfil.php?id=<?php echo $rankingJogadores[0]['id']; ?>" class="nome-link">
                            <?php echo htmlspecialchars($rankingJogadores[0]['nome_completo']); ?>
                        </a>
                        <div class="badge-posicao primeiro">👑 1º Lugar</div>
                        <p class="total-avaliacoes"><?php echo number_format($rankingJogadores[0]['total_pontos']); ?> pontos</p>
                        <div class="stats-extras">
                            <span>🎮 <?php echo $rankingJogadores[0]['total_jogos']; ?> jogos</span>
                        </div>
                    </div>

                    <!-- 3º Lugar -->
                    <div class="posicao posicao-3">
                        <div class="avatar-podio">
                            <?php if ($rankingJogadores[2]['foto_perfil']): ?>
                                <img src="uploads/perfil/<?php echo htmlspecialchars($rankingJogadores[2]['foto_perfil']); ?>"
                                    alt="<?php echo htmlspecialchars($rankingJogadores[2]['nome_completo']); ?>">
                            <?php else: ?>
                                <svg viewBox="0 0 24 24" fill="#6A53B8">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                </svg>
                            <?php endif; ?>
                        </div>
                        <a href="perfil.php?id=<?php echo $rankingJogadores[2]['id']; ?>" class="nome-link">
                            <?php echo htmlspecialchars($rankingJogadores[2]['nome_completo']); ?>
                        </a>
                        <div class="badge-posicao terceiro">🥉 3º Lugar</div>
                        <p class="total-avaliacoes"><?php echo number_format($rankingJogadores[2]['total_pontos']); ?> pontos</p>
                        <div class="stats-extras">
                            <span>🎮 <?php echo $rankingJogadores[2]['total_jogos']; ?> jogos</span>
                        </div>
                    </div>
                </div>

                <!-- Demais Colocados Jogadores -->
                <?php if (count($rankingJogadores) > 3): ?>
                    <div class="lista-demais">
                        <?php for ($i = 3; $i < count($rankingJogadores); $i++): ?>
                            <div class="item-comunidade">
                                <span class="numero-posicao"><?php echo ($i + 1); ?>º</span>
                                <a href="perfil.php?id=<?php echo $rankingJogadores[$i]['id']; ?>" class="nome-usuario-item">
                                    <?php echo htmlspecialchars($rankingJogadores[$i]['nome_completo']); ?>
                                </a>
                                <div>
                                    <span class="total-avaliacoes-item">
                                        🏆 <?php echo number_format($rankingJogadores[$i]['total_pontos']); ?> pontos
                                    </span>
                                    <div class="stats-extras" style="margin-top: 0.2rem;">
                                        <span>🎮 <?php echo $rankingJogadores[$i]['total_jogos']; ?> jogos</span>
                                        <span>⭐ Melhor: <?php echo number_format($rankingJogadores[$i]['melhor_pontuacao']); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="sem-dados" style="text-align: center; padding: 3rem; color: #999;">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="#ddd" style="margin-bottom: 1rem;">
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                    </svg>
                    <p style="font-size: 1.2rem;">Nenhum jogador no ranking ainda.</p>
                    <p style="margin-top: 0.5rem;">Seja o primeiro a jogar e conquistar pontos!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function alternarRanking(tipo) {
            // Alternar botões
            const btnAvaliacoes = document.getElementById('btnAvaliacoes');
            const btnJogadores = document.getElementById('btnJogadores');

            // Alternar seções
            const rankingAvaliacoes = document.getElementById('rankingAvaliacoes');
            const rankingJogadores = document.getElementById('rankingJogadores');

            if (tipo === 'avaliacoes') {
                btnAvaliacoes.classList.add('active');
                btnJogadores.classList.remove('active');
                rankingAvaliacoes.classList.add('active');
                rankingJogadores.classList.remove('active');
            } else {
                btnJogadores.classList.add('active');
                btnAvaliacoes.classList.remove('active');
                rankingJogadores.classList.add('active');
                rankingAvaliacoes.classList.remove('active');
            }
        }
    </script>
</body>

</html>