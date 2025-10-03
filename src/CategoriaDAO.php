<?php
require_once "ConexaoBD.php";

class CategoriaDAO
{

    public static function inserir($dados)
    {
        $conexao = ConexaoBD::conectar();

        $nomecategoria = $dados['nomecategoria'];

        $sql = "INSERT INTO categoria (nomecategoria) VALUES (:nomecategoria)";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':nomecategoria', $nomecategoria);
        $stmt->execute();
    }

    public static function listar()
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT idcategoria, nomecategoria FROM categoria";
        $stmt = $conexao->prepare($sql);
        $stmt->execute();
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $categorias;
    }
}
?>