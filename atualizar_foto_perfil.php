<?php
session_start();
require_once 'src/UsuarioDAO.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto_perfil'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $arquivo = $_FILES['foto_perfil'];

    if ($arquivo['error'] === 0) {
        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
        $tamanhoMaximo = 5 * 1024 * 1024; 

        if (!in_array($extensao, $extensoesPermitidas)) {
            $_SESSION['erro'] = 'Formato de imagem não permitido. Use JPG, JPEG, PNG ou GIF.';
            header('Location: perfil.php?id=' . $usuario_id);
            exit;
        }

        if ($arquivo['size'] > $tamanhoMaximo) {
            $_SESSION['erro'] = 'A imagem deve ter no máximo 5MB.';
            header('Location: perfil.php?id=' . $usuario_id);
            exit;
        }

        $pastaUploads = 'uploads/perfil/';
        if (!file_exists($pastaUploads)) {
            mkdir($pastaUploads, 0777, true);
        }

        $fotoAntiga = UsuarioDAO::obterFotoPerfil($usuario_id);
        if ($fotoAntiga && file_exists($pastaUploads . $fotoAntiga)) {
            unlink($pastaUploads . $fotoAntiga);
        }

        $nomeFoto = 'perfil_' . $usuario_id . '_' . uniqid() . '.' . $extensao;
        $caminhoCompleto = $pastaUploads . $nomeFoto;

        if (move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
            
            if (UsuarioDAO::atualizarFotoPerfil($usuario_id, $nomeFoto)) {
                $_SESSION['sucesso'] = 'Foto de perfil atualizada com sucesso!';
            } else {
                $_SESSION['erro'] = 'Erro ao salvar no banco de dados.';
            }
        } else {
            $_SESSION['erro'] = 'Erro ao fazer upload da imagem.';
        }
    } else {
        $_SESSION['erro'] = 'Erro no upload: ' . $arquivo['error'];
    }
}

header('Location: perfil.php?id=' . $usuario_id);
exit;
