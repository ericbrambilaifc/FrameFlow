<?php
session_start();
require_once('src/SeguidorDAO.php');

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Verifica se está logado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Usuário não autenticado'
    ]);
    exit;
}

// Pega os parâmetros
$usuario_id = isset($_GET['usuario_id']) ? (int)$_GET['usuario_id'] : null;
$tipo = isset($_GET['tipo']) ? trim($_GET['tipo']) : 'seguidores';
$usuario_logado_id = $_SESSION['usuario_id'];

// Validações
if (!$usuario_id || $usuario_id <= 0) {
    echo json_encode([
        'sucesso' => false,
        'erro' => 'ID do usuário não fornecido ou inválido'
    ]);
    exit;
}

if (!in_array($tipo, ['seguidores', 'seguindo'])) {
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Tipo inválido. Use "seguidores" ou "seguindo".'
    ]);
    exit;
}

try {
    if ($tipo === 'seguidores') {
        // Busca os seguidores do usuário
        $lista = SeguidorDAO::listarSeguidores($usuario_id);
    } else {
        // Busca quem o usuário está seguindo
        $lista = SeguidorDAO::listarSeguindo($usuario_id);
    }

    // Processa a lista para adicionar informação de "está seguindo"
    $listaProcessada = [];

    foreach ($lista as $usuario) {
        // Verifica se o usuário logado está seguindo este usuário
        $estaSeguindo = SeguidorDAO::estaSeguindo($usuario_logado_id, $usuario['id']);

        $listaProcessada[] = [
            'id' => (int)$usuario['id'],
            'nome_completo' => $usuario['nome_completo'],
            'email' => $usuario['email'] ?? null,
            'foto_perfil' => $usuario['foto_perfil'] ?? null,
            'data_seguimento' => $usuario['data_seguimento'] ?? null,
            'esta_seguindo' => $estaSeguindo,
            'is_admin' => isset($usuario['is_admin']) ? (bool)$usuario['is_admin'] : false
        ];
    }

    echo json_encode([
        'sucesso' => true,
        'lista' => $listaProcessada,
        'total' => count($listaProcessada),
        'tipo' => $tipo
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    error_log("Erro ao buscar seguidores: " . $e->getMessage());

    echo json_encode([
        'sucesso' => false,
        'erro' => 'Erro ao buscar dados. Tente novamente.',
        'erro_debug' => $e->getMessage() // Remova em produção
    ]);
}
