<?php
require_once "ConexaoBD.php";
require_once "src/Util.php";

class SerieDAO
{
    public static function inserir($dados)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "INSERT INTO serie (titulo, diretor, elenco, ano, temporadas, episodios, imagem, idcategoria, idclassificacao, detalhes)
                VALUES (:titulo, :diretor, :elenco, :ano, :temporadas, :episodios, :imagem, :idcategoria, :idclassificacao, :detalhes)";

        $stmt = $conexao->prepare($sql);
        $stmt->execute([
            ':titulo' => $dados['titulo'],
            ':diretor' => $dados['diretor'] ?? null,
            ':elenco' => $dados['elenco'] ?? null,
            ':ano' => $dados['ano'] ?? null,
            ':temporadas' => $dados['temporadas'] ?? null,
            ':episodios' => $dados['episodios'] ?? null,
            ':imagem' => Util::salvarArquivo(),
            ':idcategoria' => $dados['idcategoria'] ?? null,
            ':idclassificacao' => $dados['idclassificacao'] ?? null,
            ':detalhes' => $dados['detalhes'] ?? null,
        ]);
    }

    public static function listar()
    {
        $conexao = ConexaoBD::conectar();
        $sql = "SELECT serie.*, categoria.nomecategoria, classificacao.nomeclassificacao
                FROM serie
                JOIN categoria ON serie.idcategoria = categoria.idcategoria
                JOIN classificacao ON serie.idclassificacao = classificacao.idclassificacao";
                
        return $conexao->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarPorClassificacao($idClassificacao)
    {
        $conexao = ConexaoBD::conectar();
        $sql = "SELECT serie.*, categoria.nomecategoria, classificacao.nomeclassificacao
                FROM serie
                JOIN categoria ON serie.idcategoria = categoria.idcategoria
                JOIN classificacao ON serie.idclassificacao = classificacao.idclassificacao
                WHERE serie.idclassificacao = ?";
                
        $stmt = $conexao->prepare($sql);
        $stmt->execute([$idClassificacao]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarPorCategoria($idCategoria)
    {
        $conexao = ConexaoBD::conectar();
        $sql = "SELECT serie.*, categoria.nomecategoria, classificacao.nomeclassificacao
                FROM serie
                JOIN categoria ON serie.idcategoria = categoria.idcategoria
                JOIN classificacao ON serie.idclassificacao = classificacao.idclassificacao
                WHERE serie.idcategoria = ?";
                
        $stmt = $conexao->prepare($sql);
        $stmt->execute([$idCategoria]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}