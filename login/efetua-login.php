<?php
    session_start();
    require "src/UsuarioDAO.php";

    if (UsuarioDAO::validarUsuario($_POST)){    
        $_SESSION['email'] = $_POST ['email'];
        header("Location:home.php");
    }else{
        $_SESSION['msg'] = "Usuário ou senha inválido.";
        header("Location:login.php");
    }
?>