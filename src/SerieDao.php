<?php
require_once "ConexaoBD.php";

class SerieDao
{
    
    public static function inserir($dados)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "INSERT INTO series (titulo, imagem_url, classificacao_id, genero_id) 
                VALUES (:titulo, :imagem_url, :classificacao_id, :genero_id)";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':titulo', $dados['titulo']);
        $stmt->bindParam(':imagem_url', $dados['imagem_url']);
        $stmt->bindParam(':classificacao_id', $dados['classificacao_id']);
        $stmt->bindParam(':genero_id', $dados['genero_id']);

        return $stmt->execute();
    }

    public static function listar()
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT s.id, s.titulo, s.imagem_url, s.classificacao_id, s.genero_id,
                c.nome as classificacao_nome,
                g.nome as genero_nome,
                COUNT(a.id) as total_avaliacoes,
                COALESCE(AVG(a.nota), 0) as media_nota
                FROM series s
                LEFT JOIN avaliacoes a ON s.id = a.serie_id
                LEFT JOIN classificacoes c ON s.classificacao_id = c.id
                LEFT JOIN generos g ON s.genero_id = g.id
                GROUP BY s.id, s.titulo, s.imagem_url, s.classificacao_id, s.genero_id, c.nome, g.nome
                ORDER BY s.titulo";

        $stmt = $conexao->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId($id)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT s.id, s.titulo, s.imagem_url, s.classificacao_id, s.genero_id,
                c.nome as classificacao_nome,
                g.nome as genero_nome,
                COUNT(a.id) as total_avaliacoes,
                COALESCE(AVG(a.nota), 0) as media_nota
                FROM series s
                LEFT JOIN avaliacoes a ON s.id = a.serie_id
                LEFT JOIN classificacoes c ON s.classificacao_id = c.id
                LEFT JOIN generos g ON s.genero_id = g.id
                WHERE s.id = :id
                GROUP BY s.id, s.titulo, s.imagem_url, s.classificacao_id, s.genero_id, c.nome, g.nome";

        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function buscar($termo, $genero_id = null, $classificacao_id = null)
    {
        $conexao = ConexaoBD::conectar();

        $termo = "%{$termo}%";

        $sql = "SELECT s.id, s.titulo, s.imagem_url, s.classificacao_id, s.genero_id,
                c.nome as classificacao_nome,
                g.nome as genero_nome,
                COUNT(a.id) as total_avaliacoes,
                COALESCE(AVG(a.nota), 0) as media_nota
                FROM series s
                LEFT JOIN avaliacoes a ON s.id = a.serie_id
                LEFT JOIN classificacoes c ON s.classificacao_id = c.id
                LEFT JOIN generos g ON s.genero_id = g.id
                WHERE s.titulo LIKE :termo";

        if ($genero_id !== null && $genero_id != '') {
            $sql .= " AND s.genero_id = :genero_id";
        }

        if ($classificacao_id !== null && $classificacao_id != '') {
            $sql .= " AND s.classificacao_id = :classificacao_id";
        }

        $sql .= " GROUP BY s.id, s.titulo, s.imagem_url, s.classificacao_id, s.genero_id, c.nome, g.nome
                  ORDER BY s.titulo
                  LIMIT 20";

        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':termo', $termo);

        if ($genero_id !== null && $genero_id != '') {
            $stmt->bindParam(':genero_id', $genero_id);
        }

        if ($classificacao_id !== null && $classificacao_id != '') {
            $stmt->bindParam(':classificacao_id', $classificacao_id);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
