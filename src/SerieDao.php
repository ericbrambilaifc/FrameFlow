<?php
require_once "ConexaoBD.php";

class SerieDao
{
    // Cadastrar nova série (apenas titulo e imagem_url)
    public static function inserir($dados)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "INSERT INTO series (titulo, imagem_url) VALUES (:titulo, :imagem_url)";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':titulo', $dados['titulo']);
        $stmt->bindParam(':imagem_url', $dados['imagem_url']);

        return $stmt->execute();
    }

    // Listar todas as séries
    public static function listar()
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT s.id, s.titulo, s.imagem_url,
                COUNT(a.id) as total_avaliacoes,
                COALESCE(AVG(a.nota), 0) as media_nota
                FROM series s
                LEFT JOIN avaliacoes a ON s.id = a.serie_id
                GROUP BY s.id, s.titulo, s.imagem_url
                ORDER BY s.titulo";

        $stmt = $conexao->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar série por ID
    public static function buscarPorId($id)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT s.id, s.titulo, s.imagem_url,
                COUNT(a.id) as total_avaliacoes,
                COALESCE(AVG(a.nota), 0) as media_nota
                FROM series s
                LEFT JOIN avaliacoes a ON s.id = a.serie_id
                WHERE s.id = :id
                GROUP BY s.id, s.titulo, s.imagem_url";

        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Buscar séries por título
    public static function buscar($termo)
    {
        $conexao = ConexaoBD::conectar();

        $termo = "%{$termo}%";
        $sql = "SELECT s.id, s.titulo, s.imagem_url,
                COUNT(a.id) as total_avaliacoes,
                COALESCE(AVG(a.nota), 0) as media_nota
                FROM series s
                LEFT JOIN avaliacoes a ON s.id = a.serie_id
                WHERE s.titulo LIKE :termo
                GROUP BY s.id, s.titulo, s.imagem_url
                ORDER BY s.titulo
                LIMIT 20";

        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':termo', $termo);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
