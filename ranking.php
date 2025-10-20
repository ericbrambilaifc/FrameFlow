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
    <link rel="stylesheet" href="ranking.css">
</head>

<body>
    <div class="container-ranking">
        <!-- Header -->
        <div class="ranking-header">
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

            <a href="index.php" class="btn-voltar-ranking">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Voltar
            </a>
        </div>

        <!-- Título -->
        <div class="titulo-secao">
            <h1>Maiores Avaliadores</h1>
            <div class="icone-medalha">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="10" r="6" fill="#FFD700" stroke="#FFA500" stroke-width="2" />
                    <path d="M12 16L9 22L10 18L7 17L12 16Z" fill="#FFD700" stroke="#FFA500" stroke-width="1.5" />
                    <path d="M12 16L15 22L14 18L17 17L12 16Z" fill="#FFD700" stroke="#FFA500" stroke-width="1.5" />
                </svg>
            </div>
        </div>

        

        <!-- Pódio TOP 3 -->
        <div class="podio">
            <?php if (isset($top3[1])): // 2º lugar 
            ?>
                <div class="posicao posicao-2">
                    <a href="perfil.php?id=<?php echo $top3[1]['id']; ?>" class="nome-link">
                        <?php echo htmlspecialchars($top3[1]['nome_completo']); ?>
                    </a>
                    <div class="badge-posicao segundo">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" fill="#C0C0C0" />
                        </svg>
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
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="8" r="4" fill="#6A53B8" />
                                <path d="M4 20c0-4 3.5-7 8-7s8 3 8 7" fill="#6A53B8" />
                            </svg>
                        <?php endif; ?>
                    </div>
                    <a href="perfil.php?id=<?php echo $top3[0]['id']; ?>" class="nome-link">
                        <?php echo htmlspecialchars($top3[0]['nome_completo']); ?>
                    </a>
                    <div class="badge-posicao primeiro">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" fill="#FFD700" />
                        </svg>
                        1º Lugar
                    </div>
                    <span class="total-avaliacoes"><?php echo $top3[0]['total_avaliacoes']; ?> avaliações</span>
                </div>
            <?php endif; ?>

            <?php if (isset($top3[2])): // 3º lugar 
            ?>
                <div class="posicao posicao-3">
                    <a href="perfil.php?id=<?php echo $top3[2]['id']; ?>" class="nome-link">
                        <?php echo htmlspecialchars($top3[2]['nome_completo']); ?>
                    </a>
                    <div class="badge-posicao terceiro">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" fill="#CD7F32" />
                        </svg>
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
                    <div class="item-ranking">
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