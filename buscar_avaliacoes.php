<?php
// buscar_avaliacoes.php
session_start();
require_once('src/AvaliacaoDAO.php');
require_once('src/UsuarioDAO.php');
require_once('src/CurtidaDAO.php');

header('Content-Type: application/json');

if (!isset($_GET['serie_id'])) {
    echo json_encode(['erro' => 'ID da série não fornecido']);
    exit;
}

$serie_id = intval($_GET['serie_id']);

// Verifica se o usuário está logado
$usuario_logado_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
$eh_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

try {
    // Buscar avaliações da série
    $avaliacoes = AvaliacaoDAO::listarPorSerie($serie_id);

    // Buscar informações dos usuários e curtidas
    $avaliacoesCompletas = [];
    $avaliacoes_ids = []; // Para buscar curtidas do usuário em lote

    // Coleta os IDs das avaliações
    foreach ($avaliacoes as $avaliacao) {
        $avaliacoes_ids[] = $avaliacao['id'];
    }

    // Busca as curtidas do usuário logado para todas as avaliações de uma vez
    $curtidas_usuario = [];
    if ($usuario_logado_id && !$eh_admin && !empty($avaliacoes_ids)) {
        $curtidas_usuario = CurtidaDAO::buscarCurtidasUsuario($usuario_logado_id, $avaliacoes_ids);
    }

    // Monta o array completo de avaliações
    foreach ($avaliacoes as $avaliacao) {
        $usuario = UsuarioDAO::buscarPorId($avaliacao['usuario_id']);
        $avaliacao_id = $avaliacao['id'];

        // Conta total de curtidas da avaliação
        $total_curtidas = CurtidaDAO::contarCurtidas($avaliacao_id);

        // Verifica se o usuário logado curtiu esta avaliação
        $ja_curtiu = in_array($avaliacao_id, $curtidas_usuario);

        // Pega as iniciais do usuário
        $iniciais = '';
        if ($usuario && $usuario['nome_completo']) {
            $nomes = explode(' ', $usuario['nome_completo']);
            $iniciais = strtoupper(substr($nomes[0], 0, 1));
            if (isset($nomes[1])) {
                $iniciais .= strtoupper(substr($nomes[1], 0, 1));
            }
        }

        $avaliacoesCompletas[] = [
            'id' => $avaliacao['id'],
            'nota' => $avaliacao['nota'],
            'comentario' => $avaliacao['comentario'],
            'data_avaliacao' => $avaliacao['data_avaliacao'],
            'usuario_id' => $avaliacao['usuario_id'],
            'usuario_nome' => $usuario ? $usuario['nome_completo'] : 'Usuário',
            'foto_perfil' => $usuario ? $usuario['foto_perfil'] : null,
            'iniciais' => $iniciais,
            'total_curtidas' => $total_curtidas,
            'ja_curtiu' => $ja_curtiu,
            'pode_curtir' => ($usuario_logado_id && !$eh_admin && $usuario_logado_id != $avaliacao['usuario_id'])
        ];
    }

    echo json_encode([
        'avaliacoes' => $avaliacoesCompletas,
        'usuario_logado' => $usuario_logado_id,
        'eh_admin' => $eh_admin
    ]);
} catch (Exception $e) {
    error_log("Erro ao buscar avaliações: " . $e->getMessage());
    echo json_encode(['erro' => 'Erro ao buscar avaliações: ' . $e->getMessage()]);
}
