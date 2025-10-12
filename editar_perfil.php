<?php
session_start();
require_once('src/UsuarioDAO.php');
require_once('src/ConexaoBD.php'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario_id'])) {
    $nome_completo = trim($_POST['nome_completo']);
    $usuario_id = $_SESSION['usuario_id'];

    if (empty($nome_completo)) {
        $_SESSION['erro'] = 'O nome não pode estar vazio.';
        header('Location: perfil.php?id=' . $usuario_id);
        exit;
    }

    // Atualizar no banco
    $conexao = ConexaoBD::conectar();
    $sql = "UPDATE usuarios SET nome_completo = :nome WHERE id = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':nome', $nome_completo);
    $stmt->bindParam(':id', $usuario_id);

    if ($stmt->execute()) {
        $_SESSION['usuario_nome'] = $nome_completo;
        $_SESSION['sucesso'] = 'Perfil atualizado com sucesso!';
    } else {
        $_SESSION['erro'] = 'Erro ao atualizar perfil.';
    }

    header('Location: perfil.php?id=' . $usuario_id);
    exit;
}

header('Location: explorar.php');
exit;
