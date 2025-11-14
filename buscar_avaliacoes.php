<?php

session_start();
require_once('src/ConexaoBD.php');
require_once('src/AvaliacaoDAO.php');
require_once('src/UsuarioDAO.php');
require_once('src/VotoDAO.php');

header('Content-Type: application/json');

if (!isset($_GET['serie_id'])) {
    echo json_encode(['erro' => 'ID da série não fornecido']);
    exit;
}

$serie_id = intval($_GET['serie_id']);

$usuario_logado_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
$eh_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

try {
    
    $avaliacoes = AvaliacaoDAO::listarPorSerie($serie_id);

    $avaliacoesCompletas = [];

    foreach ($avaliacoes as $avaliacao) {
        $usuario = UsuarioDAO::buscarPorId($avaliacao['usuario_id']);
        $avaliacao_id = $avaliacao['id'];

        $totais = VotoDAO::contarVotos($avaliacao_id);

        $voto_usuario = null;
        if ($usuario_logado_id && !$eh_admin) {
            $voto_usuario = VotoDAO::buscarVotoUsuario($avaliacao_id, $usuario_logado_id);
        }

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
            'total_likes' => $totais['likes'],
            'total_dislikes' => $totais['dislikes'],
            'usuario_voto' => $voto_usuario, 
            'pode_curtir' => true 
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
