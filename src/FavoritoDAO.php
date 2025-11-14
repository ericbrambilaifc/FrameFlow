<?php
require_once('ConexaoBD.php');

class FavoritoDAO {
    
    public static function adicionar($usuario_id, $serie_id) {
        try {
            $conexao = ConexaoBD::conectar();
            $sql = "INSERT INTO favoritos (usuario_id, serie_id) VALUES (:usuario_id, :serie_id)";
            $stmt = $conexao->prepare($sql);
            $stmt->bindValue(':usuario_id', $usuario_id);
            $stmt->bindValue(':serie_id', $serie_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao adicionar favorito: " . $e->getMessage());
            return false;
        }
    }
    
    public static function remover($usuario_id, $serie_id) {
        try {
            $conexao = ConexaoBD::conectar();
            $sql = "DELETE FROM favoritos WHERE usuario_id = :usuario_id AND serie_id = :serie_id";
            $stmt = $conexao->prepare($sql);
            $stmt->bindValue(':usuario_id', $usuario_id);
            $stmt->bindValue(':serie_id', $serie_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao remover favorito: " . $e->getMessage());
            return false;
        }
    }
    
    public static function isFavorito($usuario_id, $serie_id) {
        try {
            $conexao = ConexaoBD::conectar();
            $sql = "SELECT COUNT(*) as total FROM favoritos WHERE usuario_id = :usuario_id AND serie_id = :serie_id";
            $stmt = $conexao->prepare($sql);
            $stmt->bindValue(':usuario_id', $usuario_id);
            $stmt->bindValue(':serie_id', $serie_id);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Erro ao verificar favorito: " . $e->getMessage());
            return false;
        }
    }
    
    public static function listarPorUsuario($usuario_id) {
        try {
            $conexao = ConexaoBD::conectar();
            $sql = "SELECT s.*, f.data_favorito 
                    FROM favoritos f
                    INNER JOIN series s ON f.serie_id = s.id
                    WHERE f.usuario_id = :usuario_id
                    ORDER BY f.data_favorito DESC";
            $stmt = $conexao->prepare($sql);
            $stmt->bindValue(':usuario_id', $usuario_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar favoritos: " . $e->getMessage());
            return [];
        }
    }
    
    public static function contarPorUsuario($usuario_id) {
        try {
            $conexao = ConexaoBD::conectar();
            $sql = "SELECT COUNT(*) as total FROM favoritos WHERE usuario_id = :usuario_id";
            $stmt = $conexao->prepare($sql);
            $stmt->bindValue(':usuario_id', $usuario_id);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'];
        } catch (PDOException $e) {
            error_log("Erro ao contar favoritos: " . $e->getMessage());
            return 0;
        }
    }
}
?>
