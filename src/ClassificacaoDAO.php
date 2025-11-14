<?php
require_once "ConexaoBD.php";

class ClassificacaoDAO
{
    
    public static function listar()
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT * FROM classificacoes ORDER BY id";
        $stmt = $conexao->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId($id)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT * FROM classificacoes WHERE id = :id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>  