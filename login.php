<?php
session_start();
require_once "src/UsuarioDAO.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    // Validações
    if (empty($email) || empty($senha)) {
        $_SESSION['erro'] = "E-mail e senha são obrigatórios!";
        header("Location: explorar.php");
        exit();
    }

    // Tentar fazer login
    $usuario = UsuarioDAO::login($email, $senha);

    if ($usuario) {
        // Login bem-sucedido
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome_completo'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['sucesso'] = "Bem-vindo(a), " . $usuario['nome_completo'] . "!";
        header("Location: explorar.php");
        exit();
    } else {
        // Login falhou
        $_SESSION['erro'] = "E-mail ou senha incorretos!";
        header("Location: explorar.php");
        exit();
    }
} else {
    // Acesso direto ao arquivo
    header("Location: explorar.php");
    exit();
}
