
<div class="mb-3 col-4">
    <button type="submit" class="btn btn-primary btn-lg my-4">Buscar</button>
</div>
<?php
require_once "src/UsuarioDao.php";

if(!isset($_GET['nome'])){
    $_GET['nome'] = '';
}
$usuarios = UsuarioDAO::buscarusuarios($_SESSION['idusuario'], $_GET['nome']);
foreach ($usuarios as $usuario) 
?>

<p class="m-3"><?=$usuario['nome']?>
<a href="seguir.php?idseguido<?=$usuario['usuario']?>"btn btn-primary mx-3>
    Adicionar
</a>
</p>
