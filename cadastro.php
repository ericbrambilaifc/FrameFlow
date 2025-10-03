<?php
require_once 'src/FilmeDAO.php';
require_once 'src/SerieDAO.php';

if ($_POST) {
    if ($_POST['tipo'] == 'filme') {
        FilmeDAO::inserir($_POST);
        echo "Filme cadastrado!";
    } else {
        SerieDAO::inserir($_POST);
        echo "Série cadastrada!";
    }
    
    echo "<br><a href='newDesing.php'>Ir para seção de filmes e séries</a>";
}
?>