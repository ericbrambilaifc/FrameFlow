<?php
session_start();
header('Content-Type: application/json');

require_once('src/SeguidorDAO.php');

$usuario_id = isset($_GET['usuario_id']) ? (int)$_GET['usuario_id'] : null;

if (!$usuario_id) {
    echo json_encode(['erro' => 'ID do usuário não fornecido']);
    exit;
}

try {
    $total_seguidores = SeguidorDAO::contarSeguidores($usuario_id);
    $total_seguindo = SeguidorDAO::contarSeguindo($usuario_id);
    
    echo json_encode([
        'sucesso' => true,
        'total_seguidores' => $total_seguidores,
        'total_seguindo' => $total_seguindo
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'erro' => 'Erro ao buscar contadores: ' . $e->getMessage()
    ]);
}