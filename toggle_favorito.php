<?php
session_start();
require_once('src/FavoritoDAO.php');

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Você precisa estar logado!']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serie_id = isset($_POST['serie_id']) ? intval($_POST['serie_id']) : 0;
    $usuario_id = $_SESSION['usuario_id'];
    
    if ($serie_id <= 0) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Série inválida!']);
        exit;
    }
    
    $isFavorito = FavoritoDAO::isFavorito($usuario_id, $serie_id);
    
    if ($isFavorito) {
        
        $resultado = FavoritoDAO::remover($usuario_id, $serie_id);
        $mensagem = 'Removido dos favoritos!';
        $acao = 'removido';
    } else {
        
        $resultado = FavoritoDAO::adicionar($usuario_id, $serie_id);
        $mensagem = 'Adicionado aos favoritos!';
        $acao = 'adicionado';
    }
    
    if ($resultado) {
        echo json_encode([
            'sucesso' => true, 
            'mensagem' => $mensagem,
            'acao' => $acao,
            'isFavorito' => !$isFavorito
        ]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao processar favorito!']);
    }
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido!']);
}
?>
