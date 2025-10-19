<?php
// buscar_avaliacoes.php
session_start();
require_once('src/AvaliacaoDAO.php');
require_once('src/UsuarioDAO.php');

header('Content-Type: application/json');

if (!isset($_GET['serie_id'])) {
    echo json_encode(['erro' => 'ID da série não fornecido']);
    exit;
}

$serie_id = intval($_GET['serie_id']);

try {
    // Buscar avaliações da série
    $avaliacoes = AvaliacaoDAO::listarPorSerie($serie_id);

    // Buscar informações dos usuários
    $avaliacoesCompletas = [];
    foreach ($avaliacoes as $avaliacao) {
        $usuario = UsuarioDAO::buscarPorId($avaliacao['usuario_id']);
        $avaliacoesCompletas[] = [
            'id' => $avaliacao['id'],
            'nota' => $avaliacao['nota'],
            'comentario' => $avaliacao['comentario'],
            'data_avaliacao' => $avaliacao['data_avaliacao'],
            'usuario_id' => $avaliacao['usuario_id'],
            'usuario_nome' => $usuario ? $usuario['nome_completo'] : 'Usuário',
            'foto_perfil' => $usuario ? $usuario['foto_perfil'] : null
        ];
    }

    echo json_encode(['avaliacoes' => $avaliacoesCompletas]);
} catch (Exception $e) {
    echo json_encode(['erro' => 'Erro ao buscar avaliações: ' . $e->getMessage()]);
}
