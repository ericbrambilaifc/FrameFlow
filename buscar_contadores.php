<?php
session_start();
require_once('src/SeguidorDAO.php');

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

$usuario_id = isset($_GET['usuario_id']) ? (int)$_GET['usuario_id'] : null;

if (!$usuario_id || $usuario_id <= 0) {
    echo json_encode([
        'sucesso' => false,
        'erro' => 'ID do usuário não fornecido ou inválido'
    ]);
    exit;
}

try {
    
    $total_seguidores = SeguidorDAO::contarSeguidores($usuario_id);
    $total_seguindo = SeguidorDAO::contarSeguindo($usuario_id);

    echo json_encode([
        'sucesso' => true,
        'total_seguidores' => (int)$total_seguidores,
        'total_seguindo' => (int)$total_seguindo,
        'usuario_id' => $usuario_id
    ]);
} catch (Exception $e) {
    error_log("Erro ao buscar contadores: " . $e->getMessage());

    echo json_encode([
        'sucesso' => false,
        'erro' => 'Erro ao buscar contadores. Tente novamente.'
    ]);
}
