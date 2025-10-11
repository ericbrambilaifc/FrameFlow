<?php
require_once "ConexaoBD.php";

class SeguidorDAO
{
    // Seguir um usuário
    public static function seguir($seguidor_id, $seguindo_id)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "INSERT INTO seguidores (seguidor_id, seguindo_id) VALUES (:seguidor_id, :seguindo_id)";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':seguidor_id', $seguidor_id);
        $stmt->bindParam(':seguindo_id', $seguindo_id);
        
        return $stmt->execute();
    }

    // Deixar de seguir
    public static function deixarDeSeguir($seguidor_id, $seguindo_id)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "DELETE FROM seguidores WHERE seguidor_id = :seguidor_id AND seguindo_id = :seguindo_id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':seguidor_id', $seguidor_id);
        $stmt->bindParam(':seguindo_id', $seguindo_id);
        
        return $stmt->execute();
    }

    // Verificar se está seguindo
    public static function estaSeguindo($seguidor_id, $seguindo_id)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT EXISTS(SELECT 1 FROM seguidores WHERE seguidor_id = :seguidor_id AND seguindo_id = :seguindo_id) as esta_seguindo";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':seguidor_id', $seguidor_id);
        $stmt->bindParam(':seguindo_id', $seguindo_id);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return (bool) $resultado['esta_seguindo'];
    }

    // Listar seguidores de um usuário
    public static function listarSeguidores($usuario_id)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT u.id, u.nome_completo, u.email
                FROM seguidores s
                INNER JOIN usuarios u ON s.seguidor_id = u.id
                WHERE s.seguindo_id = :usuario_id";
        
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Listar quem o usuário está seguindo
    public static function listarSeguindo($usuario_id)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT u.id, u.nome_completo, u.email
                FROM seguidores s
                INNER JOIN usuarios u ON s.seguindo_id = u.id
                WHERE s.seguidor_id = :usuario_id";
        
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>