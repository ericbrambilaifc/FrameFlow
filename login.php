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
        $_SESSION['is_admin'] = $usuario['is_admin']; // ✅ ADICIONE ESTA LINHA

        $_SESSION['sucesso'] = "Você acessou sua conta com sucesso. Aproveite todos os recursos do nosso sistema.";
        header("Location: explorar.php");
        exit();
    } else {
        // Login falhou
        $_SESSION['erro'] = "Não conseguimos conectar sua conta. Verifique seus dados e tente novamente.";
        header("Location: explorar.php");
        exit();
    }
} else {
    // Acesso direto ao arquivo
    header("Location: explorar.php");
    exit();
}
