<?php

session_start();
require_once 'src/ConexaoBD.php';

header('Content-Type: application/json');

function calcularPontuacaoFinal($jogo, $pontuacaoBase, $tempo, $movimentos, $nivel)
{
    $pontuacao = $pontuacaoBase;

    switch ($jogo) {
        case 'quebra_cabeca':
            
            $penalTempo = ($tempo > 300) ? ($tempo - 300) * 5 : 0;
            
            $penalMovimentos = ($movimentos > 30) ? ($movimentos - 30) * 10 : 0;
            $pontuacao = max(100, $pontuacaoBase - $penalTempo - $penalMovimentos);
            break;

        case 'memoria':
            
            $pontuacao = $pontuacaoBase;
            break;

        case 'cruzadinha':
            
            $pontuacao = $pontuacaoBase;
            break;
    }

    $multiplicadores = [
        'facil' => 1.0,
        'medio' => 1.5,
        'dificil' => 2.0,
        'normal' => 1.0
    ];

    $mult = $multiplicadores[$nivel] ?? 1.0;
    $pontuacao = round($pontuacao * $mult);

    return max(100, min(20000, $pontuacao));
}

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Usuário não está logado'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Método inválido. Use POST.'
    ]);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$jogo = $_POST['jogo'] ?? null;
$pontuacao = isset($_POST['pontuacao']) ? intval($_POST['pontuacao']) : 0;
$tempo = isset($_POST['tempo']) ? intval($_POST['tempo']) : 0;
$movimentos = isset($_POST['movimentos']) ? intval($_POST['movimentos']) : null;
$nivel = $_POST['nivel'] ?? 'normal';

error_log("🎮 Recebendo pontuação:");
error_log("   Usuário ID: $usuario_id");
error_log("   Jogo: $jogo");
error_log("   Pontuação recebida: $pontuacao");
error_log("   Tempo: $tempo segundos");
error_log("   Movimentos: " . ($movimentos ?? 'N/A'));
error_log("   Nível: $nivel");

if (!$jogo || !in_array($jogo, ['quebra_cabeca', 'memoria', 'cruzadinha'])) {
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Tipo de jogo inválido',
        'jogo_recebido' => $jogo
    ]);
    exit;
}

$pontuacaoFinal = calcularPontuacaoFinal($jogo, $pontuacao, $tempo, $movimentos, $nivel);

error_log("   Pontuação final calculada: $pontuacaoFinal");

try {
    $conexao = ConexaoBD::conectar();

    $sql = "INSERT INTO pontuacoes_jogos 
            (usuario_id, jogo, pontuacao, tempo_segundos, movimentos, nivel, data_jogo) 
            VALUES 
            (:usuario_id, :jogo, :pontuacao, :tempo_segundos, :movimentos, :nivel, NOW())";

    $stmt = $conexao->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->bindValue(':jogo', $jogo, PDO::PARAM_STR);
    $stmt->bindValue(':pontuacao', $pontuacaoFinal, PDO::PARAM_INT);
    $stmt->bindValue(':tempo_segundos', $tempo, PDO::PARAM_INT);
    if ($movimentos === null) {
        $stmt->bindValue(':movimentos', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(':movimentos', $movimentos, PDO::PARAM_INT);
    }
    $stmt->bindValue(':nivel', $nivel, PDO::PARAM_STR);

    $sucesso = $stmt->execute();

    if ($sucesso) {
        $id_inserido = $conexao->lastInsertId();

        error_log("✅ Pontuação salva com sucesso! ID: $id_inserido");

        $sqlRanking = "SELECT 
                          COUNT(*) + 1 as posicao
                       FROM (
                           SELECT usuario_id, SUM(pontuacao) as total
                           FROM pontuacoes_jogos
                           WHERE jogo = :jogo
                           GROUP BY usuario_id
                           HAVING total > (
                               SELECT SUM(pontuacao)
                               FROM pontuacoes_jogos
                               WHERE usuario_id = :usuario_id AND jogo = :jogo2
                           )
                       ) as ranking";

        $stmtRanking = $conexao->prepare($sqlRanking);
        $stmtRanking->bindValue(':jogo', $jogo);
        $stmtRanking->bindValue(':jogo2', $jogo);
        $stmtRanking->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmtRanking->execute();
        $ranking = $stmtRanking->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Pontuação salva com sucesso!',
            'dados' => [
                'id' => $id_inserido,
                'pontuacao' => $pontuacaoFinal,
                'tempo' => $tempo,
                'movimentos' => $movimentos,
                'nivel' => $nivel,
                'posicao_ranking' => $ranking['posicao'] ?? 'N/A'
            ]
        ]);
    } else {
        throw new Exception("Falha ao executar INSERT");
    }
} catch (PDOException $e) {
    error_log("❌ Erro ao salvar pontuação: " . $e->getMessage());
    error_log("   SQL State: " . $e->getCode());

    echo json_encode([
        'sucesso' => false,
        'erro' => 'Erro ao salvar no banco de dados',
        'detalhes' => $e->getMessage(),
        'codigo' => $e->getCode()
    ]);
} catch (Exception $e) {
    error_log("❌ Erro geral: " . $e->getMessage());

    echo json_encode([
        'sucesso' => false,
        'erro' => 'Erro ao processar pontuação',
        'detalhes' => $e->getMessage()
    ]);
}
