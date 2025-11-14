<?php
session_start();
require_once('src/UsuarioDAO.php');
require_once('src/ConexaoBD.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario_id'])) {
    $senha_atual = $_POST['senha_atual'];
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $usuario_id = $_SESSION['usuario_id'];
    
    if ($nova_senha !== $confirmar_senha) {
        $_SESSION['erro'] = 'As senhas não coincidem.';
        header('Location: perfil.php?id=' . $usuario_id);
        exit;
    }
    
    if (strlen($nova_senha) < 6) {
        $_SESSION['erro'] = 'A senha deve ter no mínimo 6 caracteres.';
        header('Location: perfil.php?id=' . $usuario_id);
        exit;
    }
    
    $usuario = UsuarioDAO::buscarPorEmail($_SESSION['usuario_email']);
    
    if (!password_verify($senha_atual, $usuario['senha'])) {
        $_SESSION['erro'] = 'Senha atual incorreta.';
        header('Location: perfil.php?id=' . $usuario_id);
        exit;
    }
    
    $senhaHash = password_hash($nova_senha, PASSWORD_DEFAULT);
    $conexao = ConexaoBD::conectar();
    $sql = "UPDATE usuarios SET senha = :senha WHERE id = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':senha', $senhaHash);
    $stmt->bindParam(':id', $usuario_id);
    
    if ($stmt->execute()) {
        $_SESSION['sucesso'] = 'Senha alterada com sucesso!';
    } else {
        $_SESSION['erro'] = 'Erro ao alterar senha.';
    }
    
    header('Location: perfil.php?id=' . $usuario_id);
    exit;
}

header('Location: explorar.php');
exit;
?>