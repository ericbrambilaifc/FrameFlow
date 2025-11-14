<?php
require_once "ConexaoBD.php";
require_once "src/Util.php";

class FilmeDAO
{
    public static function inserir($dados)
    {
        $conexao = ConexaoBD::conectar();

        $titulo = $dados['titulo'];
        $diretor = $dados['diretor'];
        $elenco = $dados['elenco'];
        $ano = $dados['ano'];
        $oscar = $dados['oscar'];
        $imagem = Util::salvarArquivo();
        $idcategoria = $dados['idcategoria'];
        $idclassificacao = $dados['idclassificacao'];
        $detalhes = $dados['detalhes'];
      
        $sql = "INSERT INTO Filme (titulo, diretor, elenco, ano, oscar, imagem, idcategoria, idclassificacao, detalhes) 
                VALUES (:titulo, :diretor, :elenco, :ano, :oscar, :imagem, :idcategoria, :idclassificacao, :detalhes )";

        $stmt = $conexao->prepare($sql);

        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':diretor', $diretor);
        $stmt->bindParam(':elenco', $elenco);
        $stmt->bindParam(':ano', $ano);
        $stmt->bindParam(':oscar', $oscar);
        $stmt->bindParam(':imagem', $imagem);
        $stmt->bindParam(':idcategoria', $idcategoria);
        $stmt->bindParam(':idclassificacao', $idclassificacao);
        $stmt->bindParam(':detalhes', $detalhes);

        $stmt->execute();
    }

    public static function listar()
    {
        $conexao = ConexaoBD::conectar();
        $sql = "SELECT filme.*, categoria.nomecategoria, classificacao.nomeclassificacao
                FROM filme
                JOIN categoria ON filme.idcategoria = categoria.idcategoria
                JOIN classificacao ON filme.idclassificacao = classificacao.idclassificacao";
        $stmt = $conexao->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarPorClassificacao($idClassificacao)
    {
        $conexao = ConexaoBD::conectar();
        $sql = "SELECT filme.*, categoria.nomecategoria, classificacao.nomeclassificacao
                FROM filme
                JOIN categoria ON filme.idcategoria = categoria.idcategoria
                JOIN classificacao ON filme.idclassificacao = classificacao.idclassificacao
                WHERE filme.idclassificacao = :idclassificacao";
        $stmt = $conexao->prepare($sql);
        $stmt->bindValue(':idclassificacao', $idClassificacao, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarPorCategoria($idCategoria)
    {
        $conexao = ConexaoBD::conectar();
        $sql = "SELECT filme.*, categoria.nomecategoria, classificacao.nomeclassificacao
                FROM filme
                JOIN categoria ON filme.idcategoria = categoria.idcategoria
                JOIN classificacao ON filme.idclassificacao = classificacao.idclassificacao
                WHERE filme.idcategoria = :idcategoria";
        $stmt = $conexao->prepare($sql);
        $stmt->bindValue(':idcategoria', $idCategoria, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
