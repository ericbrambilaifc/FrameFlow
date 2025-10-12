<?php
session_start();
require_once('src/SerieDAO.php');

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['erro'] = "Você precisa estar logado para cadastrar séries!";
    header("Location: explorar.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $imagem_url = trim($_POST['imagem_url']);
    $genero_id = $_POST['genero_id'];
    $classificacao_id = $_POST['classificacao_id'];

    // Validações
    if (empty($titulo) || empty($imagem_url) || empty($genero_id) || empty($classificacao_id)) {
        $_SESSION['erro'] = "Todos os campos são obrigatórios!";
        header("Location: cadastrar_serie.php");
        exit();
    }

    // Validar URL
    if (!filter_var($imagem_url, FILTER_VALIDATE_URL)) {
        $_SESSION['erro'] = "URL da imagem inválida!";
        header("Location: cadastrar_serie.php");
        exit();
    }

    // Preparar dados para inserir
    $dados = [
        'titulo' => $titulo,
        'imagem_url' => $imagem_url,
        'genero_id' => $genero_id,
        'classificacao_id' => $classificacao_id
    ];

    try {
        if (SerieDao::inserir($dados)) {
            $_SESSION['sucesso'] = "Série '$titulo' cadastrada com sucesso!";
            header("Location: explorar.php");
            exit();
        } else {
            $_SESSION['erro'] = "Erro ao cadastrar série. Tente novamente.";
            header("Location: cadastrar_serie.php");
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['erro'] = "Erro ao cadastrar série: " . $e->getMessage();
        header("Location: cadastrar_serie.php");
        exit();
    }
} else {
    header("Location: cadastrar_serie.php");
    exit();
}
?>