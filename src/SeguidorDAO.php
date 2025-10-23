<?php
require_once('ConexaoBD.php');

class SeguidorDAO
{

    /**
     * Verifica se um usuário está seguindo outro
     */
    public static function estaSeguindo($seguidor_id, $seguindo_id)
    {
        try {
            $conexao = ConexaoBD::conectar();

            $sql = "SELECT COUNT(*) as total 
                    FROM seguidores 
                    WHERE seguidor_id = :seguidor_id 
                    AND seguindo_id = :seguindo_id";

            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(':seguidor_id', $seguidor_id, PDO::PARAM_INT);
            $stmt->bindParam(':seguindo_id', $seguindo_id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Erro ao verificar seguidor: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Seguir um usuário
     */
    public static function seguir($seguidor_id, $seguindo_id)
    {
        try {
            // Verifica se já está seguindo
            if (self::estaSeguindo($seguidor_id, $seguindo_id)) {
                return false;
            }

            $conexao = ConexaoBD::conectar();

            $sql = "INSERT INTO seguidores (seguidor_id, seguindo_id) 
                    VALUES (:seguidor_id, :seguindo_id)";

            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(':seguidor_id', $seguidor_id, PDO::PARAM_INT);
            $stmt->bindParam(':seguindo_id', $seguindo_id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao seguir usuário: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deixar de seguir um usuário
     */
    public static function deixarDeSeguir($seguidor_id, $seguindo_id)
    {
        try {
            $conexao = ConexaoBD::conectar();

            $sql = "DELETE FROM seguidores 
                    WHERE seguidor_id = :seguidor_id 
                    AND seguindo_id = :seguindo_id";

            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(':seguidor_id', $seguidor_id, PDO::PARAM_INT);
            $stmt->bindParam(':seguindo_id', $seguindo_id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao deixar de seguir usuário: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Listar seguidores de um usuário
     */
    /**
     * Listar seguidores de um usuário (COM FOTO DE PERFIL)
     */
    public static function listarSeguidores($usuario_id)
    {
        try {
            $conexao = ConexaoBD::conectar();

            $sql = "SELECT u.id, u.nome_completo, u.email, u.foto_perfil, s.data_seguimento
                    FROM seguidores s
                    INNER JOIN usuarios u ON s.seguidor_id = u.id
                    WHERE s.seguindo_id = :usuario_id
                    ORDER BY s.data_seguimento DESC";

            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar seguidores: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Listar usuários que o usuário está seguindo (COM FOTO DE PERFIL)
     */
    public static function listarSeguindo($usuario_id)
    {
        try {
            $conexao = ConexaoBD::conectar();

            $sql = "SELECT u.id, u.nome_completo, u.email, u.foto_perfil, s.data_seguimento
                    FROM seguidores s
                    INNER JOIN usuarios u ON s.seguindo_id = u.id
                    WHERE s.seguidor_id = :usuario_id
                    ORDER BY s.data_seguimento DESC";

            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar seguindo: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Contar total de seguidores
     */
    public static function contarSeguidores($usuario_id)
    {
        try {
            $conexao = ConexaoBD::conectar();

            $sql = "SELECT COUNT(*) as total 
                    FROM seguidores 
                    WHERE seguindo_id = :usuario_id";

            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return $resultado['total'];
        } catch (PDOException $e) {
            error_log("Erro ao contar seguidores: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Contar total de usuários que está seguindo
     */
    public static function contarSeguindo($usuario_id)
    {
        try {
            $conexao = ConexaoBD::conectar();

            $sql = "SELECT COUNT(*) as total 
                    FROM seguidores 
                    WHERE seguidor_id = :usuario_id";

            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return $resultado['total'];
        } catch (PDOException $e) {
            error_log("Erro ao contar seguindo: " . $e->getMessage());
            return 0;
        }
    }
}
