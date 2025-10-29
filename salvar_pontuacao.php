<?php
session_start();
require_once('src/ConexaoBD.php');

if (!isset($_SESSION['usuario_id'])) {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    $jogo = $_POST['jogo'] ?? '';
    $pontuacao = intval($_POST['pontuacao'] ?? 0);
    $tempo = intval($_POST['tempo'] ?? 0);
    $movimentos = intval($_POST['movimentos'] ?? 0);
    $nivel = $_POST['nivel'] ?? 'facil';

    try {
        $conexao = ConexaoBD::conectar();
        $sql = "INSERT INTO pontuacoes_jogos (usuario_id, jogo, pontuacao, tempo_segundos, movimentos, nivel) 
                VALUES (:usuario_id, :jogo, :pontuacao, :tempo, :movimentos, :nivel)";
        $stmt = $conexao->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':jogo' => $jogo,
            ':pontuacao' => $pontuacao,
            ':tempo' => $tempo,
            ':movimentos' => $movimentos,
            ':nivel' => $nivel
        ]);

        echo json_encode(['sucesso' => true]);
    } catch (PDOException $e) {
        error_log("Erro ao salvar pontuação: " . $e->getMessage());
        echo json_encode(['sucesso' => false]);
    }
}
