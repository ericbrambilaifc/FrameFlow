<?php
session_start();
require_once 'src/UsuarioDAO.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $nome_completo = trim($_POST['nome_completo']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if (empty($email) || empty($nome_completo) || empty($senha) || empty($confirmar_senha)) {
        $_SESSION['erro'] = "Todos os campos são obrigatórios!";
        header("Location: explorar.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['erro'] = "E-mail inválido!";
        header("Location: explorar.php");
        exit();
    }

    if ($senha !== $confirmar_senha) {
        $_SESSION['erro'] = "As senhas não coincidem!";
        header("Location: explorar.php");
        exit();
    }

    if (strlen($senha) < 6) {
        $_SESSION['erro'] = "A senha deve ter no mínimo 6 caracteres!";
        header("Location: explorar.php");
        exit();
    }

    if (UsuarioDAO::buscarPorEmail($email)) {
        $_SESSION['erro'] = "Este e-mail já está cadastrado!";
        header("Location: explorar.php");
        exit();
    }

    $dados = [
        'nome_completo' => $nome_completo,
        'email' => $email,
        'senha' => password_hash($senha, PASSWORD_DEFAULT)
    ];

    try {
        if (UsuarioDAO::inserir($dados)) {
            $_SESSION['sucesso'] = "Conta criada com sucesso! Faça login para continuar.";
            header("Location: explorar.php");
            exit();
        } else {
            $_SESSION['erro'] = "Erro ao criar conta. Tente novamente.";
            header("Location: explorar.php");
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['erro'] = "Erro ao criar conta: " . $e->getMessage();
        header("Location: explorar.php");
        exit();
    }
} else {
    
    header("Location: explorar.php");
    exit();
}
