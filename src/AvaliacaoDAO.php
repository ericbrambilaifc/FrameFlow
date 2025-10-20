<?php
require_once "ConexaoBD.php";

class AvaliacaoDAO
{
    // Cadastrar nova avaliação
    public static function inserir($dados)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "INSERT INTO avaliacoes (usuario_id, serie_id, nota, comentario) 
                VALUES (:usuario_id, :serie_id, :nota, :comentario)";

        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':usuario_id', $dados['usuario_id']);
        $stmt->bindParam(':serie_id', $dados['serie_id']);
        $stmt->bindParam(':nota', $dados['nota']);
        $stmt->bindParam(':comentario', $dados['comentario']);

        return $stmt->execute();
    }

    // Atualizar avaliação existente
    public static function atualizar($dados)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "UPDATE avaliacoes 
                SET nota = :nota, comentario = :comentario
                WHERE usuario_id = :usuario_id AND serie_id = :serie_id";

        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':nota', $dados['nota']);
        $stmt->bindParam(':comentario', $dados['comentario']);
        $stmt->bindParam(':usuario_id', $dados['usuario_id']);
        $stmt->bindParam(':serie_id', $dados['serie_id']);

        return $stmt->execute();
    }

    // Verificar se usuário já avaliou a série
    public static function jaAvaliou($usuario_id, $serie_id)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT * FROM avaliacoes WHERE usuario_id = :usuario_id AND serie_id = :serie_id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':serie_id', $serie_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Listar avaliações de uma série
    public static function listarPorSerie($serie_id)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT a.*, u.nome_completo, u.email
                FROM avaliacoes a
                INNER JOIN usuarios u ON a.usuario_id = u.id
                WHERE a.serie_id = :serie_id
                ORDER BY a.id DESC";

        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':serie_id', $serie_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Listar avaliações de um usuário
    public static function listarPorUsuario($usuario_id)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT a.*, s.titulo, s.imagem_url
                FROM avaliacoes a
                INNER JOIN series s ON a.serie_id = s.id
                WHERE a.usuario_id = :usuario_id
                ORDER BY a.id DESC";

        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Feed de avaliações recentes
    public static function feedRecente($limite = 20)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT a.*, 
                u.nome_completo, u.email,
                s.titulo, s.imagem_url
                FROM avaliacoes a
                INNER JOIN usuarios u ON a.usuario_id = u.id
                INNER JOIN series s ON a.serie_id = s.id
                ORDER BY a.id DESC
                LIMIT :limite";

        $stmt = $conexao->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obter avaliação por ID
    public static function obterPorId($avaliacao_id)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT * FROM avaliacoes WHERE id = :id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $avaliacao_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Deletar avaliação por ID
    public static function deletarPorId($avaliacao_id)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "DELETE FROM avaliacoes WHERE id = :id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $avaliacao_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>