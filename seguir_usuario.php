<?php
session_start();
require_once('src/SeguidorDAO.php');

header('Content-Type: application/json');

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Você precisa estar logado para seguir usuários.'
    ]);
    exit;
}

// Pega os dados da requisição
$dados = json_decode(file_get_contents('php://input'), true);

if (!isset($dados['usuario_id']) || !isset($dados['acao'])) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Dados inválidos.'
    ]);
    exit;
}

$seguidor_id = $_SESSION['usuario_id'];
$seguindo_id = (int)$dados['usuario_id'];
$acao = $dados['acao'];

// Verifica se não está tentando seguir a si mesmo
if ($seguidor_id == $seguindo_id) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Você não pode seguir a si mesmo.'
    ]);
    exit;
}

try {
    if ($acao === 'seguir') {
        $resultado = SeguidorDAO::seguir($seguidor_id, $seguindo_id);
        if ($resultado) {
            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Agora você está seguindo este usuário!'
            ]);
        } else {
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Você já segue este usuário.'
            ]);
        }
    } elseif ($acao === 'deixar_seguir') {
        $resultado = SeguidorDAO::deixarDeSeguir($seguidor_id, $seguindo_id);
        if ($resultado) {
            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Você deixou de seguir este usuário.'
            ]);
        } else {
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Erro ao deixar de seguir.'
            ]);
        }
    } else {
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Ação inválida.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao processar solicitação: ' . $e->getMessage()
    ]);
}
?>