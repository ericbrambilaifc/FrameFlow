<?php
require_once "ConexaoBD.php";

class GeneroDAO
{
    
    public static function listar()
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT * FROM generos ORDER BY nome";
        $stmt = $conexao->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId($id)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT * FROM generos WHERE id = :id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>  