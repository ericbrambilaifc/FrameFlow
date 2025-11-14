<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Você precisa estar logado.']);
    exit;
}

require_once('src/AvaliacaoDAO.php');

$dados = json_decode(file_get_contents('php:

if (!isset($dados['avaliacao_id'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'ID da avaliação não informado.']);
    exit;
}

$avaliacao_id = (int) $dados['avaliacao_id'];
$usuario_id = $_SESSION['usuario_id'];

try {
    
    $avaliacao = AvaliacaoDAO::obterPorId($avaliacao_id);
    
    if (!$avaliacao) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Avaliação não encontrada.']);
        exit;
    }
    
    if ($avaliacao['usuario_id'] != $usuario_id) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Você não tem permissão para excluir esta avaliação.']);
        exit;
    }
    
    $sucesso = AvaliacaoDAO::deletarPorId($avaliacao_id);
    
    if ($sucesso) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Avaliação excluída com sucesso!']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao excluir avaliação.']);
    }
    
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao processar solicitação.']);
}
?>