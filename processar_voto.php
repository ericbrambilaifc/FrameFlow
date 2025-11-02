<?php
// processar_voto.php
session_start();
header('Content-Type: application/json');

require_once('src/ConexaoBD.php');
require_once('src/AvaliacaoDAO.php');
require_once('src/VotoDAO.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Você precisa estar logado para votar!'
    ]);
    exit;
}

// Verifica se é admin (admin não pode votar)
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Administradores não podem votar em avaliações!'
    ]);
    exit;
}

// Verifica se os dados foram enviados
if (!isset($_POST['avaliacao_id']) || !isset($_POST['tipo_voto'])) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Dados incompletos!'
    ]);
    exit;
}

$avaliacao_id = intval($_POST['avaliacao_id']);
$usuario_id = intval($_SESSION['usuario_id']);
$tipo_voto = intval($_POST['tipo_voto']);

// Valida o tipo de voto (deve ser 1 ou -1)
if ($tipo_voto !== 1 && $tipo_voto !== -1) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Tipo de voto inválido!'
    ]);
    exit;
}

try {
    // Obtém a conexão com o banco
    $pdo = ConexaoBD::conectar();

    // Busca a avaliação e verifica se existe
    $stmt = $pdo->prepare("SELECT id, usuario_id FROM avaliacoes WHERE id = ?");
    $stmt->execute([$avaliacao_id]);
    $avaliacao = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$avaliacao) {
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Avaliação não encontrada!'
        ]);
        exit;
    }

    // Busca o voto atual do usuário
    $voto_anterior = VotoDAO::buscarVotoUsuario($avaliacao_id, $usuario_id);

    // Processa o voto
    $sucesso = VotoDAO::votar($avaliacao_id, $usuario_id, $tipo_voto);

    if ($sucesso) {
        // Determina o novo estado do voto
        $voto_atual = null;
        if ($voto_anterior === null) {
            // Novo voto
            $voto_atual = $tipo_voto;
            $mensagem = $tipo_voto == 1 ? 'Like registrado!' : 'Dislike registrado!';
        } elseif ($voto_anterior == $tipo_voto) {
            // Removeu o voto (toggle off)
            $voto_atual = null;
            $mensagem = 'Voto removido!';
        } else {
            // Trocou o voto
            $voto_atual = $tipo_voto;
            $mensagem = $tipo_voto == 1 ? 'Mudou para like!' : 'Mudou para dislike!';
        }

        // Busca os totais atualizados
        $totais = VotoDAO::contarVotos($avaliacao_id);

        echo json_encode([
            'sucesso' => true,
            'mensagem' => $mensagem,
            'voto_atual' => $voto_atual,
            'total_likes' => $totais['likes'],
            'total_dislikes' => $totais['dislikes']
        ]);
    } else {
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Erro ao processar voto. Tente novamente.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao processar voto.'
    ]);
    error_log("Erro ao processar voto: " . $e->getMessage());
}
