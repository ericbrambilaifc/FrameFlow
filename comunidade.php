<?php
session_start();
require_once 'src/ConexaoBD.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$conexao = new ConexaoBD();
$pdo = $conexao->conectar();

// Busca o usuário logado
$stmt = $pdo->prepare("SELECT nome_completo, foto_perfil FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario_logado = $stmt->fetch(PDO::FETCH_ASSOC);

// Busca o TOP 3 - Maiores avaliadores
$query_top3 = "
    SELECT 
        u.id,
        u.nome_completo,
        u.foto_perfil,
        COUNT(a.id) as total_avaliacoes
    FROM usuarios u
    LEFT JOIN avaliacoes a ON u.id = a.usuario_id
    WHERE u.is_admin = 0
    GROUP BY u.id, u.nome_completo, u.foto_perfil
    ORDER BY total_avaliacoes DESC
    LIMIT 3
";
$stmt_top3 = $pdo->query($query_top3);
$top3 = $stmt_top3->fetchAll(PDO::FETCH_ASSOC);

// Busca os demais usuários (do 4º ao 10º)
$query_demais = "
    SELECT 
        u.id,
        u.nome_completo,
        COUNT(a.id) as total_avaliacoes
    FROM usuarios u
    LEFT JOIN avaliacoes a ON u.id = a.usuario_id
    WHERE u.is_admin = 0
    GROUP BY u.id, u.nome_completo
    ORDER BY total_avaliacoes DESC
    LIMIT 7 OFFSET 3
";
$stmt_demais = $pdo->query($query_demais);
$demais = $stmt_demais->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maiores Avaliadores - FrameFlow</title>
    <link rel="stylesheet" href="global.css">
    <link rel="stylesheet" href="comunidade.css">
    <link rel="stylesheet" href="explorar.css">
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
                    <input
                        type="text"
                        class="search-input"
                        placeholder="Buscar usuários..."
                        id="searchUsers"
                        autocomplete="off">
                    <button class="clear-search" id="clearSearch">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
                <div class="search-results" id="searchResults"></div>
            </div>
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
                    Seu Perfil
                </a>
            <?php endif; ?>
        </div>
    </header>

    <div class="container-comunidade">
        <!-- Header -->
        <div class="comunidade-header">
            <div class="perfil-usuario">
                <div class="avatar-pequeno">
                    <?php if ($usuario_logado['foto_perfil'] && file_exists('uploads/perfil/' . $usuario_logado['foto_perfil'])): ?>
                        <img src="uploads/perfil/<?php echo htmlspecialchars($usuario_logado['foto_perfil']); ?>"
                            alt="<?php echo htmlspecialchars($usuario_logado['nome_completo']); ?>">
                    <?php else: ?>
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="8" r="4" fill="#6A53B8" />
                            <path d="M4 20c0-4 3.5-7 8-7s8 3 8 7" fill="#6A53B8" />
                        </svg>
                    <?php endif; ?>
                </div>
                <span class="nome-usuario"><?php echo htmlspecialchars($usuario_logado['nome_completo']); ?></span>
            </div>

            <a href="explorar.php" class="btn-voltar">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Voltar
            </a>
        </div>

        <!-- Título -->
        <div class="titulo-secao">
            <h1>Ranking dos Avaliadores</h1>

        </div>



        <!-- Pódio TOP 3 -->
        <div class="podio">
            <?php if (isset($top3[1])): // 2º lugar 
            ?>
                <div class="posicao posicao-2">
                    <div class="avatar-podio avatar-segundo">
                        <?php if ($top3[1]['foto_perfil'] && file_exists('uploads/perfil/' . $top3[1]['foto_perfil'])): ?>
                            <img src="uploads/perfil/<?php echo htmlspecialchars($top3[1]['foto_perfil']); ?>"
                                alt="<?php echo htmlspecialchars($top3[1]['nome_completo']); ?>">
                        <?php else: ?>
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="8" r="4" fill="#6A53B8" />
                                <path d="M4 20c0-4 3.5-7 8-7s8 3 8 7" fill="#6A53B8" />
                            </svg>
                        <?php endif; ?>
                    </div>
                    <a href="perfil.php?id=<?php echo $top3[1]['id']; ?>" class="nome-link">
                        <?php echo htmlspecialchars($top3[1]['nome_completo']); ?>
                    </a>
                    <div class="badge-posicao segundo">
                        2º Lugar
                    </div>
                    <span class="total-avaliacoes"><?php echo $top3[1]['total_avaliacoes']; ?> avaliações</span>
                </div>
            <?php endif; ?>

            <?php if (isset($top3[0])): // 1º lugar 
            ?>
                <div class="posicao posicao-1">
                    <div class="avatar-podio">

                        <?php if ($top3[0]['foto_perfil'] && file_exists('uploads/perfil/' . $top3[0]['foto_perfil'])): ?>
                            <img src="uploads/perfil/<?php echo htmlspecialchars($top3[0]['foto_perfil']); ?>"
                                alt="<?php echo htmlspecialchars($top3[0]['nome_completo']); ?>">
                        <?php else: ?>

                        <?php endif; ?>
                    </div>
                    <a href="perfil.php?id=<?php echo $top3[0]['id']; ?>" class="nome-link">
                        <?php echo htmlspecialchars($top3[0]['nome_completo']); ?>
                    </a>
                    <div class="badge-posicao primeiro">

                        1º Lugar
                    </div>
                    <span class="total-avaliacoes"><?php echo $top3[0]['total_avaliacoes']; ?> avaliações</span>
                </div>
            <?php endif; ?>

            <?php if (isset($top3[2])): // 3º lugar 
            ?>
                <div class="posicao posicao-3">
                    <div class="avatar-podio avatar-terceiro">
                        <?php if ($top3[2]['foto_perfil'] && file_exists('uploads/perfil/' . $top3[2]['foto_perfil'])): ?>
                            <img src="uploads/perfil/<?php echo htmlspecialchars($top3[2]['foto_perfil']); ?>"
                                alt="<?php echo htmlspecialchars($top3[2]['nome_completo']); ?>">
                        <?php else: ?>
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="8" r="4" fill="#6A53B8" />
                                <path d="M4 20c0-4 3.5-7 8-7s8 3 8 7" fill="#6A53B8" />
                            </svg>
                        <?php endif; ?>
                    </div>
                    <a href="perfil.php?id=<?php echo $top3[2]['id']; ?>" class="nome-link">
                        <?php echo htmlspecialchars($top3[2]['nome_completo']); ?>
                    </a>
                    <div class="badge-posicao terceiro">
                        3º Lugar
                    </div>
                    <span class="total-avaliacoes"><?php echo $top3[2]['total_avaliacoes']; ?> avaliações</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Lista dos demais colocados -->
        <?php if (count($demais) > 0): ?>
            <div class="lista-demais">
                <?php foreach ($demais as $index => $usuario): ?>
                    <div class="item-comunidade">
                        <span class="numero-posicao"><?php echo ($index + 4); ?>º</span>
                        <a href="perfil.php?id=<?php echo $usuario['id']; ?>" class="nome-usuario-item">
                            <?php echo htmlspecialchars($usuario['nome_completo']); ?>
                        </a>
                        <span class="total-avaliacoes-item">
                            <?php echo $usuario['total_avaliacoes']; ?> avaliações
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>