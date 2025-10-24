<?php
session_start();
require_once('src/SeguidorDAO.php');

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Você precisa estar logado para seguir usuários.',
        'esta_seguindo' => false
    ]);
    exit;
}

// Pega os dados da requisição
$dados = json_decode(file_get_contents('php://input'), true);

if (!isset($dados['usuario_id']) || !isset($dados['acao'])) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Dados inválidos.',
        'esta_seguindo' => false
    ]);
    exit;
}

$seguidor_id = $_SESSION['usuario_id'];
$seguindo_id = (int)$dados['usuario_id'];
$acao = trim($dados['acao']);

// Validação adicional do ID
if ($seguindo_id <= 0) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'ID de usuário inválido.',
        'esta_seguindo' => false
    ]);
    exit;
}

// Verifica se não está tentando seguir a si mesmo
if ($seguidor_id == $seguindo_id) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Você não pode seguir a si mesmo.',
        'esta_seguindo' => false
    ]);
    exit;
}

try {
    if ($acao === 'seguir') {
        $resultado = SeguidorDAO::seguir($seguidor_id, $seguindo_id);

        if ($resultado) {
            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Agora você está seguindo este usuário!',
                'esta_seguindo' => true,
                'acao_realizada' => 'seguir'
            ]);
        } else {
            // Verifica se já estava seguindo
            $jaSeguindo = SeguidorDAO::estaSeguindo($seguidor_id, $seguindo_id);

            echo json_encode([
                'sucesso' => $jaSeguindo, // Se já estava seguindo, considera sucesso
                'mensagem' => $jaSeguindo ? 'Você já segue este usuário.' : 'Erro ao seguir usuário.',
                'esta_seguindo' => $jaSeguindo,
                'acao_realizada' => 'seguir'
            ]);
        }
    } elseif ($acao === 'deixar_seguir') {
        $resultado = SeguidorDAO::deixarDeSeguir($seguidor_id, $seguindo_id);

        if ($resultado) {
            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Você deixou de seguir este usuário.',
                'esta_seguindo' => false,
                'acao_realizada' => 'deixar_seguir'
            ]);
        } else {
            // Verifica se realmente não estava seguindo
            $estaSeguindo = SeguidorDAO::estaSeguindo($seguidor_id, $seguindo_id);

            echo json_encode([
                'sucesso' => !$estaSeguindo, // Se não estava seguindo, considera sucesso
                'mensagem' => !$estaSeguindo ? 'Você não estava seguindo este usuário.' : 'Erro ao deixar de seguir.',
                'esta_seguindo' => $estaSeguindo,
                'acao_realizada' => 'deixar_seguir'
            ]);
        }
    } else {
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Ação inválida. Use "seguir" ou "deixar_seguir".',
            'esta_seguindo' => false
        ]);
    }
} catch (Exception $e) {
    error_log("Erro ao processar ação de seguir: " . $e->getMessage());

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao processar solicitação. Tente novamente.',
        'esta_seguindo' => false,
        'erro_debug' => $e->getMessage() // Remova em produção
    ]);
}
