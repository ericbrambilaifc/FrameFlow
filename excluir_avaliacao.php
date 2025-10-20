<?php
session_start();
header('Content-Type: application/json');

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Você precisa estar logado.']);
    exit;
}

require_once('src/AvaliacaoDAO.php');

// Recebe os dados JSON
$dados = json_decode(file_get_contents('php://input'), true);

if (!isset($dados['avaliacao_id'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'ID da avaliação não informado.']);
    exit;
}

$avaliacao_id = (int) $dados['avaliacao_id'];
$usuario_id = $_SESSION['usuario_id'];

try {
    // Verifica se a avaliação existe e pertence ao usuário
    $avaliacao = AvaliacaoDAO::obterPorId($avaliacao_id);
    
    if (!$avaliacao) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Avaliação não encontrada.']);
        exit;
    }
    
    if ($avaliacao['usuario_id'] != $usuario_id) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Você não tem permissão para excluir esta avaliação.']);
        exit;
    }
    
    // Exclui a avaliação
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