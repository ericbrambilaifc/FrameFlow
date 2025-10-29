<?php
session_start();
require_once('src/AvaliacaoDAO.php');

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['erro'] = 'Você precisa estar logado para avaliar uma série!';
    header('Location: explorar.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serie_id = isset($_POST['serie_id']) ? intval($_POST['serie_id']) : 0;
    $nota = isset($_POST['nota']) ? intval($_POST['nota']) : 0;
    $comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';
    $usuario_id = $_SESSION['usuario_id'];

    // Validações
    if ($serie_id <= 0) {
        $_SESSION['erro'] = 'Série inválida!';
        header('Location: explorar.php');
        exit;
    }

    if ($nota < 1 || $nota > 5) {
        $_SESSION['erro'] = 'Nota inválida! Escolha entre 1 e 5 estrelas.';
        header('Location: explorar.php');
        exit;
    }

    if (empty($comentario) || strlen($comentario) < 10) {
        $_SESSION['erro'] = 'O comentário deve ter no mínimo 10 caracteres!';
        header('Location: explorar.php');
        exit;
    }

    try {
        $avaliacaoExistente = AvaliacaoDAO::jaAvaliou($usuario_id, $serie_id);

        $dados = [
            'usuario_id' => $usuario_id,
            'serie_id' => $serie_id,
            'nota' => $nota,
            'comentario' => $comentario
        ];

        if ($avaliacaoExistente) {
            AvaliacaoDAO::atualizar($dados);
            $_SESSION['sucesso_avaliacao'] = 'Sua avaliação foi atualizada com sucesso!';
        } else {
            AvaliacaoDAO::inserir($dados);
            $_SESSION['sucesso_avaliacao'] = 'Avaliação foi publicada com sucesso!';
        }
    } catch (Exception $e) {
        $_SESSION['erro'] = 'Erro ao salvar avaliação: ' . $e->getMessage();
    }
}

header('Location: explorar.php');
exit;
