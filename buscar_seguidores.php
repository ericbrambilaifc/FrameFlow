<?php
session_start();
header('Content-Type: application/json');

// Verifica se está logado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['erro' => 'Usuário não autenticado']);
    exit;
}

require_once('src/SeguidorDAO.php');

// Pega os parâmetros
$usuario_id = isset($_GET['usuario_id']) ? (int)$_GET['usuario_id'] : null;
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'seguidores';

if (!$usuario_id) {
    echo json_encode(['erro' => 'ID do usuário não fornecido']);
    exit;
}

try {
    if ($tipo === 'seguidores') {
        // Busca os seguidores
        $lista = SeguidorDAO::listarSeguidores($usuario_id);
    } else {
        // Busca quem o usuário está seguindo
        $lista = SeguidorDAO::listarSeguindo($usuario_id);
    }
    
    echo json_encode([
        'sucesso' => true,
        'lista' => $lista,
        'total' => count($lista)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'erro' => 'Erro ao buscar dados: ' . $e->getMessage()
    ]);
}