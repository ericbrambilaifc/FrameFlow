<?php
session_start();
require_once('src/CurtidaDAO.php');

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Você precisa estar logado para curtir avaliações.'
    ]);
    exit;
}

if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Administradores não podem curtir avaliações.'
    ]);
    exit;
}

if (!isset($_POST['avaliacao_id'])) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'ID da avaliação não fornecido.'
    ]);
    exit;
}

$avaliacao_id = intval($_POST['avaliacao_id']);
$usuario_id = $_SESSION['usuario_id'];

try {
    $resultado = CurtidaDAO::toggleCurtida($usuario_id, $avaliacao_id);

    if ($resultado['sucesso']) {
        $mensagem = $resultado['acao'] === 'curtiu'
            ? 'Avaliação curtida!'
            : 'Curtida removida!';

        echo json_encode([
            'sucesso' => true,
            'acao' => $resultado['acao'],
            'mensagem' => $mensagem,
            'total_curtidas' => $resultado['total_curtidas']
        ]);
    } else {
        echo json_encode([
            'sucesso' => false,
            'mensagem' => $resultado['mensagem'] ?? 'Erro ao processar curtida'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao processar curtida: ' . $e->getMessage()
    ]);
}
