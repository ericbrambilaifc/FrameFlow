<?php
session_start();
header('Content-Type: application/json');

require_once('src/UsuarioDAO.php');

// Recebe o termo de busca
$termo = isset($_GET['termo']) ? trim($_GET['termo']) : '';

// Se o termo estiver vazio, retorna array vazio
if (empty($termo) || strlen($termo) < 2) {
    echo json_encode([]);
    exit;
}

try {
    // Busca usuários
    $usuarios = UsuarioDAO::buscarUsuarios($termo);
    
    // Formata os resultados
    $resultados = array_map(function($usuario) {
        // Pega a primeira letra do nome para o avatar
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