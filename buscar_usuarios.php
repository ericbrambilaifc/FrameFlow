<?php
session_start();
header('Content-Type: application/json');

require_once('src/UsuarioDAO.php');

$termo = isset($_GET['termo']) ? trim($_GET['termo']) : '';

if (empty($termo) || strlen($termo) < 2) {
    echo json_encode([]);
    exit;
}

try {
    
    $usuarios = UsuarioDAO::buscarUsuarios($termo);
    
    $resultados = array_map(function($usuario) {
        
        $iniciais = strtoupper(substr($usuario['nome_completo'], 0, 1));
        
        return [
            'id' => $usuario['id'],
            'nome_completo' => $usuario['nome_completo'],
            'email' => $usuario['email'],
            'foto_perfil' => $usuario['foto_perfil'],
            'iniciais' => $iniciais,
            'is_admin' => $usuario['is_admin']
        ];
    }, $usuarios);
    
    echo json_encode($resultados);
    
} catch (Exception $e) {
    echo json_encode(['erro' => 'Erro ao buscar usuários']);
}
?>